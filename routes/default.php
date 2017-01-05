<?php
/**
 * Created by Freisicht
 * User: Zoran
 * Date: 24.10.16
 * Time: 13:04
 */

use Slim\Http\Request;
use Slim\Http\Response;
use \Freisicht\InvoiceImporter\CustomerOptionQuery;
use \Freisicht\InvoiceImporter\CustomerOption;
use \Freisicht\InvoiceImporter\ProjectOption;
use \Freisicht\InvoiceImporter\ProjectOptionQuery;
use \Propel\Runtime\ActiveQuery\Criteria;

$app = SlimApp::getInstance();

$app->get('/projekttest', function (Request $request, Response $response, array $args) {
//    $response = $this->view->render($response, 'header.html');
    $miteApi = new MiteApiHandler();
    var_dump($miteApi->getProjects());
    var_dump($miteApi->getCustomers());
});

$app->get('/', function (Request $request, Response $response, array $args) {
    $response = $this->view->render($response, 'header.html');
    $response = $this->view->render($response, 'customer_options.html');

    return $this->view->render($response, 'footer.html');
});

$app->get('/manuellerimport', function (Request $request, Response $response, array $args) {
    $response = $this->view->render($response, 'header.html');
    $response = $this->view->render($response, 'manuel_importer.html');

    return $this->view->render($response, 'footer.html');
});

$app->post('/options/customers/sync/', function (Request $request, Response $response, array $args) {
    $customerOptions = $request->getParam("customerOptions");
    $projectOptions = $request->getParam("projectOptions");

    foreach ($customerOptions as $customerOption) {
        $c = CustomerOptionQuery::create()->findPk($customerOption['Id']);
        $c->setGsalesId($customerOption['GsalesId']);
        $c->setSkip($customerOption['Skip']);
        $c->save();

        echo "{$customerOption['Id']}, {$customerOption['GsalesId']}, {$customerOption['Skip']}" . PHP_EOL;
    }

    foreach ($projectOptions as $projectOption) {
        $p = ProjectOptionQuery::create()->findPk($projectOption['Id']);
        $p->setSeparate($projectOption['Separate']);
        $p->setSkip($projectOption['Skip']);
        $p->save();

        echo "{$projectOption['Id']}, {$projectOption['Skip']}, {$projectOption['Separate']}" . PHP_EOL;
    }
});

$app->get('/testings', function (Request $request, Response $response, array $args) {
    $miteApi = new MiteApiHandler();
    $gsalesApi = new GsalesApiHandler();

    $miteCustomers = $miteApi->getCustomers();
    $miteProjects = $miteApi->getProjects();
    $gsalesCustomers = $gsalesApi->getCustomers();

    $counter = 0;
    foreach ($miteCustomers->getAll() as $miteCustomer) {
        $customerOption = CustomerOptionQuery::create()
            ->filterByMiteId($miteCustomer->getId())
            ->findOneOrCreate();

        var_dump($customerOption);

        foreach ($miteProjects->getAll() as $project) {
            if ($project->getCustomerId() == $miteCustomer->getId()) {
                $projectOption = ProjectOptionQuery::create()
                    ->filterByCustomerOption($customerOption)
                    ->filterByMiteProjectId($project->getId())
                    ->findOneOrCreate();

                $customerOption->addProjectOption($projectOption);
            }
        }
        $counter++;
//        $customerOption->save();
    }
    var_dump($counter);
});

$app->post('/options/customers/get/', function (Request $request, Response $response, array $args) {
    $miteApi = new MiteApiHandler();
    $gsalesApi = new GsalesApiHandler();

    $miteCustomers = $miteApi->getCustomers();
    $miteProjects = $miteApi->getProjects();
    $gsalesCustomers = $gsalesApi->getCustomers();

    foreach ($miteCustomers->getAll() as $miteCustomer) {
        $customerOption = CustomerOptionQuery::create()
            ->filterByMiteId($miteCustomer->getId())
            ->findOneOrCreate();

        foreach ($miteProjects->getAll() as $project) {
            if ($project->getCustomerId() == $miteCustomer->getId()) {
                $projectOption = ProjectOptionQuery::create()
                    ->filterByCustomerOption($customerOption)
                    ->filterByMiteProjectId($project->getId())
                    ->findOneOrCreate();

                $customerOption->addProjectOption($projectOption);
            }
        }

        $customerOption->save();
    }

    $customerOptions = CustomerOptionQuery::create();
    $projectOptions = ProjectOptionQuery::create();

    if ($request->getParam("isImporter")) {
        $projectOptions->orderBySkip(Criteria::ASC);
    }

    $rtnData = new stdClass();
    $rtnData->miteCustomers = $miteCustomers->toStdObjectArray();
    $rtnData->miteProjects = $miteProjects->toStdObjectArray();
    $rtnData->gsalesCustomers = $gsalesCustomers->toStdObjectArray();
    $rtnData->customerOptions = json_decode($customerOptions->find()->toJSON())->CustomerOptions;
    $rtnData->projectOptions = json_decode($projectOptions->find()->toJSON())->ProjectOptions;

    if ($request->getParam("dateFrom") && $request->getParam("dateTo")) {
        $rtnData->timeEntries = $miteApi->getEntries(new DateTime($request->getParam("dateFrom")), new DateTime($request->getParam("dateTo")))->toStdObjectArray();
    }

    foreach ($rtnData->projectOptions as $projectOption) {
        if (isset($projectOption->CustomerOption)) {
            unset($projectOption->CustomerOption);
        }
    }

    foreach ($rtnData->customerOptions as $customerOption) {
        if (isset($customerOption->ProjectOptions)) {
            unset($customerOption->ProjectOptions);
        }
    }

    $rtnJson = json_encode($rtnData);

    return $response->write($rtnJson);
});

$app->get('options/get/', function (Request $request, Response $response, array $args) {
    $miteApi = new MiteApiHandler();

    // Hole alle aktiven MiteKunden
    $miteCustomers = $miteApi->getCustomers();

    $customerOptionCollection = new \Propel\Runtime\Collection\Collection();
    foreach ($miteCustomers as $miteCustomer) {
        $customerOption = CustomerOptionQuery::create()
            ->filterByMiteId($miteCustomer)
            ->findOneOrCreate();

        $customerOptionJson = $customerOption->exportTo("JSON");
        $customerOptionStdObject = json_decode($customerOptionJson);
        $customerOptionStdObject->name = $miteCustomer;

        $customerOptionCollection->append($customerOption);
    }

    $options = [];
    foreach ($customerOptionCollection as $customerOption) {
        /** @var CustomerOption $customerOption */
        $customerOptionJson = $customerOption->exportTo("JSON");
        $customerOptionStdObject = json_decode($customerOptionJson);
    }
    $options = [];
    $option = new stdClass();
    $option->db = new stdClass();
    $option->miteName = "";
    $option->gsalesName = "";

    $optionsJson = json_encode($options);
    return $response->write($optionsJson);
});

$app->get('/options/sync/', function (Request $request, Response $response, array $args) {
    $miteApi = new MiteApiHandler();

    // Hole alle aktiven MiteKunden
    $startTime = microtime(true);
    $miteCustomers = $miteApi->getCustomers();
    var_dump($miteCustomers);
    var_dump(microtime(true) - $startTime);

    $gsalesApi = new GsalesApiHandler();
    $gsalesCustomers = $gsalesApi->getCustomers()['result'];
    var_dump(microtime(true) - $startTime);
    var_dump($gsalesCustomers);

    foreach ($miteCustomers as $miteCustomer) {
        foreach ($gsalesCustomers as $gsalesCustomer) {

        }
    }

//    var_dump($gsalesApi->getCustomers());
    $customerOptions = [];
    foreach ($miteCustomers as $miteCustomer) {
        $customerOption = CustomerOptionQuery::create()
            ->filterByMiteId()
            ->findOneOrCreate();

        if ($customerOption->isModified()) {
            $customerOption->save();
        }

        $customerOptions[] = $customerOption;
    }

    var_dump(microtime(true) - $startTime);
});


$app->get('/test', function (Request $request, Response $response, array $args) {
    $co = CustomerOptionQuery::create()->findOne();
    $co->setMiteId(3);
    $co->getProjectOptions();

    var_dump($co);

    $json = $co->exportTo("JSON");
    $obj = json_decode($json);
    $obj->Name = "hallo";
    $obj->GsalesId = 13;

    $obj->aijwidajwdi = 1;
    $obj->pwekfpwekfwepf = 1;

    var_dump($obj);

    $json2 = json_encode($obj);
    $co->importFrom("JSON", $json2);
    var_dump($co);

    var_dump($json2);

    $co2 = new CustomerOption();
    $co2->importFrom("JSON", $json2);
});
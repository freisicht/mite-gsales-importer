<?php
/**
 * Created by PhpStorm.
 * User: Dev
 * Date: 27.10.16
 * Time: 16:21
 */

use Freisicht\InvoiceImporter\CustomerOptionCollection;
use Freisicht\InvoiceImporter\ProjectOptionCollection;
use Slim\Http\Request;
use Slim\Http\Response;
use Freisicht\InvoiceImporter\CustomerOption;
use Freisicht\InvoiceImporter\ProjectOption;

$app->get('/manuelimport', function (Request $request, Response $response, array $args) {
    $response = $this->view->render($response, 'header.html');
    $response = $this->view->render($response, 'manuel_importer.html');

    return $this->view->render($response, 'footer.html');
});

$app->post('/runrun', function (Request $request, Response $response, array $args) {
    if (ob_get_level() == 0) ob_start();

    $view = new Slim\Views\PhpRenderer("../web/templates/");
    echo $view->fetch("header.html");

    $filename = new DateTime('now', new DateTimeZone('Europe/Berlin'));
    $file = dirname(__DIR__) . "/logs/{$filename->format('Y_m_d H_i_s')}.log";
    $fp = fopen($file, 'w');
    fclose($fp);
    chmod($file, 0777);
    $stream = new \Monolog\Handler\StreamHandler($file);

    $log = new ImportLogger("ImportLogger", [$stream, new ImportHtmlHandler()]);

    echo '<div class="content">';
    echo '<h1>Importverlauf</h1>';

    $optionsJson = $request->getParsedBody()['options'];
    $options = json_decode($optionsJson);
    $start = new DateTime($options->from);
    $end = new DateTime($options->to);
    $customersOptions = $options->customers;
    $projectsOptions = $options->projects;

    $cColl = new CustomerOptionCollection();
    foreach ($customersOptions as $pOption) {
        $newPO = new CustomerOption();
        $newPO->setId($pOption->Id);
        $newPO->setMiteId($pOption->MiteId);
        $newPO->setGsalesId($pOption->GsalesId);
        $newPO->setSkip($pOption->Skip);
        $cColl->append($newPO);
    }

    $pColl = new ProjectOptionCollection();
    foreach ($projectsOptions as $pOption) {
        $newPO = new ProjectOption();
        $newPO->setId($pOption->Id);
        $newPO->setMiteProjectId($pOption->MiteProjectId);
        $newPO->setSkip($pOption->Skip);
        $newPO->setSeparate($pOption->Separate);
        $pColl->append($newPO);
    }

    $importer = new Importer(new GsalesApiHandler(), new MiteApiHandler(), $cColl, $pColl, $start, $end, $log);

    $importer->getApiData();
    $importer->checkData();
    $importer->prepareData();
    $importer->startImport();
    echo "Done.";
    echo '</div>';
    echo $view->fetch("footer.html");
    ob_end_flush();
    die;
});

$app->get('/runrun2', function (Request $request, Response $response, array $args) {
    if (ob_get_level() == 0) ob_start();

    $view = new Slim\Views\PhpRenderer("../web/templates/");
    echo $view->fetch("header.html");

    $log = new ImportLogger("Logga");
    $filename = microtime();
    $file = dirname(__DIR__) . "/logs/{$filename}.log";
    $fp = fopen($file, 'w');
    fclose($fp);
    chmod($file, 0777);
    $stream = new \Monolog\Handler\StreamHandler(dirname(__DIR__) . "/logs/test.log");


    $log->pushHandler($stream);
    $log->pushHandler(new ImportHtmlHandler());

    echo '<div class="content">';
    echo '<h1>Wird importiert <i class="material-icons loading">&#xE86A;</i></h1>';

    for ($i = 0; $i<10; $i++){

        if ($i === 1 || $i === 3)
            $log->success("hi");
        else
            $log->error("ERROORR");

        $log->info("yoyo");

        echo str_pad('',4096)."\n";

        ob_flush();
        flush();
        sleep(2);
    }


    echo "Done.";
    echo '</div>';
    echo $view->fetch("footer.html");
    ob_end_flush();


    die;

    $response = $this->view->render($response, 'header.html');
    $response = $this->view->render($response, 'manuel_importer.html');

    return $this->view->render($response, 'footer.html');
});
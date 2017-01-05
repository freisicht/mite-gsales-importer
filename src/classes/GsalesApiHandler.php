<?php
/**
 * Created by PhpStorm.
 * User: Dev
 * Date: 24.10.16
 * Time: 13:19
 */

class GsalesApiHandler implements IApiAvailability
{
    protected $client = null;
    protected $customers = [];
    protected $options;

    function __construct($options = null)
    {
        if ($options instanceof ApiHandlerOptions) {
            $this->options = $options;
        } else {
            $this->options = self::generateApiOptions();
        }

        $intStart = microtime(true);
        ini_set("soap.wsdl_cache_enabled", "0");
        $this->client = new soapclient($this->options->getApiUrl());
    }

    public function createInvoice(int $gsalesCustomerId):GsalesInvoice
    {
        $arrResult = $this->client->createInvoiceForCustomer($this->options->getApiKey(), $gsalesCustomerId);

        return new GsalesInvoice($arrResult["result"]->base->id, $arrResult["result"]->base->customers_id);
    }

    public function createInvoicePosition(GsalesInvoice $invoice, $quantity, $price, $unit, $posTxt, $useDefaultTax = true)
    {
        $arrCreateInvoicePosition = array('quantity'=>$quantity, 'price'=>$price, 'unit'=>$unit, 'pos_txt'=>$posTxt, 'useDefaultTax'=>$useDefaultTax);
        $arrResult = $this->client->createInvoicePosition($this->options->getApiKey(), $invoice->getId(), $arrCreateInvoicePosition);

        return $arrResult;
    }

    public function getCustomers(): GsalesCustomerCollection
    {
        $customers = new GsalesCustomerCollection();

        $customerCount = $this->client->getCustomersCount($this->options->getApiKey());

        $recordCount = 450;
        $offset = 0;

        while(true) {
            $parameters = [
                'apikey' => $this->options->getApiKey(),
                'filter' => null,
                'sort' => null,
                'recordcount' => 450,
                'recordoffset' => $offset
            ];

            $results = $this->client->__soapCall('getCustomers', $parameters);

            if (count($results['result']) == 0) {
                break;
            }

            foreach ($results['result'] as $result) {
                $customer = ApiObjectFactory::createGsalesCustomer($result);
                $customers->add($customer);
            }

            $offset += $recordCount;
        }

        return $customers;
    }

    public function getCustomer($id): GsalesCustomer
    {
        $results = $this->client->getCustomer($this->options->getApiKey(), $id);

        $customer = new GsalesCustomer($results['result']);

        return $customer;
    }

    public function isApiAvailable(): bool
    {
        return true;
    }

    public static function generateApiOptions($configPath = "../config/apis/gsales.json"): ApiHandlerOptions
    {
        $json = file_get_contents($configPath);
        $data = json_decode($json);

        return new ApiHandlerOptions($data->apiUrl, $data->apiKey);
    }
}

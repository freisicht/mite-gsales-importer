<?php
/**
 * Created by PhpStorm.
 * User: Dev
 * Date: 24.10.16
 * Time: 13:19
 */

class MiteApiHandler implements IApiAvailability
{
    /**
     * @var ApiHandlerOptions
     */
    protected $options;

    private $customers;
    private $allCustomersReceived = false;
    private $projects;
    private $allProjectsReceived = false;

    function __construct($options = null)
    {
        if ($options instanceof ApiHandlerOptions) {
            $this->options = $options;
        } else {
            $this->options = self::generateApiOptions();
        }

        $this->customers = new MiteCustomerCollection();
        $this->projects = new MiteProjectCollection();
    }

    /**
     * @param $from
     * @param $to
     * @param null|int|int[] $projectFilter
     * @return MiteTimeEntryCollection
     */
    public function getEntries(DateTime $from, DateTime $to, $projectFilter = null)
    {
        $filter = '';
        if ($projectFilter != null) {
            $filter .= "&";

            if (is_array($projectFilter)) {
                $filter .= implode(",", $projectFilter);
            } else {
                $filter .= $projectFilter;
            }
        }

        $url = $this->getFullUrl('time_entries.json', "&from={$from->format('Y-m-d')}&to={$to->format('Y-m-d')}{$filter}");
        $entries = json_decode($this->performExec($url));

        $collection = new MiteTimeEntryCollection();
        foreach($entries AS $entry) {
            $timeEntry = ApiObjectFactory::createMiteEntryFromMiteApiObj($entry->time_entry);
            $collection->add($timeEntry);
        }

        return $collection;
    }

    /**
     * @param $id
     * @return MiteCustomer|null
     */
    public function getCustomer($id)
    {
        return $this->getCustomers()->getById($id);
    }

    /**
     * @return MiteCustomerCollection
     */
    public function getCustomers(): MiteCustomerCollection
    {
        if (!$this->allCustomersReceived) {
            $url = $this->getFullUrl("customers.json");
            $customers = json_decode($this->performExec($url));
            foreach ($customers as $miteCustomer) {
                $customer = ApiObjectFactory::createMiteCustomer($miteCustomer->customer);

                if (!$this->customers->containsId($customer->getId())) {
                    $this->customers->add($customer);
                }
            }

            $this->allCustomersReceived = true;
        }

        return $this->customers->getCopy();
    }

    /**
     * @param MiteCustomerCollection|null $miteCustomers
     * @return MiteProjectCollection
     */
    public function getProjects(MiteCustomerCollection $miteCustomers = null): MiteProjectCollection
    {
        if (!$this->allCustomersReceived) {
            $url = $this->getFullUrl("customers.json");
            $customers = json_decode($this->performExec($url));
            foreach ($customers as $miteCustomer) {
                $customer = ApiObjectFactory::createMiteCustomer($miteCustomer->customer);

                if (!$this->customers->containsId($customer->getId())) {
                    $this->customers->add($customer);
                }
            }

            $this->allCustomersReceived = true;
        }

        if (!$this->allProjectsReceived) {
            $url = $this->getFullUrl("projects.json");
            $projects = json_decode($this->performExec($url));
            foreach ($projects as $miteProject) {
                $project = ApiObjectFactory::createMiteProject($miteProject->project);
                $this->projects->add($project);
            }
        }

        $rtnProjects = new MiteProjectCollection();
        if ($miteCustomers != null) {
            foreach ($this->projects->getAll() as $miteProject) {
                if ($miteCustomers->containsId($miteProject->getId())) {
                    $rtnProjects->add($miteProject);
                }
            }
        } else {
            $rtnProjects = $this->projects->getCopy();
        }

        return $rtnProjects;
    }

    /**
     * @param $id
     * @return MiteProject|null
     */
    public function getProject($id)
    {
        return $this->getProjects()->getById($id);
    }

    protected function performExec($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        return curl_exec($ch);
    }

    protected function getFullUrl($section, $parameters='')
    {
        return "{$this->options->getApiUrl()}{$section}?api_key={$this->options->getApiKey()}{$parameters}";
    }

    public function isApiAvailable(): bool
    {
        return true;
    }

    public static function generateApiOptions($configPath = "../config/apis/mite.json"): ApiHandlerOptions
    {
        $json = file_get_contents($configPath);
        $data = json_decode($json);

        return new ApiHandlerOptions($data->apiUrl, $data->apiKey);
    }
}
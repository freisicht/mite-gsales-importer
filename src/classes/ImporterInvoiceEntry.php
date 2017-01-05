<?php

class ImporterInvoiceEntry
{
    /** @var MiteCustomer */
    private $miteCustomer;
    /** @var GsalesCustomer */
    private $gsalesCustomer;
    /** @var MiteProjectCollection */
    private $miteProjects;
    /** @var MiteTimeEntryCollection */
    private $timeEntries;

    function __construct()
    {
        $this->miteProjects = new MiteProjectCollection();
        $this->timeEntries = new MiteTimeEntryCollection();
    }

    /**
     * @return MiteCustomer
     */
    public function getMiteCustomer(): MiteCustomer
    {
        return $this->miteCustomer;
    }

    /**
     * @param MiteCustomer $miteCustomer
     */
    public function setMiteCustomer(MiteCustomer $miteCustomer)
    {
        $this->miteCustomer = $miteCustomer;
    }

    /**
     * @return GsalesCustomer
     */
    public function getGsalesCustomer(): GsalesCustomer
    {
        return $this->gsalesCustomer;
    }

    /**
     * @param GsalesCustomer $gsalesCustomer
     */
    public function setGsalesCustomer(GsalesCustomer $gsalesCustomer)
    {
        $this->gsalesCustomer = $gsalesCustomer;
    }

    /**
     * @return MiteProjectCollection
     */
    public function getMiteProjects(): MiteProjectCollection
    {
        return $this->miteProjects;
    }

    /**
     * @param MiteProjectCollection $miteProjects
     */
    public function addMiteProjects(MiteProjectCollection $miteProjects)
    {
        foreach ($miteProjects AS $project) {
            $this->miteProjects->add($project);
        }
    }

    /**
     * @param MiteProject $miteProject
     */
    public function addMiteProject(MiteProject $miteProject)
    {
        $this->miteProjects->add($miteProject);
    }

    /**
     * @return MiteTimeEntryCollection
     */
    public function getTimeEntries(): MiteTimeEntryCollection
    {
        return $this->timeEntries;
    }

    /**
     * @param MiteTimeEntryCollection $timeEntries
     */
    public function addTimeEntries(MiteTimeEntryCollection $timeEntries)
    {
        foreach ($timeEntries->getAll() AS $timeEntry) {
            $this->timeEntries->add($timeEntry);
        }
    }

    /**
     * @param MiteTimeEntry $timeEntry
     */
    public function addTimeEntry(MiteTimeEntry $timeEntry)
    {
        $this->timeEntries->add($timeEntry);
    }
}

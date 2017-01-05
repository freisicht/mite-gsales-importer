<?php

class ApiObjectFactory
{
    public static function createGsalesInvoicePosition(GsalesInvoice $invoice, MiteTimeEntry $timeEntry): GsalesInvoicePosition
    {
        // calculate minutes in hours to get our "quantity" for the position
        $minutes = $timeEntry->getMinutes();
        $hours = floor($minutes / 60);
        $rest = $minutes % 60;

        if ($rest >= 30) {
            $hours++;
        } elseif ($rest > 0) {
            $hours += 0.5;
        }

        return new GsalesInvoicePosition($invoice->getCustomerId(), $hours, $timeEntry->getNote(), $timeEntry->getHourlyRate());
    }

    public static function createMiteEntryFromMiteApiObj(stdClass $obj): MiteTimeEntry
    {
        if (!isset($obj->id))
            throw new Exception("Id missing on the apiObject. Could not create a MiteTimeEntry!");

        $entry = new MiteTimeEntry();

        $entry->setId($obj->id);

        if (isset($obj->billable))
            $entry->setBillable($obj->billable);

        if (isset($obj->created_at))
            $entry->setCreatedAt($obj->created_at);

        if (isset($obj->date_at))
            $entry->setDateAt($obj->date_at);

        if (isset($obj->hourly_rate))
            $entry->setHourlyRate($obj->hourly_rate);

        if (isset($obj->locked))
            $entry->setLocked($obj->locked);

        if (isset($obj->minutes))
            $entry->setMinutes($obj->minutes);

        if (isset($obj->note))
            $entry->setNote($obj->note);

        if (isset($obj->project_id))
            $entry->setProjectId($obj->project_id);

        if (isset($obj->revenue))
            $entry->setRevenue($obj->revenue);

        if (isset($obj->service_id))
            $entry->setServiceId($obj->service_id);

        if (isset($obj->service_name))
            $entry->setServiceName($obj->service_name);

        if (isset($obj->updated_at))
            $entry->setUpdatedAt($obj->updated_at);

        if (isset($obj->user_id))
            $entry->setUserId($obj->user_id);

        if (isset($obj->user_name))
            $entry->setUserName($obj->user_name);

        return $entry;
    }

    public static function createMiteCustomer(stdClass $obj): MiteCustomer
    {
        $customer = new MiteCustomer();

        if (isset($obj->id))
            $customer->setId($obj->id);

        if (isset($obj->name))
            $customer->setName($obj->name);

        return $customer;
    }

    public static function createMiteProject(stdClass $obj): MiteProject
    {
        $project = new MiteProject();

        if (isset($obj->id))
            $project->setId($obj->id);

        if (isset($obj->name))
            $project->setName($obj->name);

        if (isset($obj->customer_id))
            $project->setCustomerId($obj->customer_id);

        return $project;
    }

    public static function createGsalesCustomer(stdClass $obj): GsalesCustomer
    {
        $rtn = new GsalesCustomer();

        if (isset($obj->id))
            $rtn->setId($obj->id);

        if (isset($obj->company))
            $rtn->setCompany($obj->company);

        if (isset($obj->firstname))
            $rtn->setFirstname($obj->firstname);

        if (isset($obj->lastname))
            $rtn->setLastname($obj->lastname);

        return $rtn;
    }
}

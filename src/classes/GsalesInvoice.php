<?php

/**
 * Created by PhpStorm.
 * User: Dev
 * Date: 27.10.16
 * Time: 17:13
 */

class GsalesInvoice extends ApiDataObject
{
    private $id;
    private $customerId;
    private $positions = [];

    function __construct(int $id, int $customerId)
    {
        $this->id = $id;
        $this->customerId = $customerId;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    /**
     * @return GsalesInvoicePosition[]
     */
    public function getPositions(): array
    {
        return $this->positions;
    }

    /**
     * @param GsalesInvoicePosition $position
     */
    public function addPosition(GsalesInvoicePosition $position)
    {
        $this->positions[] = $position;
    }

    public function getPositionsCount(): int
    {
        return count($this->getPositions());
    }

    public function hasPositions(): bool
    {
        return $this->getPositionsCount() > 0;
    }

    /**
     * @return stdClass
     */
    public function toStdObject():stdClass
    {
        $obj     = new stdClass();
        $allVars = get_object_vars($this);

        foreach ($allVars as $key => $val) {
            if (!$val instanceof ApiDataObjectCollection) {
                $obj->$key = $val;
            }
        }

        return $obj;
    }
}
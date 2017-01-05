<?php
/**
 * Created by PhpStorm.
 * User: Dev
 * Date: 25.10.16
 * Time: 12:02
 */

class MiteProject extends ApiDataObject
{
    /** @var int */
    private $id;
    /** @var string */
    private $name;
    /** @var int */
    private $customer_id;

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @param int $customer_id
     */
    public function setCustomerId(int $customer_id)
    {
        $this->customer_id = $customer_id;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getCustomerId()
    {
        return $this->customer_id;
    }

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

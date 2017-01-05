<?php
/**
 * Created by PhpStorm.
 * User: Dev
 * Date: 24.10.16
 * Time: 18:12
 */

class MiteCustomer extends ApiDataObject
{
    private $id;
    private $name = "";

    /**
     * @param mixed $id
     */
    public function setId($id)
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
     * @return int
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    function toJson(): string
    {
        return json_encode($this->toStdObject());
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

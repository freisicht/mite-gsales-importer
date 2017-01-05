<?php

/**
 * Created by PhpStorm.
 * User: Dev
 * Date: 25.10.16
 * Time: 19:12
 */
abstract class ApiDataObjectCollection
{
    /** @var ApiDataObject[] */
    private $array = [];

    public function add(ApiDataObject $item)
    {
        if (!in_array($item, $this->array)) {
            $this->array[] = $item;
        }
    }

    public function remove($item)
    {
        $key = array_search($item, $this->array);

        if ($key !== false) {
            unset($this->array[$key]);
        }
    }

    public function count(): int
    {
        return count($this->array);
    }

    /**
     * @param int $id
     * @return mixed
     * Only works if the items have an IId Interface
     */
    public function getById(int $id)
    {
        foreach ($this->array as $item) {
            if ($item->getId() == $id) {
                return $item;
            }
        }

        return null;
    }

    public function getAll(): array
    {
        return $this->array;
    }

    /**
     * @param $item
     * @return bool
     */
    public function contains($item): bool
    {
        foreach ($this as $itemit) {
            if ($item == $itemit) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $id
     * @return bool
     */
    public function containsId($id): bool
    {
        foreach ($this->array as $item) {
            if ($item->getId() == $id) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return stdClass[]
     */
    public function toStdObjectArray(): array
    {
        $rtnArray = [];
        foreach ($this->array as $item) {
            $rtnArray[] = $item->toStdObject();
        }

        return $rtnArray;
    }

    public function getCopy()
    {
        $class = get_class($this);
        $copy = new $class;

        foreach ($this->array as $item) {
            $copy->add($item);
        }

        return $copy;
    }

    public function toJson():string
    {

    }
}

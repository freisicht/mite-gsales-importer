<?php

/**
 * Created by PhpStorm.
 * User: Dev
 * Date: 20.11.16
 * Time: 22:53
 */
abstract class ApiDataObject
{
    public abstract function getId(): int;

    public function toJson(): string
    {
        return json_encode($this->toStdObject());
    }

    abstract public function toStdObject(): stdClass;
}

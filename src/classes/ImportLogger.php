<?php

class ImportLogger extends \Monolog\Logger
{
    public function success($message, array $context = array())
    {
        $this->info($message, array_merge($context, ["displaytype" => "success"]));
    }

    public function error($message, array $context = array())
    {
        return parent::error($message, array_merge($context, ["displaytype" => "error"]));
    }

    public function notice($message, array $context = array())
    {
        return parent::notice($message, array_merge($context, ["displaytype" => "info"]));
    }
}

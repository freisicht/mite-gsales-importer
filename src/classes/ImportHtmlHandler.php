<?php

use Monolog\Handler\HandlerInterface;
use Monolog\Logger;

class ImportHtmlHandler implements HandlerInterface
{
    public function isHandling(array $record)
    {
        return true;
    }

    public function handle(array $record)
    {
        $colorClass = "";

        if (array_key_exists("displaytype", $record['context'])) {
            $colorClass = $record['context']['displaytype'];
        }

        echo '<p class="' . $colorClass . '">' . $record['message'] . '</p>';
        ob_flush();
        flush();
    }

    public function handleBatch(array $records)
    {
        // TODO: Implement handleBatch() method.
    }

    public function pushProcessor($callback)
    {
        // TODO: Implement pushProcessor() method.
    }

    public function popProcessor()
    {
        // TODO: Implement popProcessor() method.
    }

    public function setFormatter(\Monolog\Formatter\FormatterInterface $formatter)
    {
        // TODO: Implement setFormatter() method.
    }

    public function getFormatter()
    {
        // TODO: Implement getFormatter() method.
    }

}
<?php
namespace Freisicht\InvoiceImporter;

use Propel\Runtime\Collection\ObjectCollection;

class CustomerOptionCollection extends ObjectCollection
{
    public function getByMiteId(int $id)
    {
        foreach ($this->getData() as $option) {
            /** @var CustomerOption $option */
            if ($option->getMiteId() === $id) {
                return $option;
            }
        }


        return null;
    }
}

<?php
namespace Freisicht\InvoiceImporter;

use Propel\Runtime\Collection\ObjectCollection;

class ProjectOptionCollection extends ObjectCollection
{
    public function getByCustomerOptionId(int $id): ProjectOptionCollection
    {
        $coll = new ProjectOptionCollection();

        foreach ($this->getData() as $option) {
            /** @var ProjectOption $option */
            if ($option->getCustomerOptionId() === $id) {
                $coll->append($option);
            }
        }

        return $coll;
    }

    /**
     * @param int $id
     * @return ProjectOption|null
     */
    public function getByMiteProjectId(int $id)
    {
        foreach ($this->data AS $item) {
            /** @var ProjectOption $item */
            if ($item->getMiteProjectId() == $id) {
                return $item;
            }
        }

        return null;
    }
}

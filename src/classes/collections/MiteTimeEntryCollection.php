<?php

/**
 * Created by PhpStorm.
 * User: Dev
 * Date: 20.11.16
 * Time: 23:27
 */
class MiteTimeEntryCollection extends ApiDataObjectCollection
{
    public function getByProjectId(int $id): MiteTimeEntryCollection
    {
        $coll = new MiteTimeEntryCollection();

        foreach ($this->getAll() AS $item) {
            /** @var MiteTimeEntry $item */
            if ($item->getProjectId() == $id) {
                $coll->add($item);
            }
        }

        return $coll;
    }
}
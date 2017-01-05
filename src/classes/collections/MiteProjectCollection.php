<?php

class MiteProjectCollection extends ApiDataObjectCollection
{
    /**
     * @return int[]
     */
    public function getIdsArray(): array
    {
        $rtnArr = [];
        foreach ($this->getAll() AS $item) {
            /** @var MiteProject $item */
            $rtnArr[] = $item->getId();
        }

        return $rtnArr;
    }

    /**
     * @param int $id
     * @return MiteProjectCollection
     */
    public function getByCustomerId(int $id): MiteProjectCollection
    {
        $coll = new MiteProjectCollection();
        foreach ($this->getAll() AS $item) {
            /** @var $item MiteProject */
            if ($item->getCustomerId() === $id) {
                $coll->add($item);
            }
        }

        return $coll;
    }
}

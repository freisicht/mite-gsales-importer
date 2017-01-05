<?php

/**
 * Created by PhpStorm.
 * User: Dev
 * Date: 27.10.16
 * Time: 17:13
 */

class GsalesInvoicePosition extends ApiDataObject
{
    private $id;
    private $invoiceId;
    private $quantity;
    private $pos_text;
    private $price;
    private $unit;
    private $useDefaultTax;

    /**
     * @return int
     */
    public function getInvoiceId(): int
    {
        return $this->invoiceId;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @return string
     */
    public function getPosText(): string
    {
        return $this->pos_text;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @return string
     */
    public function getUnit(): string
    {
        return $this->unit;
    }

    /**
     * @return boolean
     */
    public function isUseDefaultTax(): bool
    {
        return $this->useDefaultTax;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param int $invoiceId
     */
    public function setInvoiceId(int $invoiceId)
    {
        $this->invoiceId = $invoiceId;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity(int $quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @param string $pos_text
     */
    public function setPosText(string $pos_text)
    {
        $this->pos_text = $pos_text;
    }

    /**
     * @param float $price
     */
    public function setPrice(float $price)
    {
        $this->price = $price;
    }

    /**
     * @param string $unit
     */
    public function setUnit(string $unit)
    {
        $this->unit = $unit;
    }

    /**
     * @param boolean $useDefaultTax
     */
    public function setUseDefaultTax(bool $useDefaultTax)
    {
        $this->useDefaultTax = $useDefaultTax;
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
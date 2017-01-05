<?php

/**
 * Created by PhpStorm.
 * User: Dev
 * Date: 27.10.16
 * Time: 17:32
 */

class MiteTimeEntry extends ApiDataObject
{
    /** @var int */
    private $id;
    /** @var int */
    private $minutes;
    /** @var string */
    private $date_at;
    /** @var string */
    private $note;
    /** @var bool */
    private $billable;
    /** @var bool */
    private $locked;
    private $revenue;
    private $hourly_rate;
    /** @var int */
    private $user_id;
    /** @var string */
    private $user_name;
    /** @var int */
    private $project_id;
    /** @var int */
    private $service_id;
    /** @var string */
    private $service_name;
    private $created_at;
    private $updated_at;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getMinutes(): int
    {
        return $this->minutes;
    }

    /**
     * @param int $minutes
     */
    public function setMinutes(int $minutes)
    {
        $this->minutes = $minutes;
    }

    /**
     * @return string
     */
    public function getDateAt(): string
    {
        return $this->date_at;
    }

    /**
     * @param string $date_at
     */
    public function setDateAt(string $date_at)
    {
        $this->date_at = $date_at;
    }

    /**
     * @return string
     */
    public function getNote(): string
    {
        return $this->note;
    }

    /**
     * @param string $note
     */
    public function setNote(string $note)
    {
        $this->note = $note;
    }

    /**
     * @return boolean
     */
    public function isBillable(): bool
    {
        return $this->billable;
    }

    /**
     * @param boolean $billable
     */
    public function setBillable(bool $billable)
    {
        $this->billable = $billable;
    }

    /**
     * @return boolean
     */
    public function isLocked(): bool
    {
        return $this->locked;
    }

    /**
     * @param boolean $locked
     */
    public function setLocked(bool $locked)
    {
        $this->locked = $locked;
    }

    /**
     * @return mixed
     */
    public function getRevenue()
    {
        return $this->revenue;
    }

    /**
     * @param mixed $revenue
     */
    public function setRevenue($revenue)
    {
        $this->revenue = $revenue;
    }

    /**
     * @return mixed
     */
    public function getHourlyRate()
    {
        return $this->hourly_rate;
    }

    /**
     * @param mixed $hourly_rate
     */
    public function setHourlyRate($hourly_rate)
    {
        $this->hourly_rate = $hourly_rate;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->user_id;
    }

    /**
     * @param int $user_id
     */
    public function setUserId(int $user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     * @return string
     */
    public function getUserName(): string
    {
        return $this->user_name;
    }

    /**
     * @param string $user_name
     */
    public function setUserName(string $user_name)
    {
        $this->user_name = $user_name;
    }

    /**
     * @return int
     */
    public function getProjectId(): int
    {
        return $this->project_id;
    }

    /**
     * @param int $project_id
     */
    public function setProjectId(int $project_id)
    {
        $this->project_id = $project_id;
    }

    /**
     * @return int
     */
    public function getServiceId(): int
    {
        return $this->service_id;
    }

    /**
     * @param int $service_id
     */
    public function setServiceId(int $service_id)
    {
        $this->service_id = $service_id;
    }

    /**
     * @return string
     */
    public function getServiceName(): string
    {
        return $this->service_name;
    }

    /**
     * @param string $service_name
     */
    public function setServiceName(string $service_name)
    {
        $this->service_name = $service_name;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param mixed $created_at
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * @param mixed $updated_at
     */
    public function setUpdatedAt($updated_at)
    {
        $this->updated_at = $updated_at;
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

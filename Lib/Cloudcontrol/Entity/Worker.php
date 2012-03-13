<?php

namespace Cloudcontrol\Entity;

use Cloudcontrol\Api;

class Worker extends AbstractEntity
{
    protected $createdAt;

    /**
     * Constructor.
     *
     * @param array $data
     */
    public function __construct($data)
    {
        $this->createdAt = new \DateTime($data['date_created'], Api::getTimezone());

        parent::__construct($data);
    }

    /**
     * Return the worker ID.
     *
     * @return string
     */
    public function getWorkerId()
    {
        return $this['wrk_id'];
    }

    /**
     * Return the command line.
     *
     * @return string
     */
    public function getCommand()
    {
        return $this['command'];
    }

    /**
     * Return the command parameters.
     *
     * @return string
     */
    public function getParameters()
    {
        return $this['params'];
    }

    /**
     * Return the creation date of this Worker.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}
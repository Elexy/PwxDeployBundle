<?php

namespace Pwx\DeployBundle\Lib\Cloudcontrol\Decoder;

interface DecoderInterface
{
    /**
     * Decode the given data into an associative array.
     *
     * @throws \Pwx\DeployBundle\Lib\Cloudcontrol\Exception\DecodingException
     *
     * @param mixed $data
     *
     * @return array
     */
    public function decode($data);
}
<?php

namespace Cloudcontrol\Decoder;

interface DecoderInterface
{
    /**
     * Decode the given data into an associative array.
     *
     * @throws \Cloudcontrol\Exception\DecodingException
     *
     * @param mixed $data
     *
     * @return array
     */
    public function decode($data);
}
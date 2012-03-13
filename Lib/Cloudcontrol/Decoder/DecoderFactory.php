<?php

namespace Cloudcontrol\Decoder;

use Cloudcontrol\Exception\RuntimeException;
use Cloudcontrol\Exception\UnknownContentTypeException;

class DecoderFactory
{
    /**
     * Return the corresponding decoder for a response content type.
     *
     * @throws \Cloudcontrol\Exception\UnknownContentTypeException
     *
     * @param string $contentType
     *
     * @return DecoderInterface
     */
    static public function createByContentType($contentType)
    {
        // Content-Type: application/json; charset=utf-8
        $mime = explode(';', $contentType);
        $mime = str_replace('Content-Type: ', '', $mime[0]);

        switch ($mime) {
            case 'application/json':
                $decoder = new JsonDecoder();
                break;

            default:
                throw new UnknownContentTypeException(sprintf('The given content type "%s" is not supported.', $contentType));
        }

        return $decoder;
    }

    /**
     * Return a decoder by naming convention.
     *
     * A decoder following the naming convention as follows, will be found.
     * The name of the decoder with upper case first and "Decoder" suffix in the Cloudcontrol\Decoder namespace.
     *
     * @param string $name The name of the Decoder to create.
     *
     * @return DecoderInterface
     */
    static public function createByName($name)
    {
        $class = sprintf('\Cloudcontrol\Decoder\%sDecoder', ucfirst($name));

        if (class_exists($class)) {
            return new $class();
        }

        throw new RuntimeException(sprintf('The class "%s" for decoder with name "%s" could not be found.', $class, $name));
    }
}

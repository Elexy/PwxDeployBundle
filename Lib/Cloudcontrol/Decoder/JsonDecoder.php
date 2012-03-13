<?php

namespace Cloudcontrol\Decoder;

use Cloudcontrol\Exception\DecodingException;

class JsonDecoder implements DecoderInterface
{
    /**
     * Decode the given json data string.
     *
     * @throws DecodingException
     *
     * @param string $data
     *
     * @return array
     */
    public function decode($data)
    {
        $result = json_decode($data, true);
        if (JSON_ERROR_NONE !== ($errorCode = json_last_error())) {
            switch (json_last_error()) {
                // @codeCoverageIgnoreStart
                case JSON_ERROR_DEPTH:
                    $error = 'The maximum stack depth has been exceeded';
                    break;
                case JSON_ERROR_CTRL_CHAR:
                    $error = 'Control character error, possibly incorrectly encoded';
                    break;
                case JSON_ERROR_SYNTAX:
                    $error = 'Syntax error';
                    break;
                case JSON_ERROR_STATE_MISMATCH:
                    $error = 'Invalid or malformed JSON';
                    break;
                case JSON_ERROR_UTF8:
                    $error = 'Malformed UTF-8 characters, possibly incorrectly encoded';
                    break;
                // @codeCoverageIgnoreEnd
            }

            throw new DecodingException(sprintf('Could not decode data with error "%s".', $error));
        }

        return $result;
    }
}


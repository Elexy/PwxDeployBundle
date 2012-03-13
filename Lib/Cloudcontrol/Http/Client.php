<?php

namespace Cloudcontrol\Http;

use Cloudcontrol\Api;
use Cloudcontrol\Authentication\Token;

use Http\Exception\Factory as HttpExceptionFactory;

use Buzz\Browser;
use Buzz\Client\Curl;
use Buzz\Client\ClientInterface;
use Buzz\Message\FactoryInterface;

class Client extends Browser
{
    protected $api;

    public function __construct(Client\ClientInterface $client = null, Message\FactoryInterface $factory = null)
    {
        if (null === $client) {
            $client = new Curl();
        }

        parent::__construct($client, $factory);
    }

    /**
     * Set the API to use.
     *
     * @param Api $api
     *
     * @return Client
     */
    public function setApi(Api $api)
    {
        $this->api = $api;

        return $this;
    }

    /**
     * Sends a request.
     *
     * @param string $url     The URL to call
     * @param string $method  The request method to use
     * @param array  $headers An array of request headers
     * @param string|array $content The request content
     *
     * @return \Buzz\Message\Response The response object
     */
    public function call($url, $method, $headers = array(), $content = '')
    {
        if (is_array($content)) {
            $content = $this->toBodyString($content);
        }

        $url = $this->api->getEndpoint().$url;

        if (!$this->api->hasToken()) {
            $response = parent::call($url, $method, $headers, $content);
        } else {
            $headers[Token::FIELD_NAME] = sprintf('Authorization: %s="%s"', Token::FIELD_NAME, (string) $this->api->getToken());
            $response = parent::call($url, $method, $headers, $content);

            // The token may have expired, let's try one more time.
            if (401 === $response->getStatusCode()) {
                $this->api->refreshToken();

                $headers[Token::FIELD_NAME] = sprintf('Authorization: %s="%s"', Token::FIELD_NAME, (string) $this->api->getToken());
                $response = parent::call($url, $method, $headers, $content);
            }
        }

        if ($response->getStatusCode() >= 400) {
            throw HttpExceptionFactory::createByCode($response->getStatusCode());
        }

        return $response;
    }

    /**
     * Convert the given data into a valid HTTP body.
     *
     * @param array $data
     *
     * @return string
     */
    public function toBodyString(array $data)
    {
        return str_replace('%7E', '~', http_build_query($data, '', '&'));
    }
}
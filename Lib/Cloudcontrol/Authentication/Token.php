<?php

namespace Pwx\DeployBundle\Lib\Cloudcontrol\Authentication;

use Pwx\DeployBundle\Lib\Cloudcontrol\Decoder\DecoderFactory;
use Pwx\DeployBundle\Lib\Cloudcontrol\Http\Client;

class Token
{
    /**
     * The field name for a token.
     *
     * @var string
     */
    const FIELD_NAME = 'cc_auth_token';

    /**
     * @var Client
     */
    protected $client;

    protected $email;
    protected $password;

    protected $isAuthenticated = false;

    protected $token = '';

    /**
     * Constructor.
     *
     * @param Api $api
     * @param string $email
     * @param string $password
     */
    public function __construct(Client $client, $email, $password)
    {
        $this->client = $client;
        $this->email = $email;
        $this->password = $password;
    }

    /**
     * Return the token string for API authentication.
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    public function __toString()
    {
        return $this->getToken();
    }

    /**
     * Check whether this Token has been retrieved.
     *
     * @return bool
     */
    public function isAuthenticated()
    {
        return $this->isAuthenticated;
    }

    /**
     * Authenticate with the cloudControl API.
     *
     * @return Token $this
     */
    public function authenticate()
    {
        if (!$this->isAuthenticated) {
            $headers = array('Authorization: Basic '.base64_encode($this->email.':'.$this->password));
            $response = $this->client->post('/token/', $headers);

            if (200 === $response->getStatusCode()) {
                $data = DecoderFactory::createByContentType($response->getHeader('Content-Type'))->decode($response->getContent());

                $this->token = $data['token'];
                $this->isAuthenticated = true;
            }
        }

        return $this;
    }
}
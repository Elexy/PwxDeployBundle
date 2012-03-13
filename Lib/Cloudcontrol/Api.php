<?php

namespace Cloudcontrol;

use Pwx\DeployBundle\Lib\Cloudcontrol\Authentication\Token;
use Pwx\DeployBundle\Lib\Cloudcontrol\Entity\Worker;
use Pwx\DeployBundle\Lib\Cloudcontrol\Decoder\DecoderFactory;
use Pwx\DeployBundle\Lib\Cloudcontrol\Http\Client;

use Pwx\DeployBundle\Lib\Cloudcontrol\Exception\ApplicationMissingException;
use Pwx\DeployBundle\Lib\Cloudcontrol\Exception\DeploymentMissingException;

use Http\Exception\Client\Gone as GoneException;

class Api
{
    /**
     * The URI for the live cloudControl API.
     *
     * @var string
     */
    const LIVE_ENDPOINT = 'https://api.cloudcontrol.com';

    /**
     * The currently active API endpoint.
     *
     * @var string
     */
    protected $endpoint;
    protected $client;

    // Authentication.
    protected $email;
    protected $password;
    protected $token;

    // The current active scope.
    protected $application;
    protected $deployment;

    /**
     * Constructor.
     *
     * @param Client $client An http client to utilize.
     * @param string $endpoint The http endpoint of the cloudControl API implementation to use.
     */
    public function __construct(Client $client = null, $endpoint = self::LIVE_ENDPOINT)
    {
        $this->endpoint = $endpoint;

        if (null === $client) {
            $client = new Client($this);
        }

        $client->setApi($this);
        $this->client = $client;
    }

    /**
     * Return the http client in use.
     *
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Return the endpoint used by this API.
     *
     * @return string
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * Set the credentials to authenticate with the cloudControl API.
     *
     * @param string $email
     * @param string $password
     *
     * @return Api $this
     */
    public function setCredentials($email, $password)
    {
        $this->email = $email;
        $this->password = $password;

        $this->token = $this->getToken();

        return $this;
    }

    /**
     * Set the application to use for further API calls.
     *
     * @param string $application
     *
     * @return Api $this
     */
    public function setApplication($application)
    {
        $this->application = $application;

        return $this;
    }

    /**
     * Set the deployment to use for further API calls.
     *
     * @param string $deployment
     *
     * @return Api $this
     */
    public function setDeployment($deployment)
    {
        $this->deployment = $deployment;

        return $this;
    }

    /**
     * Check wether this API has a Token assigned.
     *
     * @return bool
     */
    public function hasToken()
    {
        return (null !== $this->token and $this->token->isAuthenticated());
    }

    /**
     * Remove the old Token and create a new one.
     *
     * @retrun Token The new token.
     */
    public function refreshToken()
    {
        $this->token = null;

        return $this->getToken();
    }

    /**
     * Return the authorization token.
     *
     * If none is given, a new one will be created.
     *
     * @return Token
     */
    public function getToken()
    {
        if (null === $this->token) {
            $this->token = new Token($this->getClient(), $this->email, $this->password);
            $this->token->authenticate();
        }

        return $this->token;
    }

    /**
     * Return a list of workers currently running.
     *
     * @return array of Worker
     */
    public function getWorkersList()
    {
        $this->validateDeployment();

        $url = sprintf('/app/%s/deployment/%s/worker/', $this->application, $this->deployment);
        $response = $this->getClient()->get($url);
        $data = $this->decodeResponse($response);

        /*
         * The list only contains the worker id and the creation date.
         * So we request the details for each worker in the list.
         */
        $workers = array();
        foreach ($data as $entry) {
            try {
                $workers[] = $this->getWorker($entry['wrk_id']);
            } catch (GoneException $e) {
                /*
                 * The worker has finished between the two requests.
                 */
            }
        }

        return $workers;
    }

    /**
     * Return the details information for one worker.
     *
     * @param string $workerId
     *
     * @return Worker
     */
    public function getWorker($workerId)
    {
        $url = sprintf('/app/%s/deployment/%s/worker/%s/', $this->application, $this->deployment, $workerId);
        $workerResponse = $this->getClient()->get($url);

        return new Worker($this->decodeResponse($workerResponse));
    }

    /**
     * Start a worker on cloudControl.
     *
     * @param string $command
     * @param string $parameters
     *
     * @return string
     */
    public function addWorker($command, $parameters)
    {
        $this->validateDeployment();

        $url = sprintf('/app/%s/deployment/%s/worker/', $this->application, $this->deployment);
        $data = array(
            'command' => $command,
            'params' => $parameters,
        );

        $response = $this->getClient()->post($url, array(), $data);
        return new Worker($this->decodeResponse($response));
    }

    /**
     * Stop a worker on cloudControl.
     *
     * @param string $workerId
     *
     * @return bool
     */
    public function removeWorker($workerId)
    {
        $this->validateDeployment();

        $url = sprintf('/app/%s/deployment/%s/worker/%s/', $this->application, $this->deployment, $workerId);
        return (204 === $this->getClient()->delete($url)->getStatusCode());
    }

    /**
     * Return the timezone of all dates used within the API.
     *
     * @return \DateTimeZone
     */
    static public function getTimezone()
    {
        return new \DateTimeZone('UTC');
    }

    /**
     * Decode the response content.
     *
     * @param \Buzz\Message\Response $response
     *
     * @return array
     */
    protected function decodeResponse(\Buzz\Message\Response $response)
    {
        $decoder = DecoderFactory::createByContentType($response->getHeader('Content-Type'));

        return $decoder->decode($response->getContent());
    }

    /**
     * Validate an application is set.
     *
     * @throws ApplicationMissingException
     *
     * @return Api $this
     */
    protected function validateApplication()
    {
        if (empty($this->application)) {
            throw new ApplicationMissingException();
        }

        // A deployment also requires an application.
        $this->validateApplication();

        return $this;
    }

    /**
     * Validate a deployment is set.
     *
     * @throws DeploymentMissingException
     *
     * @return Api $this
     */
    protected function validateDeployment()
    {
        if (empty($this->deployment)) {
            throw new DeploymentMissingException();
        }

        return $this;
    }
}
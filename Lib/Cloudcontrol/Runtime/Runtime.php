<?php

namespace Pwx\DeployBundle\Lib\Cloudcontrol\Runtime;

use Pwx\DeployBundle\Lib\Cloudcontrol\Decoder\DecoderFactory;
use Pwx\DeployBundle\Lib\Cloudcontrol\Exception\InvalidArgumentException;
use Pwx\DeployBundle\Lib\Cloudcontrol\Exception\RuntimeException;

class Runtime
{
    /**
     * Return the name of the current application.
     *
     * @return string
     */
    static public function getApplicationName()
    {
        list($applicationName,) = explode('/', getenv('DEP_NAME'));

        return $applicationName;
    }

    /**
     * Return the name of the current deployment.
     *
     * @return string
     */
    static public function getDeploymentName()
    {
        list(, $deploymentName) = explode('/', getenv('DEP_NAME'));

        return $deploymentName;
    }

    /**
     * Return the unique identifier for the current deployment.
     *
     * @return string
     */
    static public function getDeploymentId()
    {
        return getenv('DEP_ID');
    }

    /**
     * Return the currently deployed version.
     *
     * @return string
     */
    static public function getDeployedVersion()
    {
        return getenv('DEP_VERSION');
    }

    /**
     * Return the temporary directory configured.
     *
     * @return string
     */
    static public function getTempDir()
    {
        return getenv('TMP_DIR');
    }

    /**
     * Return the credential configuration for a given addon.
     *
     * @throws InvalidArgumentException
     * @throws DecodingException
     *
     * @param string $addon The name of the addon to return credentials for.
     * @param bool $required Whether the credential information are required to be loaded.
     *
     * @return array The credential information.
     */
    static public function getCredentials($addon, $required = true)
    {
        // Lazy loaded and never loaded twice.
        static $content = null;

        if (null === $content) {
            $content = @file_get_contents($filename = getenv('CRED_FILE'), false);

            if (false === $content) {
                // @codeCoverageIgnoreStart
                throw new RuntimeException(sprintf('Could not load content of file "%s".', $filename));
                // @codeCoverageIgnoreEnd
            }

            $content = DecoderFactory::createByName('json')->decode($content);
        }

        if (empty($content[$addon])) {
            if ($required) {
                throw new InvalidArgumentException(sprintf('There is no configuration for addon "%s".', $addon));
            }

            return array();
        }

        return $content[$addon];
    }
}

<?php

namespace Pwx\DeployBundle\Lib\Cloudcontrol\Runtime;

use Pwx\DeployBundle\Lib\Cloudcontrol\Exception\RuntimeException;

class Worker extends Runtime
{
    /**
     * The interrupt sent to a worker before it will be killed.
     *
     * This signal can be caught and offers the chance on a graceful shutdown.
     * If this signal will be ignored, SIGKILL will follow.
     *
     * @var int
     */
    const INTERRUPT = SIGTERM;

    /**
     * The worker has been completed successfully.
     *
     * @var int
     */
    const RETURN_CODE_NO_ERROR = 0;

    /**
     * The worker encountered an error and shall be restarted.
     *
     * @var int
     */
    const RETURN_CODE_ERROR_RESTART = 1;

    /**
     * The worker has encountered an error and shall shut down.
     *
     * @var int
     */
    const RETURN_CODE_ERROR = 2;

    /**
     * Check whether the given code is a valid return code on cloudControl.
     *
     * @param int $code
     *
     * @return bool
     */
    static public function isValidReturnCode($code)
    {
        $codes = array(
            self::RETURN_CODE_NO_ERROR,
            self::RETURN_CODE_ERROR_RESTART,
            self::RETURN_CODE_ERROR,
        );

        return in_array($code, $codes, true);
    }

    /**
     * Check whether the execution is within the worker environment.
     *
     * @return bool
     */
    static public function isWorker()
    {
        $workerId = getenv('WRK_ID');

        return !empty($workerId);
    }

    /**
     * Return the worker id of the current process.
     *
     * @uses Worker::isWorker
     *
     * @throws RuntimeException If no valid worker.
     *
     * @return string
     */
    static public function getWorkerId()
    {
        if (!self::isWorker()) {
            throw new RuntimeException('The current process is no cloudControl worker.');
        } else {
            return getenv('WRK_ID');
        }
    }
}

<?php

namespace Pwx\DeployBundle\Tests\Lib\Cloudcontrol\Runtime;

use Pwx\DeployBundle\Lib\Cloudcontrol\Runtime\Worker;

class WorkerTest extends RuntimeTest
{
    public static function setUpBeforeClass()
    {
        $vars = array(
            'WRK_ID',
        );

        foreach ($vars as $key) {
            static::$env[$key] = (string) getenv($key);
        }

        parent::setUpBeforeClass();
    }

    public function returnCodesProvider()
    {
        return array(
            array(-1, false),
            array(0, true),
            array(1, true),
            array(2, true),
            array(3, false),
            array('abc', false),
        );
    }

    /**
     * @dataProvider returnCodesProvider
     */
    public function testReturnCodes($code, $expected)
    {
        $this->assertEquals($expected, Worker::isValidReturnCode($code));
    }

    public function testReadEnvironment()
    {
        putenv('WRK_ID=wrk42gjdanx');
        $this->assertTrue(Worker::isWorker());
        $this->assertEquals('wrk42gjdanx', Worker::getWorkerId());

        putenv('WRK_ID=');
        $this->assertFalse(Worker::isWorker());

        $this->setExpectedException('Pwx\DeployBundle\Lib\Cloudcontrol\Exception\RuntimeException');
        Worker::getWorkerId();
    }
}

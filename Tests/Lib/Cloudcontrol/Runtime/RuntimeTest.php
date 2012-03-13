<?php

namespace Pwx\DeployBundle\Tests\Lib\Cloudcontrol\Runtime;

use Pwx\DeployBundle\Lib\Cloudcontrol\Runtime\Runtime;
use Pwx\DeployBundle\Lib\Cloudcontrol\Runtime\Addon;

class RuntimeTest extends \Pwx\DeployBundle\Tests\Lib\Cloudcontrol\AbstractTest
{
    protected static $env = array();

    public static function setUpBeforeClass()
    {
        $vars = array(
            'DEP_NAME',
            'DEP_VERSION',
            'TMP_DIR',
            'DEP_ID',
            'CRED_FILE',
        );

        foreach ($vars as $key) {
            static::$env[$key] = (string) getenv($key);
        }

        parent::setUpBeforeClass();
    }

    public static function tearDownAfterClass()
    {
        foreach (static::$env as $key => $value) {
            putenv(sprintf("%s=%s", $key, $value));
        }

        parent::tearDownAfterClass();
    }

    public function testReadEnvironment()
    {
        putenv('DEP_NAME=myapplication/mydeployment');
        $this->assertEquals('myapplication', Runtime::getApplicationName());
        $this->assertEquals('mydeployment', Runtime::getDeploymentName());

        // bzr is increasing number
        putenv('DEP_VERSION=12345');
        $this->assertEquals('12345', Runtime::getDeployedVersion());
        // git is hash versioned
        putenv('DEP_VERSION=abc123d');
        $this->assertEquals('abc123d', Runtime::getDeployedVersion());

        putenv('TMP_DIR='.sys_get_temp_dir());
        $this->assertEquals(sys_get_temp_dir(), Runtime::getTempDir());

        putenv('DEP_ID=depamsku6vd');
        $this->assertEquals('depamsku6vd', Runtime::getDeploymentId());
    }

    public function testCredentialFile()
    {
        $filename = tempnam(sys_get_temp_dir(), 'php-cctrl-test');
        @unlink($filename);

        $mysqlDedicatedConfig = array(
            Addon\MysqlDedicated::PARAM_DATABASE => 'databasename',
            Addon\MysqlDedicated::PARAM_USER     => 'username',
            Addon\MysqlDedicated::PARAM_PASSWORD => 'password',
            Addon\MysqlDedicated::PARAM_SERVER   => 'serveraddress',
        );
        $json = json_encode(array(
           Addon\MysqlDedicated::NAME => $mysqlDedicatedConfig,
        ));
        file_put_contents($filename, $json);
        $this->assertEquals($json, file_get_contents($filename));

        putenv('CRED_FILE='.$filename);
        $this->assertEquals($mysqlDedicatedConfig, Runtime::getCredentials(Addon\MysqlDedicated::NAME));
        $this->assertEquals(array(), Runtime::getCredentials('MONGODB', false));

        @unlink($filename);

        $this->setExpectedException('Pwx\DeployBundle\Lib\Cloudcontrol\Exception\InvalidArgumentException');
        Runtime::getCredentials('MONGODB');
    }
}

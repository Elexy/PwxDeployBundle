<?php

namespace Pwx\DeployBundle\Test\Lib;

use Pwx\DeployBundle\Lib\CredFile;

/**
 * Description of CredFileTest
 *
 * @author alex
 */
class CredFileTest extends \PHPUnit_Framework_TestCase
{

  public function testCredentialFile()
  {
    $filename = tempnam(sys_get_temp_dir(), 'pjx-credfile-test');
    @unlink($filename);

    $mysqlConfig = array(
        'MYSQLS_DATABASE' => 'databasename',
        'MYSQLS_USERNAME' => 'username',
        'MYSQLS_PASSWORD' => 'password',
        'MYSQLS_HOSTNAME' => 'serveraddress',
    );
    $json = json_encode(array(
        'MYSQLS' => $mysqlConfig,
            ));
    file_put_contents($filename, $json);
    $this->assertEquals($json, file_get_contents($filename));

    putenv('CRED_FILE=' . $filename);
    $this->assertEquals($mysqlConfig, CredFile::getCredentials());

    @unlink($filename);
  }

}

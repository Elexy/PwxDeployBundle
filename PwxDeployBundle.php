<?php

namespace Pwx\DeployBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
//use Pwx\DeployBundle\PwxDeployBundle\Lib\CredFile;

class PwxDeployBundle extends Bundle
{

  /**
   * Override settings is present
   */
  public function boot()
  {
    $credFile = new Lib\CredFile();
    if ( $credentials = $credFile->getCredentials() )
    {
      $propelConfig = \Propel::getConfiguration();

      $dsn = $propelConfig['datasources']['default']['connection']['dsn'];
      $patterns = array('/host=(.*);dbname/', '/;dbname=(.*);charset/');
      $replaces = array(
          'host=' . $credentials['MYSQL_HOSTNAME'] . ';dbname',
          ';dbname=' . $credentials['MYSQL_DATABASE'] . ';charset');
      $newDsn = preg_replace($patterns, $replaces, $dsn);
      // "mysql:host=localhost;dbname=projectx;charset=UTF8"

      $propelConfig['datasources']['default']['connection']['user'] = $credentials['MYSQL_USERNAME'];
      $propelConfig['datasources']['default']['connection']['password'] = $credentials['MYSQL_PASSWORD'];
      $propelConfig['datasources']['default']['connection']['dsn'] = $newDsn;

      \Propel::setConfiguration($propelConfig);
    }
  }

}

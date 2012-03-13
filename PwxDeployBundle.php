<?php

namespace Pwx\DeployBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Pwx\DeployBundle\Lib\Cloudcontrol\Runtime\Runtime;

class PwxDeployBundle extends Bundle
{

  /**
   * Override settings is present
   */
  public function boot()
  {
    $addonArray = array('MYSQLS');
    //foreach ($this->container->getParameter('pwx_deploy_addons') as $addon)
    foreach ($addonArray as $addon)
    {
      if ( Runtime::hasCredentials($addon)) {
        if ( $credentials = Runtime::getCredentials($addon) )
        {
          $propelConfig = \Propel::getConfiguration();

          $dsn = $propelConfig['datasources']['default']['connection']['dsn'];
          $patterns = array('/host=(.*);dbname/', '/;dbname=(.*);charset/');
          $replaces = array(
              'host=' . $credentials['MYSQLS_HOSTNAME'] . ';dbname',
              ';dbname=' . $credentials['MYSQLS_DATABASE'] . ';charset');
          $newDsn = preg_replace($patterns, $replaces, $dsn);
          // "mysql:host=localhost;dbname=projectx;charset=UTF8"

          $propelConfig['datasources']['default']['connection']['user'] = $credentials['MYSQLS_USERNAME'];
          $propelConfig['datasources']['default']['connection']['password'] = $credentials['MYSQLS_PASSWORD'];
          $propelConfig['datasources']['default']['connection']['dsn'] = $newDsn;

          \Propel::setConfiguration($propelConfig);
        }
      }
    }
  }

}

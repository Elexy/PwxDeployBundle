<?php

namespace Pwx\DeployBundle\Lib;

/**
 *
 * Every deployment gets different credentials for each Add-on. Providers
 * can change these credentials at any time. It is therefor required to read
 * the credentials from the provided JSON file to keep the application running
 * in case the credentials change.
 *
 * The path to the JSON file can be found in the CRED_FILE environment variable.
 *
 */
class CredFile
{

  public static function getCredentials()
  {
    # read the credentials file
    if ( isset($_ENV['CRED_FILE']) )
    {
      $this->string = file_get_contents($_ENV['CRED_FILE'], false);

      if ( $this->string == false )
      {
        throw new \Exception('FATAL: Could not read credentials file');
      }
      # the file contains a JSON string, decode it and return an associative array
      $creds = json_decode($this->string, true);

      # use credentials to set the configuration for MySQL
      return array(
          'MYSQL_HOSTNAME' => $creds['MYSQLS']['MYSQLS_HOSTNAME'],
          'MYSQL_DATABASE' => $creds['MYSQLS']['MYSQLS_DATABASE'],
          'MYSQL_USERNAME' => $creds['MYSQLS']['MYSQLS_USERNAME'],
          'MYSQL_PASSWORD' => $creds['MYSQLS']['MYSQLS_PASSWORD']
      );
    }
  }
}

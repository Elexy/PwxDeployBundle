<?php

namespace Pwx\DeployBundle\Tests\Lib\Cloudcontrol;

abstract class AbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Return the directory of all fixtures.
     *
     * @return string
     */
    public static function getFixturesDir()
    {
        return dirname(__FILE__) . '/Fixtures/';
    }

    /**
     * Return the content of the given fixtures file.
     *
     * @param string $filename
     *
     * @return string
     */
    public static function getFixtureContent($filename)
    {
        return file_get_contents(self::getFixturesDir() . $filename);
    }
}
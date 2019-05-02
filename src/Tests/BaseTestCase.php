<?php
/**
 * Created by PhpStorm.
 * User: newsy
 * Date: 30/04/2019
 * Time: 20:39
 */

namespace Framework\Tests;

use PHPUnit\Framework\TestCase;
use Framework\Application\Settings;
use Framework\Application;
use Framework\Application\UtilitiesV2\Debug;

class BaseTestCase extends TestCase
{

    /**
     * @var Application
     */

    protected static $application;

    /**
     * Saves some time when setting the env ready to test. Just need to preload settings and set document root
     */

    public static function setUpBeforeClass(): void
    {


        Debug::setCMD();
            include( "../index.php");
        Settings::preloadSettings();

        self::$application = new Application( false );
        self::$application->addToGlobalContainer();

        parent::setUpBeforeClass();
    }
}
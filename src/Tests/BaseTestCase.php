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

class BaseTestCase extends TestCase
{

    /**
     * Saves some time when setting the env ready to test. Just need to preload settings and set document root
     */

    public static function setUpBeforeClass(): void
    {

        $_SERVER["DOCUMENT_ROOT"] = "E:\/Webservers\/XAMPP\/htdocs";
        Settings::preloadSettings();


        parent::setUpBeforeClass();
    }
}
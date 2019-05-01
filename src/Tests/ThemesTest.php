<?php
/**
 * Created by PhpStorm.
 * User: newsy
 * Date: 30/04/2019
 * Time: 20:39
 */

namespace Framework\Tests;

use Framework\Syscrack\Game\Themes;

class ThemesTest extends BaseTestCase
{

    /**
     * @var Themes
     */

    protected static $themes;

    public static function setUpBeforeClass(): void
    {

        self::$themes = new Themes();
        parent::setUpBeforeClass();
    }

    /**
     * Tests the folder get function
     */

    public function testGetFolders()
    {

        $results = self::$themes->getFolders();

        $this->assertNotEmpty( $results );

        print_r( $results );
    }
}

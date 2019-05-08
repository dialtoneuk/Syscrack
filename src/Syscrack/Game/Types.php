<?php
/**
 * Created by PhpStorm.
 * User: newsy
 * Date: 05/05/2019
 * Time: 21:30
 */

namespace Framework\Syscrack\Game;

use Framework\Application\Settings;
use Framework\Application\Utilities\FileSystem;

class Types
{

    /**
     * @var Computer
     */

    protected static $computer;

    /**
     * Types constructor.
     */

    public function __construct()
    {

        if( isset( self::$computer ) == false )
            self::$computer = new Computer();
    }

    /**
     * @return mixed
     */

    public function get()
    {

        if( FileSystem::fileExists( Settings::setting("computer_types_filepath") ) == false )
            $this->generate();

        return ( FileSystem::readJson( Settings::setting("computer_types_filepath") ) );
    }

    /**
     * Generates the types
     */

    public function generate()
    {

        $types = [];

        foreach( self::$computer->getComputerClasses() as $class )
        {

            if( $class instanceof \Framework\Syscrack\Game\Structures\Computer == false )
                continue;

            $types[] = $class->configuration()["type"];
        }

        FileSystem::writeJson( Settings::setting("computer_types_filepath"), $types );
    }
}
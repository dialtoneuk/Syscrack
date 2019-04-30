<?php
/**
 * Created by PhpStorm.
 * User: newsy
 * Date: 30/04/2019
 * Time: 19:11
 */

namespace Framework\Syscrack\Game;

use Framework\Application\Settings;
use Framework\Application\Utilities\FileSystem;
use Framework\Exceptions\ApplicationException;

class Themes
{

    public function gather( $folders )
    {


    }

    public function getFolders()
    {

        if( FileSystem::directoryExists( Settings::getSetting("syscrack_view_location") ) == false )
            throw new ApplicationException("Themes folder does not exist");

        return( FileSystem::getDirectories( Settings::getSetting("syscrack_view_location")  ) );
    }
}
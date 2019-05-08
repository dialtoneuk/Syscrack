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

class BrowserPages
{

    /**
     * @return mixed
     */

    public function get()
    {

        if( FileSystem::fileExists( Settings::setting("browser_pages_filepath") ) == false )
            $this->generate();

        return ( FileSystem::readJson( Settings::setting("browser_pages_filepath") ) );
    }

    /**
     * Generates the types
     */

    public function generate()
    {

        $files = FileSystem::getFilesInDirectory( Settings::setting("browser_pages_root" ) );

        foreach( $files as $key=>$item )
            $files[ $key ] = FileSystem::getFileName( $item );

        FileSystem::writeJson( Settings::setting("browser_pages_filepath"), $files );
    }
}
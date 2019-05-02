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
use Framework\Application\UtilitiesV2\Conventions\ThemeData;
use Framework\Exceptions\ApplicationException;

class Themes
{

    protected $themes;

    public function __construct( $autoread=false )
    {

        if( $autoread )
            $this->getThemes();
    }

    public function currentTheme()
    {

        return( Settings::getSetting("render_folder") );
    }

    public function set( $theme )
    {

        if( $this->themeExists( $theme ) == false )
            throw new \Error("Theme does not exist: " . $theme );

        if( $this->currentTheme() == $theme )
            throw new \Error("Theme already set to: " . $theme );

        if( $this->requiresMVC( $theme ) && $this->mvcOutput() == false )
            Settings::updateSetting("render_mvc_output", true );

        Settings::updateSetting("render_folder", $theme );
    }

    /**
     * @return bool
     */

    public function mvcOutput() : bool
    {

        return( (bool)Settings::getSetting( "render_mvc_output" ) );
    }

    public function requiresMVC( $theme )
    {

        $data = $this->getData( $theme );

        if( empty( $data ) )
            return false;
        elseif( $data["mvc"] )
            return true;

        return false;
    }

    public function getData( $theme )
    {

        return( $this->themes[ $theme ]["data"] );
    }

    public function modifyInfo( $theme, ThemeData $object )
    {

        FileSystem::writeJson( Settings::getSetting("syscrack_view_location")
            . $theme
            . Settings::getSetting("theme_info_file "), $object->contents() );
    }

    public function getTheme( $theme, $object=true )
    {

        if( $this->themeExists( $theme ) == false )
            throw new \Error("Theme does not exist: " . $theme );

        $themes = $this->getThemes( false );

        if( $object )
            return self::dataInstance( $themes[ $theme ] );
        else
            return(  $themes[ $theme ] );
    }

    public function themeExists( $theme )
    {

        return( isset( $this->themes[ $theme ] ) );
    }

    public function getThemes( $read=true )
    {

        if( $read )
        {

            $result = $this->gather( $this->getFolders() );

            if( empty( $result ) )
                throw new \Error("No theme information found please check your theme directories");

            $this->themes = $result;
            return( $result );
        }
        elseif( empty( $this->themes ) )
            throw new \Error("No theme information cached");
        else
            return( $this->themes );
    }

    public function gather( $folders )
    {

        $info = array();

        foreach( $folders as $folder )
            $info[ $folder ] = FileSystem::readJson( Settings::getSetting("syscrack_view_location")
                . $folder
                . Settings::getSetting("theme_info_file") );

        return( $info );
    }

    public function getFolders()
    {

        if( FileSystem::directoryExists( Settings::getSetting("syscrack_view_location") ) == false )
            throw new ApplicationException("Themes folder does not exist");

        return( FileSystem::getDirectories( Settings::getSetting("syscrack_view_location")  ) );
    }

    /**
     * @param array $values
     * @return ThemeData
     */

    public static function dataInstance( array $values )
    {

        return( new ThemeData( $values ) );
    }
}
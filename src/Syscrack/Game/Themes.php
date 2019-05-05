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

    /**
     * @var array
     */

    protected $themes;

    /**
     * Themes constructor.
     * @param bool $autoread
     */

    public function __construct( $autoread=false )
    {

        if( $autoread )
            $this->getThemes();
    }

    /**
     * @return mixed
     */

    public function currentTheme()
    {

        return( Settings::getSetting("render_folder") );
    }

    /**
     * @param $theme
     */

    public function set( $theme )
    {

        if( $this->themeExists( $theme ) == false )
            throw new \Error("Theme does not exist: " . $theme );

        if( $this->currentTheme() == $theme )
            throw new \Error("Theme already set to: " . $theme );

        if( $this->requiresMVC( $theme ) && $this->mvcOutput() == false )
            Settings::updateSetting("render_mvc_output", true );
        elseif( $this->requiresMVC( $theme ) == false && $this->mvcOutput() )
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

    /**
     * @param $theme
     * @return bool
     */

    public function requiresMVC( $theme )
    {

        $data = $this->getData( $theme );

        if( empty( $data ) )
            return false;
        elseif( $data["mvc"] )
            return true;

        return false;
    }

    /**
     * @param $theme
     * @return mixed
     */

    public function getData( $theme )
    {

        return( $this->themes[ $theme ]["data"] );
    }

    /**
     * @param $theme
     * @param ThemeData $object
     */

    public function modifyInfo( $theme, ThemeData $object )
    {

        FileSystem::writeJson( $this->path( $theme ), $object->contents() );
    }

    /**
     * @param $theme
     * @param bool $object
     * @return ThemeData|mixed
     */

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

    /**
     * @param $theme
     * @return bool
     */

    public function themeExists( $theme )
    {

        return( isset( $this->themes[ $theme ] ) );
    }

    /**
     * @param bool $read
     * @return array
     */

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

    /**
     * @param $folders
     * @return array
     */

    public function gather( $folders )
    {

        $info = array();

        foreach( $folders as $folder )
            $info[ $folder ] = FileSystem::readJson(
                $this->path( $folder )
            );

        return( $info );
    }

    /**
     * @return array|false|null
     */

    public function getFolders()
    {

        if( FileSystem::directoryExists( Settings::getSetting("syscrack_view_location") ) == false )
            throw new ApplicationException("Themes folder does not exist");

        return( FileSystem::getDirectories( Settings::getSetting("syscrack_view_location")  ) );
    }

    /**
     * @param null $folder
     * @return string
     */

    public function path( $folder=null )
    {

        return(FileSystem::separate(
            Settings::getSetting("syscrack_view_location"),
            $folder,
            Settings::getSetting("theme_info_file")
        ));
    }

    /**
     * @param array $values
     * @return ThemeData
     */

    public static function dataInstance( $values )
    {

        return( new ThemeData( $values ) );
    }
}
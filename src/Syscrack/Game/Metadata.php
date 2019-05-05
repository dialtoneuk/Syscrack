<?php
/**
 * Created by PhpStorm.
 * User: newsy
 * Date: 05/05/2019
 * Time: 13:29
 */

namespace Framework\Syscrack\Game;

use Framework\Application\Settings;
use Framework\Application\Utilities\FileSystem;
use Framework\Application\UtilitiesV2\Conventions\ComputerData;

class Metadata
{

    /**
     * @var array
     */

    protected static $cache = [];

    /**
     * @param $computerid
     * @param array $values
     */

    public function update( $computerid, array $values )
    {

        $object = $this->get( $computerid );
        $object = $object->contents();

        foreach( $object as $item=>$value )
            if( isset( $values[ $item ] ) )
                $object[ $item ] = $values[ $item ];

        $object = self::dataInstance( $object );
        $this->write( $computerid, serialize( $object->contents() ) );
    }

    /**
     * @param $computerid
     * @param array|null $values
     */

    public function create( $computerid, array $values=null )
    {

        $object = self::dataInstance( $values, true );

        if( $object === false )
            throw new \Error("Incorrect values given to create metadata: "
                . print_r( $values )
                . " and expecting => "
                . print_r( $object->getRequirements() ) );

        $this->write( $computerid, serialize( $object->contents() ) );
    }

    /**
     * @param $computerid
     */

    public function delete( $computerid )
    {

        if( $this->isCached( $computerid ) )
            self::$cache[ $computerid ] = null;

        FileSystem::delete( $this->path( $computerid ) );
    }

    /**
     * @param $computerid
     * @param bool $force
     * @param bool $cache
     * @return ComputerData
     */

    public function get( $computerid, $force=false, $cache=true ) : ComputerData
    {

        if( $force == false  )
            if( $this->isCached( $computerid ) )
                return( self::$cache[ $computerid ] );

        $object = self::dataInstance( unserialize( $this->read( $computerid ) ) );

        if( $cache )
            return( $this->cache( $computerid, $object ) );

        return( $object );
    }

    /**
     * @param $computerid
     * @return bool
     */

    public function exists( $computerid )
    {

        return( FileSystem::fileExists( $this->path( $computerid ) ) );
    }

    /**
     * @param null $computerid
     * @return string
     */

    public function path( $computerid=null )
    {

        return( FileSystem::separate( Settings::getSetting("metadata_filepath"), $computerid . ".db" ) );
    }

    /**
     * @param $computerid
     * @param $data
     */

    private function write( $computerid, $data )
    {
        FileSystem::write( $this->path( $computerid), $data );
    }

    /**
     * @param $computerid
     * @return string
     */

    private function read( $computerid )
    {

        return( FileSystem::read( $this->path( $computerid ) ) );
    }

    /**
     * @param $computerid
     * @return bool
     */

    private function isCached( $computerid )
    {

        if( isset( self::$cache[ $computerid ] ) )
            if( empty( self::$cache[ $computerid ] ) == false  )
                return true;

        return false;
    }

    /**
     * @param $key
     * @param $object
     * @return mixed
     */

    private function cache( $key, $object )
    {

        self::$cache[ $key ] = $object;
        return( $object );
    }

    /**
     * @param $name
     * @param $type
     * @param array $software
     * @param array $hardware
     * @param array $custom
     * @return array
     */

    public static function generateData( $name, $type, array $software = [], array $hardware = [], array $custom = [] )
    {

        $data = [];
        $data["name"]       = $name;
        $data["type"]       = $type;
        $data["info"]       = [
            "reset" => microtime( true ),
            "created" => microtime( true )
        ];
        $data["software"]   = $software;
        $data["hardware"]   = $hardware;
        $data["custom"]     = $custom;

        return( $data );
    }

    /**
     * @param $values
     * @param bool $surpress_errors
     * @return bool|ComputerData
     */

    private static function dataInstance( $values, $surpress_errors = true )
    {

        if( $surpress_errors )
            try
            {

                return( new ComputerData( $values ) );
            }
            catch( \Error $errors )
            {

                return false;
            }

        return( new ComputerData( $values ) );
    }
}
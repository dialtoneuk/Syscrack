<?php

namespace Framework\Application\UtilitiesV2;
use Framework\Application\UtilitiesV2\Conventions\AmbiguousData;

/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 31/08/2018
 * Time: 01:11
 */

abstract class Convention
{

    protected $array = [];

    /**
     * @var array
     */

    protected $requirements = [
    ];

    /**
     * Convention constructor.
     * @param array $array
     */

    public function __construct( array $array )
    {

        if( $this->parse( $array ) == false )
            throw new \RuntimeException("invalid array given to convention, does not meet requirements");

        $this->array = $array;
    }

    /**
     * @param $name
     * @return bool
     */

    public function __isset($name)
    {

        return( $this->exist( $name ) );
    }

    /**
     * @param $name
     * @return mixed
     */

    public function __get($name)
    {

        return( $this->get( $name ) );
    }

    /**
     * @param $key
     * @return mixed
     */

    public function get( $key )
    {

        return( $this->array[ $key ] );
    }

    /**
     * @param $key
     * @param $index
     * @return bool|null
     */

    public function query( $key, $index )
    {

        if( isset( $this->array[ $key ] ) == false )
            return null;

        if( is_array( $this->array[ $key ] ) == false )
            return null;

        if( isset( $this->array[ $key ][$index ] ) )
            return true;

        return false;
    }

    /**
     * @param $key
     * @param $index
     * @return mixed
     */

    public function arrayValue( $key, $index )
    {

        return( $this->array[ $key ][ $index ] );
    }

    /**
     * @param $key
     * @param $index
     * @return bool|null
     */

    public function isTrueAndExists( $key, $index )
    {

        $result = $this->query( $key, $index );

        if( $result == null )
            return false;

        if( is_bool( $result ) )
            return false;

        return( $result );
    }

    /**
     * @param $key
     * @return bool
     */

    public function exist( $key )
    {

        return( isset( $this->array[ $key ] ) );
    }

    /**
     * @param $value
     * @return bool
     */

    public function contains( $value )
    {

        foreach( $this->array as $key=>$item )
            if( $item == $value )
                return true;

        return false;
    }

    public function contents()
    {

        return( $this->array );
    }

    /**
     * @param $value
     * @return int|null|string
     */

    public function indexOf( $value )
    {

        foreach( $this->array as $key=>$item )
            if( $item == $value )
                return $key;

        return null;
    }

    /**
     * @param $array
     * @return bool
     */

    private function parse( $array )
    {

        //Empty requirements kinda defaults the point
        if( empty( $this->requirements ) )
            return true;

        foreach( $this->requirements as $key=>$requirement )
        {

            if( isset( $array[$key] ) == false )
                return false;
            else
            {

                switch ( $requirement )
                {

                    case "array":
                        if( is_array( $array[ $key ] ) == false )
                            return false;
                        break;
                    case "string":
                        if( is_string( $array[ $key ] ) == false )
                            return false;
                        break;
                    case "bool"||"boolean":
                        //Todo: fix
                        break;
                    case "int"||"integer":
                        if( is_int( $array[ $key ] ) == false )
                            return false;
                        break;
                    default:
                        if( is_null( $array[ $key ] ) == false )
                            return false;
                        break;
                }
            }
        }

        return true;
    }

    /**
     * @param Convention $convention
     * @return AmbiguousData
     */

    public static function toAmbigious( Convention $convention )
    {

        if( $convention instanceof AmbiguousData )
            return $convention;

        return( new AmbiguousData( $convention->contents() ) );
    }
}
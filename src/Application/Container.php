<?php
namespace Framework\Application;

/**
 * Lewis Lancaster 2016
 *
 * Class Container
 *
 * @package Framework
 */

use Framework\Application;
use Framework\Database\Manager;
use Framework\Exceptions\ApplicationException;

class Container
{

	/**
	 * @var array
	 */

	protected static $objects = array();

	/**
	 * Gets all the objects
	 *
	 * @return array
	 */

	public static function getObjects()
	{

		if( empty( self::$objects ) )
		{

			throw new ApplicationException();
		}

		return self::$objects;
	}

	/**
	 * Empties an object
	 */

	public static function emptyObjects()
	{

		self::$objects = array();
	}

	/**
	 * Sets an object
	 *
	 * @param $index
	 *
	 * @param $value
	 */

	public static function setObject( $index, $value )
	{

		self::$objects[ $index ] = $value;
	}

	/**
	 * Gets an object
	 *
	 * @param $index
	 *
	 * @return Application|mixed|\stdClass|Session|Manager
	 */

	public static function getObject( $index )
	{

		if( isset( self::$objects[ $index ] ) == false )
		{

			throw new ApplicationException('That does not exist');
		}

		return self::$objects[ $index ];
	}

    /**
     * Returns true if we have the index set ( eg the object )
     *
     * @param $index
     *
     * @return bool
     */

	public static function hasObject( $index )
    {

        if( isset( self::$objects[ $index] ) )
        {

            return true;
        }

        return false;
    }

    /**
     * Finds the specific object
     *
     * @param $value
     *
     * @return int|null|string
     */

    public static function findObject( $value )
    {

        foreach( self::$objects as $index=>$object )
        {

            if( $value == $object )
            {

                return $index;
            }
        }

        return null;
    }

	/**
	 * Gets a value
	 *
	 * @param $index
	 */

	public function __get ( $index )
	{

		if( @self::getObject( $index ) == false )
		{

			throw new ApplicationException();
		}

		self::$objects[ $index ];
	}

	/**
	 * Sets a value
	 *
	 * @param $name
	 *
	 * @param $value
	 */

	public function __set( $name, $value )
	{
		
		self::$objects[ $name ] = $value;
	}
}
<?php
namespace Framework\Database;

/**
 * Lewis Lancaster 2016
 *
 * Class Manager
 *
 * @package Framework\Database
 */

use Framework\Application\Container;
use Framework\Exceptions\DatabaseException;
use Illuminate\Database\Capsule\Manager as Capsule;
use Framework\Application\Utilities\Log;

class Manager
{

	/**
	 * @var Capsule
	 */

	public static $capsule;

	/**
	 * @var array
	 */

	public static $connection;

	/**
	 * Manager constructor.
	 */

	public function __construct ( $autoload=true )
	{

	    if( $autoload )
        {

            $this->setConnection();
        }
	}

    /**
     * Sets the database connection
     *
     * @param null $file
     */

	public function setConnection( $file=null )
    {

        $class = new Connection();

        if( empty( $class ) )
        {

            throw new DatabaseException();
        }

        self::$connection = $class->readConnectionFile( $file );

        if( empty( self::$connection ) )
        {

            throw new DatabaseException();
        }

        self::$capsule = new Capsule();

        if( empty( self::$capsule ) )
        {

            throw new DatabaseException();
        }

        $this->createConnection();

        Log::log('Database Connection Created');

    }

	/**
	 * Creates our database connection
	 */

	public function createConnection( $addtocontainer=true )
	{

		self::$capsule->addConnection( self::$connection );

        self::$capsule->setAsGlobal();
	}

    /**
     * @return Capsule
     */

	public static function getCapsule()
	{

		return self::$capsule;
	}
}
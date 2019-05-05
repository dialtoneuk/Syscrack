<?php
namespace Framework\Database;

/**
 * Lewis Lancaster 2016
 *
 * Class Table
 *
 * @package Framework\Database
 */

use Exception;
use Framework\Exceptions\DatabaseException;
use ReflectionClass;

class Table
{

	/**
	 * @var \Illuminate\Database\Capsule\Manager
	 */

	protected $database;

	/**
	 * Checks if we have initialized the database, if we have not, do it
	 *
	 * Table constructor.
	 */

	public function __construct ()
	{

	    try
        {

            if( Manager::getCapsule() === null )
            {

                $this->initializeDatabase();
            }

            $this->database = Manager::getCapsule();
        }
        catch( DatabaseException $error )
        {


        }
	}

    /**
     * @param null $table
     * @return \Illuminate\Database\Query\Builder
     *
     */

	final protected function getTable( $table=null )
	{

	    try
        {

            if( $table === null )
                $table = strtolower( self::getShortName( new ReflectionClass( $this ) ) );

            if( $this->database->table( $table )->exists() == false )
            {
                if( $this->database->table( $table )->exists() == false )
                {
                    try {

                        $this->database->table($table)->get();
                    } catch (Exception $error) {

                        throw new DatabaseException();
                    }

                    return $this->database->table($table);
                }
                else

                    return $this->database->table( $table );
            }
            else
                return $this->database->table( $table );

        }
        catch ( \ReflectionException $exception )
        {

            throw new DatabaseException();
        }
	}

	/**
	 * If this table has a database connection or not
	 *
	 * @return bool
	 */

	final private function hasConnection()
	{

		try
		{

			Manager::getCapsule()->connection();
		}
		catch( Exception $error )
		{

			return false;
		}

		return true;
	}

	/**
	 * Intializes a connection with the database
	 */

	final private function initializeDatabase()
	{

		$manager = new Manager();

		if( $this->hasConnection() == false )
		{

			throw new DatabaseException();
		}

		unset( $manager );
	}

	/**
	 * Gets the short name of a class
	 *
	 * @param ReflectionClass $reflectionclass
	 *
	 * @return string
	 */

	final private function getShortName( ReflectionClass $reflectionclass )
	{

		return $reflectionclass->getShortName();
	}
}
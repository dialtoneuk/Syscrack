<?php
namespace Framework\Database;

/**
 * Lewis Lancaster 2016
 *
 * Class Connection
 *
 * @package Framework\Database
 */

use Framework\Application\Utilities\Cyphers;
use Framework\Application\Utilities\FileSystem;
use Framework\Exceptions\DatabaseException;

class Connection
{

	/**
	 * @var array
	 */

	protected $connection;

	/**
	 * Connection constructor.
	 */

	public function __construct()
	{

		$connection = $this->readConnectionFile();

		if( empty( $connection ) )
		{

			throw new DatabaseException();
		}
	}

	/**
	 * Reads the connection file of the server
	 *
	 * @param string $file
	 *
	 * @return array
	 */

	public function readConnectionFile( $file = 'conf/database/connection.json' )
	{

		if( file_exists( FileSystem::getFilePath( $file) ) == false )
		{

			throw new DatabaseException();
		}

		$json = FileSystem::read( $file );

		if( empty( $json ) )
		{

			throw new DatabaseException();
		}

		$connection = Cyphers::decryptJsonToArray( $json );

		if( empty( $connection ) )
		{

			throw new DatabaseException();
		}

		return $connection;
	}
}
<?php
namespace Framework\Application\Utilities;

/**
 * Lewis Lancaster 2016
 *
 * Class CommandManager
 *
 * @package Framework\Command
 */

use Framework\Exceptions\ConsoleException;
use Exception;

class CommandManager
{

	/**
	 * @var array
	 */

	protected $arguments;

	/**
	 * Manager constructor.
	 *
	 * @param $arguments
	 */

	public function __construct( $arguments )
	{

		$this->arguments = $arguments;

		if( php_sapi_name() !== 'cli' )
		{

			throw new ConsoleException();
		}
	}

	/**
	 * Handles an error for the console class
	 *
	 * @param Exception $error
	 */

	final public function error( Exception $error )
	{

		die( $this->output( 'CRITICAL ERROR: ' . $error->getMessage() ) );
	}

	/**
	 * Returns true if the commands has arguments
	 *
	 * @return bool
	 */

	final public function hasArguments()
	{

		if( empty( $this->arguments[1] ) )
		{

			return false;
		}

		return true;
	}

	/**
	 * Outputs a string into the console
	 *
	 * @param $text
	 *
	 * @return null
	 */

	final public function output( $text, $prefix='Command Manager' )
	{

		echo "[" . $prefix . "] " . "\n" . $text;

		return null;
	}

	/**
	 * Returns the number of arguments given
	 *
	 * @param null $array
	 *
	 * @return int
	 */

	final public function getArgumentCount( $array=null )
	{

		return count( $this->getArguments( $array ) );
	}

	/**
	 * Gets the arguments of this console commands.
	 *
	 * @param string $seperator
	 *
	 * @param null $array
	 *
	 * @return array
	 */

	final public function getArguments( $array=null, $seperator=':' )
	{

		if( empty( $this->arguments ) )
		{

			throw new ConsoleException('No arguments stored');
		}

		$commands = explode( $seperator, $this->arguments[1] );

		if( empty( $commands ) )
		{

			throw new ConsoleException('Commands are empty');
		}

		if( $array !== null )
		{

			$result = array();

			if( count( $array ) != count( $commands ) )
			{

				throw new ConsoleException('Missmatch in arrays, please enter all information required');
			}

			foreach( $array as $key=>$value )
			{

				$result[ $value ] = $commands[ $key ];
			}

			if( empty( $array ) )
			{

				throw new ConsoleException('Failed to set headers');
			}

			return $result;
		}

		return $commands;
	}
}
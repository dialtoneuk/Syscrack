<?php
namespace Framework\Session;

/**
 * Lewis Lancaster 2016
 *
 * Class Capsule
 *
 * @package Framework\Session
 */

use Framework\Exceptions\SyscrackException;
use Framework\Exceptions\SessionException;
use Framework\Database\Tables\Sessions as Database;

class Capsule
{

	/**
	 * @var Database
	 */

	protected $database;

	/**
	 * Manager constructor.
	 *
	 * @param bool $start
	 */

	public function __construct ( $start=false )
	{

		$this->database = new Database();

		if( $start == true )
		{

			$this->start();
		}
	}

	/**
	 * Starts the session
	 */

	public function start()
	{

		if( $this->isSessionActive() == false )
		{

			throw new SessionException();
		}

		session_start();
	}

	/**
	 * Gets the owner of this session
	 *
	 * @return mixed
	 */

	public function getSessionOwner()
	{

		if( $this->isValid() == false )
		{

			throw new SessionException();
		}

		$result = $this->database->getSession( session_id() );

		if( empty( $result ) )
		{

			throw new SessionException();
		}

		return $result['userid'];
	}

	/**
	 * Returns true if the session is active
	 *
	 * @return bool
	 */

	public function isSessionActive()
	{

		if( session_status() != PHP_SESSION_ACTIVE )
		{

			return false;
		}

		return true;
	}

	/**
	 * Returns true if the session is valid
	 *
	 * @return bool
	 */

	public function isValid()
	{

		if( $this->database->getSession( session_id() ) == null )
		{

			return false;
		}

		return true;
	}

	/**
	 * Adds session to database
	 *
	 * @param $userid
	 *
	 * @return bool
	 */

	public function addToDatabase( $userid )
	{

        if( $this->isSessionActive() == false )
        {

            throw new SyscrackException();
        }

		$array = array(
			'sessionid' => session_id(),
			'userid'    => $userid,
			'useragent' => $_SERVER['HTTP_USER_AGENT'],
			'ipaddress' => $_SERVER['REMOTE_ADDR']
		);

		$this->database->insertSession( $array );

		if( $this->isValid() == false )
		{

			throw new SyscrackException();
		}

		return true;
	}

	/**
	 * Creates a new session
	 */

	public function create()
	{

		if( $this->isSessionActive() == true )
		{

			throw new SessionException();
		}

		$this->regenerate();

		session_start();
	}

	/**
	 * Regenerates the session id
	 */

	public function regenerate()
	{

		session_regenerate_id();
	}
	
}
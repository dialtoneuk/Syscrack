<?php
namespace Framework\Database\Tables;

/**
 * Lewis Lancaster 2016
 *
 * Class Sessions
 *
 * @package Framework\Database\Tables
 */

use Framework\Database\Table;

class Sessions extends Table
{

    /**
     * Gets the session
     *
     * @param $sessionid
     *
     * @return mixed
     */

	public function getSession( $sessionid )
	{

		$array = array(
			'sessionid' => $sessionid
		);

		$result = $this->getTable()->where( $array )->get();

		return ( empty( $result ) ) ? null : reset( $result );
	}

	/**
	 * Trashes all the user sessions
	 *
	 * @param $userid
	 */

	public function trashUserSessions( $userid )
	{

		$array = array(
			'userid' => $userid
		);

		$this->getTable()->where( $array )->delete();
	}

	/**
	 * Gets by user agent
	 *
	 * @param $useragent
	 *
	 * @return array|null|static[]
	 */

	public function getByUserAgent( $useragent )
	{

		$array = array(
			'useragent' => $useragent
		);

		$result = $this->getTable()->where( $array )->get();

		return ( empty( $result ) ) ? null : $result;
	}

	/**
	 * Trashes a session
	 *
	 * @param $sessionid
	 */

	public function trashSession( $sessionid )
	{

		$array = array(
			'sessionid' => $sessionid
		);

		$this->getTable()->where( $array )->delete();
	}

	/**
	 * Inserts the session
	 *
	 * @param $array
	 */

	public function insertSession( $array )
	{

		$this->getTable()->insert( $array );
	}
}
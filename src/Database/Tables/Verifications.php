<?php
namespace Framework\Database\Tables;

/**
 * Lewis Lancaster 2016
 *
 * Class Verifications
 *
 * @package Framework\Database\Tables
 */

use Framework\Database\Table;

class Verifications extends Table
{

	/**
	 * Gets a request
	 *
	 * @param $verifyid
	 *
	 * @return mixed|null
	 */

	public function getRequest( $verifyid )
	{

		$array = array(
			'verifyid' => $verifyid
		);

		$result = $this->getTable()->where( $array )->get();

		return ( empty( $result ) ) ? null : reset( $result );
	}

	/**
	 * Gets a request by its token
	 *
	 * @param $token
	 *
	 * @return mixed|null
	 */

	public function getToken( $token )
	{

		$array = array(
			'token' => $token
		);

		$result = $this->getTable()->where( $array )->get();

		return ( empty( $result ) ) ? null : reset( $result );
	}

	/**
	 * Gets the user requests
	 *
	 * @param $userid
	 *
	 * @return mixed|null
	 */

	public function getUserRequests( $userid )
	{

		$array = array(
			'userid' => $userid
		);

		$result = $this->getTable()->where( $array )->get();

		return ( empty( $result ) ) ? null : $result;
	}

	/**
	 * Deletes all the user verification requests
	 *
	 * @param $userid
	 */

	public function deleteUserRequests( $userid )
	{

		$array = array(
			'userid' => $userid
		);

		$this->getTable()->where( $array )->delete();
	}

	/**
	 * Deletes a verification request
	 *
	 * @param $verifyid
	 */

	public function deleteRequest( $verifyid )
	{

		$array = array(
			'verifyid' => $verifyid
		);

		$this->getTable()->where( $array )->delete();
	}

	/**
	 * Inserts a request
	 *
	 * @param $array
	 */

	public function insertRequest( $array )
	{

		$this->getTable()->insert( $array );
	}
}
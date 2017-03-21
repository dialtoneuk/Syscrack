<?php
namespace Framework\Database\Tables;

/**
 * Lewis Lancaster 2016
 *
 * Class Users
 *
 * @package Framework\Database\Tables
 */

use Framework\Database\Table;

class Users extends Table
{

	/**
	 * Gets the user
	 *
	 * @param $userid
	 *
	 * @return mixed
	 */

	public function getUser( $userid )
	{

		$array = array(
			'userid' => $userid
		);

		$result = $this->getTable()->where( $array )->get();

		return ( empty( $result ) ) ? null : reset( $result );
	}

	/**
	 * Gets a user by their username
	 *
	 * @param $username
	 *
	 * @return mixed|null
	 */

	public function getByUsername( $username )
	{

		$array = array(
			'username' => $username
		);

		$result = $this->getTable()->where( $array )->get();

		return ( empty( $result ) ) ? null : reset( $result );
	}

    /**
     * Gets a user by their email
     *
     * @param $email
     *
     * @return mixed
     */

	public function getByEmail( $email )
    {

        $array = array(
            'email' => $email
        );

        $result = $this->getTable()->where( $array )->get();

        return ( empty( $result ) ) ? null : reset( $result );
    }

	/**
	 * Updates a user
	 *
	 * @param $userid
	 *
	 * @param $values
	 */

	public function updateUser( $userid, $values )
	{

		$array = array(
			'userid' => $userid
		);

		$this->getTable()->where( $array )->update( $values );
	}

    /**
     * Inserts a new user
     *
     * @param $array
     */

	public function insertUser( $array )
    {

        return $this->getTable()->insertGetId( $array );
    }
}
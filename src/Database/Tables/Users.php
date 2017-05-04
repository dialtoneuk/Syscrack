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
     * Gets all the users
     *
     * @return \Illuminate\Support\Collection
     */

    public function getUsers()
    {

        return $this->getTable()->get();
    }

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

		return ( $result->isEmpty() ) ? null : $result[0];
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

        return ( $result->isEmpty() ) ? null : $result[0];
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

        return ( $result->isEmpty() ) ? null : $result[0];
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
     *
     * @return int
     */

	public function insertUser( $array )
    {

        return $this->getTable()->insertGetId( $array );
    }
}
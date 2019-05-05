<?php
namespace Framework\Syscrack;

/**
 * Lewis Lancaster 2016
 *
 * Class User
 *
 * @package Framework\Syscrack
 */

use Framework\Database\Tables\Users as Database;
use Framework\Exceptions\SyscrackException;

class User
{

	/**
	 * @var Database
	 */

	protected $database;

	/**
	 * User constructor.
	 */

	public function __construct ()
	{

		$this->database = new Database();
	}

	/**
	 * Checks if the user exists
	 *
	 * @param $userid
	 *
	 * @return bool
	 */

	public function userExists( $userid )
	{

		if( $this->database->getUser( $userid ) == null )
		{

			return false;
		}

		return true;
	}

	/**
	 * Returns true if the username exists
	 *
	 * @param $username
	 *
	 * @return bool
	 */

	public function usernameExists( $username )
	{

		if( $this->database->getByUsername( $username ) == null )
		{

			return false;
		}

		return true;
	}

    /**
     * Should only be used in tests
     *
     * @param $userid
     */

	public function delete( $userid )
    {

        $this->database->deleteUser( $userid );
    }

	/**
	 * Finds a user by their username
	 *
	 * @param $username
	 *
	 * @return mixed
	 */

	public function findByUsername( $username )
	{

		$result = $this->database->getByUsername( $username );

		if( $result == null )
		{

			throw new SyscrackException();
		}

		return $result->userid;
	}

	/**
	 * Gets the user
	 *
	 * @param $userid
	 *
	 * @return array|null|\stdClass
	 */

	public function getUser( $userid )
	{

		if( $this->userExists( $userid ) == false )
		{

			throw new SyscrackException();
		}

		return $this->database->getUser( $userid );
	}

	/**
	 * Gets the users username
	 *
	 * @param $userid
	 *
	 * @return string
	 */

	public function getUsername( $userid )
	{

		if( $this->userExists( $userid ) == false )
		{

			throw new SyscrackException();
		}

		return $this->getUser( $userid )->username;
	}

	/**
	 * Gets the users password
	 *
	 * @param $userid
	 *
	 * @return \___PHPSTORM_HELPERS\static
	 */

	public function getPassword( $userid )
	{

		if( $this->userExists( $userid ) == false )
		{

			throw new SyscrackException();
		}

		return $this->getUser( $userid )->password;
	}

	/**
	 * Gets the users email
	 *
	 * @param $userid
	 *
	 * @return \___PHPSTORM_HELPERS\static
	 */

	public function getEmail( $userid )
	{

		if( $this->userExists( $userid ) == false )
		{

			throw new SyscrackException();
		}

		return $this->getUser( $userid )->email;
	}

	/**
	 * Gets the users salt
	 *
	 * @param $userid
	 *
	 * @return \___PHPSTORM_HELPERS\static
	 */

	public function getSalt( $userid )
	{

		if( $this->userExists( $userid ) == false )
		{

			throw new SyscrackException();
		}

		return $this->getUser( $userid )->salt;
	}

	/**
	 * Updates the users email
	 *
	 * @param $userid
	 *
	 * @param $email
	 */

	public function updateEmail( $userid, $email )
	{

		if( $this->userExists( $userid ) == false )
		{

			throw new SyscrackException();
		}

		if( $this->isEmail( $email ) == false )
		{

			throw new SyscrackException();
		}

		$array = array(
			'email' => $email
		);

		$this->database->updateUser( $userid, $array );
	}

	/**
	 * Updates the users password
	 *
	 * @param $userid
	 *
	 * @param $password
	 */

	public function updatePassword( $userid, $password )
	{

		if( $this->userExists( $userid ) == false )
		{

			throw new SyscrackException();
		}

		$array = array(
			'password' => $password
		);

		$this->database->updateUser( $userid, $array );
	}

    /**
     * Updates the users group
     *
     * @param $userid
     *
     * @param $group
     */

    public function updateGroup( $userid, $group )
    {

        if( $this->userExists( $userid ) == false )
        {

            throw new SyscrackException();
        }

        $array = array(
            'group' => $group
        );

        $this->database->updateUser( $userid, $array );
    }

	/**
	 * Updates the users salt
	 *
	 * @param $userid
	 *
	 * @param $salt
	 */

	public function updateSalt( $userid, $salt )
	{

		if( $this->userExists( $userid ) == false )
		{

			throw new SyscrackException();
		}

		$array = array(
			'salt' => $salt
		);

		$this->database->updateUser( $userid, $array );
	}

	/**
	 * Returns true if the user is an admin
	 *
	 * @param $userid
	 *
	 * @return bool
	 */

	public function isAdmin( $userid )
	{

		if( $this->userExists( $userid ) == false )
		{

			throw new SyscrackException();
		}

		if( $this->getUser( $userid )->group !== 'admin' )
		{

			return false;
		}

		return true;
	}

    /**
     * Returns all the users currently in the database
     *
     * @return \Illuminate\Support\Collection
     */

	public function getAllUsers()
    {

        return $this->database->getUsers();
    }

    /**
     * Returns the number of users
     *
     * @return int
     */

	public function getUsersCount()
    {

        return $this->database->getUsers()->count();
    }

	/**
	 * Returns true if it is an email.
	 *
	 * @param $email
	 *
	 * @return bool
	 */

	private function isEmail( $email )
	{

		if( filter_var( $email, FILTER_VALIDATE_EMAIL ) )
		{

			return true;
		}

		return false;
	}
}
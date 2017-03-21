<?php
namespace Framework\Syscrack\Login;

/**
 * Lewis Lancaster 2016
 *
 * Class Account
 *
 * @package Framework\Syscrack\Login
 */

use Framework\Application\Utilities\Hashes;
use Framework\Exceptions\LoginException;
use Framework\Exceptions\SyscrackException;
use Framework\Syscrack\User;
use Framework\Syscrack\Verification;

class Account
{

	/**
	 * @var User
	 */

	protected $user;

	/**
	 * @var Verification
	 */

	protected $verification;

	/**
	 * Account constructor.
	 */

	public function __construct ()
	{

		$this->user = new User();

		$this->verification = new Verification();
	}

	/**
	 * Logs in a user
	 *
	 * @param $username
	 *
	 * @param $password
	 *
	 * @return bool
	 */

	public function login( $username, $password )
	{
		
		if( $this->user->usernameExists( $username ) == false )
		{

			throw new LoginException('Username does not exist');
		}

		$userid = $this->user->findByUsername( $username );

		if( empty( $userid ) )
		{

			throw new LoginException('Unknown error occured');
		}

		if( $this->checkPassword( $userid, $password, $this->user->getSalt( $userid ) ) == false )
		{

			throw new LoginException('Password is invalid');
		}

		if( $this->verification->isVerified( $userid ) == false )
		{

			throw new LoginException('Please verify your email');
		}

		return true;
	}

	/**
	 * Checks to see if a password is valid
	 *
	 * @param $userid
	 *
	 * @param $password
	 *
	 * @param $salt
	 *
	 * @return bool
	 */

	private function checkPassword( $userid, $password, $salt )
	{

		if( $this->user->userExists( $userid ) == false )
		{

			throw new SyscrackException();
		}

		$accountpassword = $this->user->getPassword( $userid );

		if( Hashes::sha1( $password, $salt ) !== $accountpassword )
		{

			return false;
		}

		return true;
	}
}
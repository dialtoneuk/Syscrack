<?php
namespace Framework\Ajax\Routes;

/**
 * Lewis Lancaster 2016
 *
 * Class Login
 *
 * @package Framework\Ajax\Routes
 */

use Framework\Ajax\Structures\Route;
use Framework\Ajax\RouteHelper;
use Framework\Ajax\Types\Error;
use Framework\Ajax\Types\Result;
use Framework\Exceptions\LoginException;
use Framework\Syscrack\Login\Account;
use Framework\Syscrack\User;
use Framework\Session\Capsule;

class Login implements Route
{

	/**
	 * @var Account
	 */

	protected $account;

	/**
	 * @var Capsule
	 */

	protected $session;

	/**
	 * @var User
	 */

	protected $user;

	/**
	 * Login constructor.
	 */

	public function __construct ()
	{

		$this->account = new Account();

		$this->session = new Capsule();

		$this->user = new User();
	}

    /**
     * This is where you would code an authenticator, an authenticator is a type of middleware which is used for instance,
     * when verifying a session or specific data. Its aim is to keep repetitive checks outside of the methods and clean
     * up the script. If you don't want the class to use an authenticator, simply return null. Returning true will allow
     * the script to process, returning false will abord the script and return the user an error.
     *
     * @return null
     */

	public function authenticator()
    {

        return null;
    }

    /**
	 * The routes of this API call
	 *
	 * @return array
	 */

	public function routes()
	{

		$routes = new RouteHelper();

		$routes->addRoute('account', 'account', array(
			'username',
			'password'
		));

		return $routes->getRoutes();
	}

	/**
	 * Logs in a user
	 *
	 * @param $username
	 *
	 * @param $password
	 *
	 * @return Error|Result
	 */

	public function account( $username, $password )
	{

		try
		{

			$result = $this->account->login( $username, $password );

			if( $result == false )
			{

				throw new LoginException('Failed to login');
			}
		}
		catch( LoginException $error )
		{

			return new Error( $error->getMessage() );
		}

		$this->session->create();

		if( $this->session->isSessionActive() == false )
		{

			return new Error('Failed to create session');
		}

		$userid = $this->user->findByUsername( $username );

		if( empty( $userid ) )
		{

			return new Error('Internal error, try again in a few minutes)');
		}

		$this->session->addToDatabase( $userid );

		if( $this->session->isValid() == false )
		{

			return new Error('Failed to finish creating session');
		}

		return new Result(
			array(
				'sessionid' => session_id()
			)
		);
	}
}
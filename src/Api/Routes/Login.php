<?php
namespace Framework\Api\Routes;

/**
 * Lewis Lancaster 2017
 *
 * Class Login
 *
 * @package Framework\Api\Routes
 */

use Framework\Api\RouteHelper;
use Framework\Api\Structures\Route;
use Framework\Api\Types\Error;
use Framework\Api\Types\Result;
use Framework\Application\Session;
use Framework\Syscrack\Login\Account;

class Login implements Route
{

    /**
     * @var Session
     */

    protected $session;

    /**
     * Login constructor.
     */

    public function __construct()
    {

        $this->session = new Session();

        if( $this->session->sessionActive() == false )
        {

            session_start();
        }
    }

    /**
     * The class routes
     *
     * @return array
     */

    public function routes()
    {

        $routes = new RouteHelper();

        $routes->addRoute('account','account', array(
            'username',
            'password'
        ));

        return $routes->getRoutes();
    }

    /**
     * The authenticator ( not used in this class )
     *
     * @return null
     */

    public function authenticator()
    {

        return null;
    }

    /**
     * Creates a new user session with the users account details
     *
     * @param $username
     *
     * @param $password
     *
     * @return Error|Result
     */

    public function account( $username, $password )
    {

        if( $this->session->isLoggedIn() )
        {

            return new Error('User is already logged in');
        }

        $login = new Account();

        if( $login->login( $username, $password ) == false )
        {

            return new Error('Username or Password is invalid');
        }

        session_regenerate_id( true );

        $this->session->insertSession( $login->getUserID( $username ) );

        return new Result( array(
            'sessionid' => session_id()
        ));
    }
}
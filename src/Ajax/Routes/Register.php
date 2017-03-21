<?php
namespace Framework\Ajax\Routes;

/**
 * Lewis Lancaster 2017
 *
 * Class Register
 *
 * @package Framework\Ajax\Routes
 */

use Framework\Ajax\Structures\Route;
use Framework\Ajax\Types\Result;
use Framework\Exceptions\SyscrackException;
use Framework\Syscrack\Register as Manager;
use Framework\Syscrack\Verification;
use Framework\Ajax\RouteHelper;
use Framework\Ajax\Types\Error;

class Register implements Route
{

    /**
     * @var Manager
     */

    protected $manager;

    /**
     * @var Verification
     */

    protected $verification;

    /**
     * Register constructor.
     */

    public function __construct()
    {

        $this->manager = new Manager();

        $this->verification = new Verification();
    }

    /**
     * Authenticator
     *
     * @return null
     */

    public function authenticator()
    {

        return null;
    }

    /**
     * The routes
     *
     * @return array
     */

    public function routes()
    {

        $routes = new RouteHelper();

        $routes->addRoute('account', 'account', array(
            'username',
            'password',
            'email'
        ));

        return $routes->getRoutes();
    }

    /**
     * Registers a new user
     *
     * @param $username
     *
     * @param $password
     *
     * @param $email
     *
     * @return Error|Result
     */

    public function account( $username, $password, $email )
    {

        try
        {

            $userid = $this->manager->register( $username, $password, $email );
        }
        catch( SyscrackException $error )
        {

            return new Error( $error->getMessage() );
        }

        $token = $this->verification->addRequest( $userid, $email );

        //TODO: Send to users email here

        return new Result( $token );
    }
}
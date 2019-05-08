<?php
namespace Framework\Views\Middleware;

/**
 * Lewis Lancaster 2016
 *
 * Class VerificationCheck
 *
 * @package Framework\Views\Middleware
 */

use Error;
use Framework\Application\Container;
use Framework\Application\Session;
use Framework\Syscrack\Verification;
use Framework\Views\BaseClasses\Middleware as BaseClass;
use Framework\Views\Structures\Middleware as Structure;

class VerificationCheck extends BaseClass implements Structure
{

    /**
     * @var Session
     */

    protected $session;

    /**
     * @var Verification
     */

    protected $verification;

    /**
     * VerificationCheck constructor.
     *
     * @throws Error
     */

    public function __construct()
    {

        if( Container::hasObject('session') == false )
        {

            throw new Error();
        }

        $this->session = Container::getObject('session');

        if( session_status() !== PHP_SESSION_ACTIVE )
        {

            session_start();
        }

        if( $this->session->isLoggedIn() == false )
        {

            throw new Error();
        }

        $this->verification = new Verification();
    }


    /**
     * Checks if the user has verified their email
     *
     * @return bool
     */

    public function onRequest()
    {

        $userid = $this->session->userid();

        if( $this->verification->isVerified( $userid ) == false )
        {

            return false;
        }

        return true;
    }

    /**
     * Called when the user passes the middleware
     */

    public function onSuccess()
    {


    }

    /**
     * If the user has not verified their email, they'll be sent here.
     */

    public function onFailure()
    {

        $this->redirect('/verify/');
    }
}
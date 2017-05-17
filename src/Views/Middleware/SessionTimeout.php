<?php
namespace Framework\Views\Middleware;

/**
 * Lewis Lancaster 2017
 *
 * Class SessionTimeout
 *
 * @package Framework\Views\Middleware
 */

use Framework\Application\Container;
use Framework\Application\Session;
use Framework\Application\Settings;
use Framework\Views\BaseClasses\Middleware as BaseClass;
use Framework\Views\Structures\Middleware as Structure;

class SessionTimeout extends BaseClass implements Structure
{

    /**
     * @var Session
     */

    protected $session;

    /**
     * SessionTimeout constructor.
     */

    public function __construct()
    {

        if( Container::hasObject('session') == false )
        {

            return;
        }

        $this->session = Container::getObject('session');

        if( session_status() !== PHP_SESSION_ACTIVE )
        {

            session_start();
        }
    }

    /**
     * Checks to see if the session has timed out
     *
     * @return bool
     */

    public function onRequest()
    {

        if( $this->session == null )
        {

            return true;
        }

        if( $this->session->isLoggedIn() == false )
        {

            return true;
        }

        if( $this->session->getLastAction() < ( time() - Settings::getSetting('session_timeout') ) )
        {

            return false;
        }

        return true;
    }

    /**
     * We don't do anything here
     */

    public function onSuccess()
    {


    }

    /**
     * Cleans up and destroys the session, asks the user to login again
     */

    public function onFailure()
    {

        $this->session->cleanupSession( $this->session->getSessionUser() );

        $this->session->destroySession();

        $this->redirectError('Session has timed out, please login again!', 'login');
    }
}
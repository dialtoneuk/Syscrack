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
use Framework\Application\Settings;
use Framework\Exceptions\SyscrackException;
use Framework\Views\Structures\Middleware;

class SessionTimeout implements Middleware
{

    /**
     * @var \Framework\Application|\Framework\Application\Session
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
    }

    /**
     * Checks to see if the session has timed out
     *
     * @return bool
     */

    public function onRequest()
    {

        if( $this->session->isLoggedIn() == false )
        {

            return true;
        }

        if( $this->session->getLastAction() < time() - Settings::getSetting('session_timeout') )
        {

            return false;
        }

        return true;
    }

    /**
     * Doesn't do anything :)
     */

    public function onSuccess()
    {

        //
    }

    /**
     * Cleans up and destroys the session, asks the user to login again
     */

    public function onFailure()
    {

        $this->session->cleanupSession( $this->session->getSessionUser() );

        session_destroy();

        \Flight::redirect('/login?error=Session has timed out, login again!');
    }
}
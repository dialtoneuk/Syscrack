<?php
namespace Framework\Views\Middleware;

/**
 * Lewis Lancaster 2017
 *
 * Class SessionCheck
 *
 * @package Framework\Views\Middleware
 */

use Framework\Application\Container;
use Framework\Application\Session;
use Framework\Application\Settings;
use Framework\Views\BaseClasses\Middleware as BaseClass;
use Framework\Views\Structures\Middleware as Structure;

class SessionCheck extends BaseClass implements Structure
{

    /**
     * @var Session
     */

    protected $session;

    /**
     * SessionCheck constructor.
     */

    public function __construct()
    {

        if( session_status() !== PHP_SESSION_ACTIVE )
        {

            return;
        }

        if( Container::hasObject('session') == false )
        {

            return;
        }

        $this->session = Container::getObject('session');
    }

    /**
     * On Request
     *
     * @return bool
     */

    public function onRequest()
    {

        if( $_SERVER['REQUEST_URI'] !== Settings::getSetting('controller_index_root') )
        {

            if( $this->getCurrentPage() == Settings::getSetting('framework_page') || $this->getCurrentPage() == Settings::getSetting('developer_page') )
            {

                return true;
            }
        }

        if( $this->session->isLoggedIn() )
        {

            if( $this->session->getLastAction() > ( time() - Settings::getSetting('session_timeout') ) )
            {

                if( empty( $_SESSION ) )
                {

                    return false;
                }

                if( empty( $_SESSION['current_computer'] ) )
                {

                    return false;
                }
            }
        }

        return true;
    }

    /**
     * On Success
     */

    public function onSuccess()
    {


    }

    /**
     * Render the error database page
     */

    public function onFailure()
    {

        $this->session->cleanupSession( $this->session->getSessionUser() );

        $this->session->destroySession();

        $this->redirectError('Session was cleared, please login again!', 'login');
    }
}
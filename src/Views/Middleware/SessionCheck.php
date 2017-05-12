<?php
namespace Framework\Views\Middleware;

/**
 * Lewis Lancaster 2017
 *
 * Class SessionCheck
 *
 * @package Framework\Views\Middleware
 */

use Flight;
use Framework\Application\Container;
use Framework\Application\Session;
use Framework\Application\Settings;
use Framework\Views\Structures\Middleware;

class SessionCheck implements Middleware
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

            if( array_values( array_filter( explode('/', $_SERVER['REQUEST_URI'] ) ) )[0] == Settings::getSetting('framework_page') || array_values( array_filter( explode('/', $_SERVER['REQUEST_URI'] ) ) )[0] == Settings::getSetting('developer_page') )
            {

                return true;
            }
        }

        if( $this->session->isLoggedIn() )
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

        return true;
    }

    /**
     * On Success
     */

    public function onSuccess()
    {

        //Do nothing
    }

    /**
     * Render the error database page
     */

    public function onFailure()
    {

        Flight::redirect('/framework/error/session/');
    }
}
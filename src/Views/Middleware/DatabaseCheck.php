<?php
namespace Framework\Views\Middleware;

/**
 * Lewis Lancaster 2017
 *
 * Class DatabaseCheck
 *
 * @package Framework\Views\Middleware
 */

use Error;
use Flight;
use Framework\Application\Settings;
use Framework\Database\Manager;
use Framework\Exceptions\DatabaseException;
use Framework\Views\BaseClasses\Middleware as BaseClass;
use Framework\Views\Structures\Middleware as Structure;

class DatabaseCheck extends BaseClass implements Structure
{

    /**
     * DatabaseCheck constructor.
     */

    public function __construct()
    {

        if( $_SERVER['REQUEST_URI'] !== Settings::getSetting('controller_index_root') )
        {

            if( $this->getCurrentPage() == Settings::getSetting('framework_page') || $this->getCurrentPage() == Settings::getSetting('developer_page') )
            {

                throw new Error();
            }
        }
    }

    /**
     * Called when the process is requested
     *
     * @return bool
     */

    public function onRequest()
    {

        try
        {

            if (Manager::getCapsule() == null)
            {

                new Manager();
            }

            Manager::$capsule->getConnection()->getPdo();
        }
        catch( DatabaseException $error )
        {

            return false;
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

        Flight::redirect('/framework/error/database/');
    }
}
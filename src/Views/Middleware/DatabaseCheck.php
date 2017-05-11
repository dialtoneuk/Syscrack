<?php
namespace Framework\Views\Middleware;

/**
 * Lewis Lancaster 2017
 *
 * Class DatabaseCheck
 *
 * @package Framework\Views\Middleware
 */

use Flight;
use Framework\Application\Settings;
use Framework\Database\Manager;
use Framework\Exceptions\DatabaseException;
use Framework\Views\Structures\Middleware;

class DatabaseCheck implements Middleware
{

    /**
     * On Request
     *
     * @return bool
     */

    public function onRequest()
    {

        if( $_SERVER['REQUEST_URI'] !== Settings::getSetting('controller_index_root') && array_values( array_filter( explode('/', $_SERVER['REQUEST_URI'] ) ) )[0] == 'framework' )
        {

            return true;
        }

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

        Flight::redirect('/framework/error/database/'); exit;
    }
}
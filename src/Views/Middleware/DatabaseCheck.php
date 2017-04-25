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
use Framework\Views\Structures\Middleware;

class DatabaseCheck implements Middleware
{

    public function onRequest()
    {

        if( $_SERVER['REQUEST_URI'] !== Settings::getSetting('controller_index_root') && array_values( array_filter( explode('/', $_SERVER['REQUEST_URI'] ) ) )[0] == 'framework' )
        {

            return true;
        }

        if (Manager::getCapsule() == null)
        {

            new Manager();
        }

        try
        {

            Manager::$capsule->getConnection()->getPdo();
        }
        catch( \Exception $error )
        {

            return false;
        }

        return true;
    }

    public function onSuccess()
    {

        //Do nothing
    }

    public function onFailure()
    {

        Flight::redirect('/framework/error/database/'); exit;
    }
}
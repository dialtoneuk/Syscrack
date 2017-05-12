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
use Framework\Views\Structures\Middleware;

class DatabaseCheck implements Middleware
{

    /**
     * DatabaseCheck constructor.
     */

    public function __construct()
    {

        if( $_SERVER['REQUEST_URI'] !== Settings::getSetting('controller_index_root') )
        {

            if( array_values( array_filter( explode('/', $_SERVER['REQUEST_URI'] ) ) )[0] == Settings::getSetting('framework_page') || array_values( array_filter( explode('/', $_SERVER['REQUEST_URI'] ) ) )[0] == Settings::getSetting('developer_page') )
            {

                //Throws an error which stops the middlewares from doing anything past this point and instantly returns a false
                throw new Error();
            }
        }
    }

    /**
     * On Request
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
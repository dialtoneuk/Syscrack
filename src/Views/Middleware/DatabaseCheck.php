<?php
namespace Framework\Views\Middleware;

/**
 * Lewis Lancaster 2017
 *
 * Class DatabaseCheck
 *
 * @package Framework\Views\Middleware
 */

use Framework\Database\Manager;
use Framework\Views\Structures\Middleware;
use Framework\Application\Container;

class DatabaseCheck implements Middleware
{

    public function onRequest()
    {

        new Manager();

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

        \Flight::redirect('/error/database/'); exit;
    }
}
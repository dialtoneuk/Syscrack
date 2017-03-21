<?php
namespace Framework\Api;

/**
 * Lewis Lancaster 2016
 *
 * Class Manager
 *
 * @package Framework\Api
 */

use Framework\Application\Settings;
use Framework\Exceptions\ApiException;
use Flight;

class Manager
{

    /**
     * @var Controller
     */

    protected $controller;

    /**
     * Starts the manager
     */

    public function initialize()
    {

        $this->controller = new Controller();
    }

    /**
     * Processes that request.
     *
     * @param $class
     *
     * @param $method
     *
     * @return string
     */

    public function processRequest( $class, $method )
    {

        $result = null;

        try
        {

            $result = $this->controller->route($class, $method, $this->createData() );
        }
        catch( ApiException $error )
        {

             Flight::json( $error->getArray(), 400 );
        }

        if( $result === null )
        {

            throw new ApiException("Result ended up being null");
        }

        if( is_array( $result->getResult() ) == false )
        {

            $result->getResult(); return;
        }

        Flight::json( $result->getResult(), 200 );
    }

    /**
     * Creates our set of data
     *
     * @return null|array
     */

    private function createData()
    {

        $data = $this->getData();

        if( $data === null )
        {

            return null;
        }

        $array = array();

        foreach( $data as $key=>$value )
        {

            if( strlen( $value ) > 2048 )
            {

                continue;
            }

            $array[ $key ] = addslashes( $value );
        }

        return $data;
    }


    public function getData()
    {

        $data = $_POST;

        if( empty( $data ) == true )
        {

            if( Settings::getSetting('api_use_get') )
            {

                if( empty( $_GET ) )
                {

                    return null;
                }

                $data = $_GET;
            }
        }

        return $data;
    }
}
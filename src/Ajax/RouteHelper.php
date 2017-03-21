<?php
namespace Framework\Ajax;

/**
 * Lewis Lancaster 2016
 *
 * Class RouteHelper
 *
 * @package Framework\Ajax
 */

use Framework\Exceptions\AjaxException;

class RouteHelper
{

    /**
     * @var array
     */

    protected $routes = array();

    /**
     * @var array
     */

    protected $syntax = [
        'method'        => 'is_string',
        'requirements'  => 'is_array'
    ];

    /**
     * Adds a route
     *
     * @param $name
     *
     * @param $method
     *
     * @param $requirements
     */

    public function addRoute( $name, $method, $requirements )
    {

        if( isset( $this->routes[ $name ] ) )
        {

            throw new AjaxException("Route already defined");
        }

        $data = array(
            'method'        => $method,
            'requirements'  => $requirements
        );

        if( $this->checkSyntax( $data ) == false )
        {

            throw new AjaxException("The data given is invalid, check parameters");
        }

        $this->routes[ $name ] = $data;
    }

    /**
     * Modifies a route
     *
     * @param $name
     *
     * @param $data
     */

    public function modifyRoute( $name, $data )
    {

        if( isset( $this->routes[ $name ] ) == false )
        {

            throw new AjaxException("Route is not defined");
        }

        if( $this->checkSyntax( $data ) == false )
        {

            throw new AjaxException("The data given is invalid, check data");
        }

        $this->routes[ $name ] = $data;
    }

    /**
     * Deletes a route
     *
     * @param $name
     */

    public function deleteRoute( $name )
    {

        if( isset( $this->routes[ $name ] ) == false )
        {

            throw new AjaxException("Route is not defined");
        }

        unset( $this->routes[ $name ] );
    }

    /**
     * Gets the routes
     *
     * @return array
     */

    public function getRoutes()
    {

        return $this->routes;
    }

    /**
     * Quick function to check the syntax of our information
     *
     * @param $data
     *
     * @return bool
     */

    private function checkSyntax( $data )
    {

        foreach( $data as $key=>$value )
        {

            if( isset( $this->syntax[ $key ] ) )
            {

                if( call_user_func( $this->syntax[ $key ], $value ) == false )
                {

                    return false;
                }
            }
            else
            {

                return false;
            }
        }

        return true;
    }
}
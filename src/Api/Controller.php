<?php
namespace Framework\Api;

/**
 * Lewis Lancaster 2016
 *
 * Class Controller
 *
 * @package Framework\Api
 */

use Framework\Api\Structures\Route;
use Framework\Exceptions\ApiException;

class Controller
{

    /**
     * Processes a route.
     *
     * @param $class
     *
     * @param $route
     *
     * @param $data
     *
     * @return mixed
     */

    public function route( $class, $route, $data )
    {

        if( $this->classExists( $class ) === false )
        {

            throw new ApiException("Class does not exist");
        }

        $instance = $this->createClass( $class );

        if( $instance instanceof Route === false )
        {

            throw new ApiException("Instance is not as expected");
        }

        if( $this->containsRoute( $instance, $route ) === false )
        {

            throw new ApiException("Method does not exist");
        }

        if( $this->compareData( $instance, $route, $data ) === false )
        {

            throw new ApiException("Data is incorrect");
        }

        $data = $this->createData( $instance, $route, $data );

        if( $this->compareAuthenticator( $instance, $data ) === false )
        {

            throw new ApiException("Authenticator failed test");
        }

        return $this->getResult( $instance, $route, $data );
    }

    /**
     * Gets the result of this API.
     *
     * @param $instance
     *
     * @param $route
     *
     * @param $data
     *
     * @return mixed
     */

    private function getResult( $instance, $route, $data )
    {

        $method = $this->getMethod( $instance, $route );

        if( $method === null )
        {

            throw new ApiException("Method returned null");
        }

        return call_user_func_array( array( $instance, $method ), $data );
    }

    /**
     * Gets the method for this route
     *
     * @param $instance Route
     *
     * @param $route
     *
     * @return mixed
     */

    private function getMethod( $instance, $route )
    {

        return $instance->routes()[ $route ]["method"];
    }

    /**
     * @param $instance Route
     *
     * @param $data
     *
     * @return bool
     */

    private function compareAuthenticator( $instance, $data )
    {

        if( $this->hasAuthenticator( $instance ) === false )
        {

            return true;
        }

        $authenticator = $instance->authenticator();

        if( $authenticator->getResult( $data ) === false )
        {

            return false;
        }

        return true;
    }

    /**
     * @param $instance Route
     *
     * @return bool
     */

    private function hasAuthenticator( $instance )
    {

        if( $instance->authenticator() === null )
        {

            return false;
        }

        return true;
    }

    /**
     * Creates the data
     *
     * @param $instance
     *
     * @param $route
     *
     * @param $data
     *
     * @return array
     */

    private function createData( $instance, $route, $data )
    {

        $requirements = $this->getRequirements( $instance, $route );

        $array = array();

        foreach( $requirements as $value )
        {

            if( $this->hasRegex( $value ) )
            {

                $value = $this->getShort( $value );
            }

            if( isset( $data[ $value ] ) == false )
            {

                throw new ApiException();
            }

            $array[ $value ] = $data[ $value ];
        }

        return $array;
    }

    /**
     * Compares the data given by the user.
     *
     * @param $instance
     *
     * @param $route
     *
     * @param $data
     *
     * @return bool
     */

    private function compareData( $instance, $route, $data )
    {

        $requirements = $this->getRequirements( $instance, $route );

        foreach( $requirements as $value )
        {

            if( $this->hasRegex( $value ) )
            {

                $short = $this->getShort( $value );

                if( isset( $data[ $short ] ) == false )
                {

                    return false;
                }

                $postdata = $data[ $short ];

                if( $this->doRegex( $postdata, $this->getRegex( $value ) ) === false )
                {

                    return false;
                }

                continue;
            }
            else
            {

                if( isset( $data[ $value ] ) == false )
                {

                    return false;
                }

                continue;
            }
        }

        return true;
    }

    /**
     * If we have regex
     *
     * @param $requirement
     *
     * @return bool
     */

    private function hasRegex( $requirement )
    {

        if( isset( explode( ":", $requirement )[1] ) )
        {

            return true;
        }

        return false;
    }

    /**
     * Does that regex.
     *
     * @param $string
     *
     * @param $regex
     *
     * @return bool
     */

    private function doRegex( $string, $regex )
    {

        if( preg_match( $regex, $string ) )
        {

            return true;
        }

        return false;
    }

    /**
     * Gets the value of the requirement before the regex.
     *
     * @param $requirement
     *
     * @return mixed
     */

    private function getShort( $requirement )
    {

        return reset( explode( ":",  $requirement ) );
    }

    /**
     * Returns the regex from the key string.
     *
     * @param $requirement
     *
     * @return mixed
     */

    private function getRegex( $requirement )
    {

        return explode( ":",  $requirement )[1];
    }

    /**
     * Returns our requirements.
     *
     * @param $instance Route
     *
     * @param $route
     *
     * @return mixed
     */

    private function getRequirements( $instance, $route )
    {

        $array = $instance->routes();

        if( empty( $array ) )
        {

            throw new ApiException();
        }

        return $array[ $route ][ "requirements" ];
    }

    /**
     * Does this contain our route?
     *
     * @param $instance Route
     *
     * @param $route
     *
     * @return bool
     */

    private function containsRoute( $instance, $route )
    {

        $routes = $instance->routes();

        if( isset( $routes[ $route ] ) )
        {

            return true;
        }

        return false;
    }

    /**
     * Creates this class.
     *
     * @param $class
     *
     * @return mixed
     */

    private function createClass( $class )
    {

        $path = "Framework\\Api\\Routes\\{$class}";

        if( class_exists( $path ) === false )
        {

            throw new ApiException("Class does not exist");
        }

        return new $path;
    }

    /**
     * Checks to see if the class specified exists.
     *
     * @param $class
     *
     * @return bool
     */

    private function classExists( $class )
    {

        if( class_exists("Framework\\Api\\Routes\\{$class}") == false )
        {

            $class = ucfirst( $class );

            if( class_exists("Framework\\Api\\Routes\\{$class}") == false )
            {

                return false;
            }
        }

        return true;
    }
}
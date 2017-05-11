<?php
namespace Framework\Views;

/**
 * Lewis Lancaster 2016
 *
 * Class Middlewares
 *
 * @package Framework\Views
 */

use Framework\Application\Settings;
use Framework\Application\Utilities\Factory;
use Framework\Application\Utilities\FileSystem;
use Framework\Exceptions\ApplicationException;
use Framework\Views\Structures\Middleware;

class Middlewares
{

    /**
     * @var array
     */

    protected $middlewares = [];

    /**
     * @var Factory
     */

    protected $factory;

    /**
     * Middlewares constructor.
     *
     * @param string $namespace
     *
     * @param bool $auto
     */

    public function __construct( $namespace = null, $auto=true )
    {

        if( $namespace == null )
        {

            $namespace = Settings::getSetting('middlewares_namespace');
        }

        $this->factory = new Factory( $namespace );

        if( $auto )
        {

            $this->loadMiddleware();
        }
    }

    /**
     * Loads the middlewares
     *
     * @return bool
     */

    public function loadMiddleware()
    {

        try
        {

            $this->getMiddlewares();
        }
        catch( \RuntimeException $error )
        {

            return false;
        }

        return true;
    }

    /**
     * Processes the middlewares
     */

    public function processMiddlewares()
    {

        foreach( $this->middlewares as $middleware )
        {

            try
            {

                $middleware = $this->factory->createClass( $middleware );

                if( $middleware instanceof Middleware == false )
                {

                    throw new ApplicationException();
                }

                if( $middleware->onRequest() )
                {

                    $middleware->onSuccess();
                }
                else
                {

                    $middleware->onFailure();
                }
            }
            catch( \RuntimeException $error )
            {

                continue;
            }
        }
    }

    /**
     * Returns if the middlewares have loaded
     *
     * @return bool
     */

    public function isLoaded()
    {

        if( empty( $this->middlewares ) )
        {

            return false;
        }

        return true;
    }

    /**
     * Returns true if there are any middlewares
     *
     * @return bool
     */

    public function hasMiddlewares()
    {

        if( FileSystem::getFilesInDirectory( Settings::getSetting('middlewares_location') ) == null )
        {

            return false;
        }

        return true;
    }

    /**
     * Gets the middlewares and sets the internal class array
     */

    private function getMiddlewares()
    {

        $middlewares = FileSystem::getFilesInDirectory( Settings::getSetting('middlewares_location') );

        if( empty( $middlewares ) )
        {

            throw new ApplicationException();
        }

        $middlewares = $this->format( $middlewares );

        if( empty( $middlewares ) )
        {

            throw new ApplicationException();
        }

        $this->middlewares = $middlewares;
    }

    /**
     * Correctly formates the files ( by removing the extension ) to be passed to the factory
     *
     * @param $files
     *
     * @return array
     */

    private function format( $files )
    {

        $array = array();

        foreach( $files as $file )
        {

            $array[] = FileSystem::getFileName( $file );

        }

        return $array;
    }
}
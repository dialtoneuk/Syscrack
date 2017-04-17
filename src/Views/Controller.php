<?php
namespace Framework\Views;

/**
 * Lewis Lancaster 2016
 *
 * Class Controller
 *
 * @package Framework\Views
 */

use Framework\Application\Settings;
use Framework\Application\Utilities\Log;
use Framework\Exceptions\ApplicationException;
use Framework\Exceptions\ViewException;
use Framework\Application\Utilities\Factory;
use Framework\Views\Structures\Page;
use Flight;

class Controller
{

    /**
     * @var array
     */

    protected $middlewares;

    /**
     * @var Factory
     */

    protected $factory;

    /**
     * Public variable of the current page
     *
     * @var string
     */

    public $page;

    /**
     * Controller constructor.
     */

	public function __construct()
    {

        $this->factory = new Factory( Settings::getSetting('controller_namespace') );
    }

    /**
     * Runs the controller
     */

    public function run()
    {

        $url = $this->getURL();

        if( $this->checkURL( $url ) == false )
        {

            throw new ViewException('URL has exceeded the maximum length');
        }

        $page = $this->getPage( $url );

        if( empty( $page ) )
        {

            $page = Settings::getSetting('controller_index_page');
        }
        else
        {

            $page = $page[0];
        }

        if( $this->isIndex( $page ))
        {

            $page = Settings::getSetting('controller_index_page');
        }

        $this->page = $page;

        if( Settings::getSetting('developer_disabled') == true )
        {

            if( $page == Settings::getSetting('developer_page') )
            {

                Flight::notFound();

                exit;
            }
        }

        if( $this->hasURLKey( $page ) )
        {

            $page = $this->removeURLKey( $page );
        }

        Log::log('Page Created');

        $this->createPage( $page );

        if( Settings::getSetting('middlewares_enabled') && Settings::getSetting('developer_page') !== $page )
        {

            $this->processMiddlewares();
        }
    }

    /**
     * Gets an instance of the page class
     *
     * @param $page
     *
     * @return Page
     */

    public function getPageClass( $page )
    {

        if( $this->factory->classExists( $page ) == false )
        {

            return null;
        }

        return $this->factory->createClass( $page );
    }

    /**
     * Processes the middlewares
     */

    private function processMiddlewares()
    {

        $middlewares = new Middlewares();

        if( $middlewares->hasMiddlewares() )
        {

            $middlewares->processMiddlewares();
        }
    }

    /**
     * Returns true if we are the index
     *
     * @param $page
     *
     * @return bool
     */

    private function isIndex( $page )
    {

        if( $page == Settings::getSetting('controller_index_root') )
        {

            return true;
        }

        return false;
    }

    /**
     * Creates the page class
     *
     * @param $page
     *
     * @return null
     */

    private function createPage( $page )
    {

        if( $this->factory->classExists( $page ) == false )
        {

            throw new ApplicationException('Page: ' . $page . ' does not exist');
        }

        $this->processClass( $this->factory->createClass( $page ) );
    }


    /**
     * Processes the class
     *
     * @param Page $class
     */

    private function processClass( Page $class )
    {

        if( $class instanceof Page == false )
        {

            throw new ViewException('Class does not have required interface');
        }

        $this->processFlightRoutes( $class, $class->mapping() );
    }

    /**
     * Processes the flight route
     *
     * @param $class
     *
     * @param $array
     *
     * @return bool
     */

    private function processFlightRoutes( $class, $array )
    {

        foreach( $array as $route )
        {

            if( method_exists( $class, $route[1] ) == false )
            {

                throw new ViewException();
            }

            Flight::route( $route[0], array( $class, $route[1]) );
        }

        return true;
    }

    /**
     * Returns true if we have URL Keys
     *
     * @param $page
     *
     * @return bool
     */

    private function hasURLKey( $page )
    {

        if( explode('?', $page ) )
        {

            return true;
        }

        return false;
    }

    /**
     * Removes a URL key from the page
     *
     * @param $page
     *
     * @return mixed
     */

    private function removeURLKey( $page )
    {

        $keys = explode('?', $page );

        if( empty( $keys ) )
        {

            throw new ViewException();
        }

        return reset( $keys );
    }

    /**
     * Gets the page
     *
     * @param $url
     *
     * @return array
     */

    private function getPage( $url )
    {

        return array_values( array_filter( explode('/', $url ) ) );
    }

    /**
     * Checks the URL
     *
     * @param $url
     *
     * @return bool
     */

    private function checkURL( $url )
    {

        if( strlen( $url ) > Settings::getSetting('controller_url_length') )
        {

            return false;
        }

        return true;
    }

    /**
     * Gets the URL
     *
     * @return mixed
     */

    private function getURL()
    {

        return $_SERVER['REQUEST_URI'];
    }
}
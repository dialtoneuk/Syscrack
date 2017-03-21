<?php
namespace Framework\Views\Pages;

/**
 * Lewis Lancaster 2016
 *
 * Class Ajax
 *
 * @package Framework\Views\Pages
 */

use Framework\Application\Container;
use Framework\Views\Structures\Page;
use Framework\Ajax\Manager;
use Exception;
use Flight;

class Ajax implements Page
{

    /**
     * @var Manager
     */

    protected $manager;

    /**
     * Ajax constructor.
     */

    public function __construct()
    {

        $this->manager = new Manager();
    }

    /**
     * The index page has a special algorithm which allows it to access the root. Only the index can do this.
     *
     * @return array
     */

    public function mapping()
    {

        return array(
            [
                '/ajax/@class/@method/', 'process'
            ]
        );
    }

    /**
     * Default page
     */

    public function process( $class, $method )
    {

        //Inits
        $this->manager->initialize();

        try
        {

            $this->manager->processRequest( $class, $method );
        }
        catch( Exception $error )
        {

            Container::getObject('application')->getErrorHandler()->handleError( $error );

            Flight::json([
                'error' => true,
                'stack' => [
                    'message'   => $error->getMessage(),
                    'file'      => $error->getFile(),
                    'line'      => $error->getLine()
                ]
            ]);
        }
    }
}
<?php
namespace Framework\Views\Pages;

/**
 * Lewis Lancaster 2016
 *
 * Class Api
 *
 * @package Framework\Views\Pages
 */

use Framework\Application\Container;
use Framework\Views\Structures\Page;
use Framework\Api\Manager;
use Exception;
use Flight;

class Api implements Page
{

    /**
     * @var Manager
     */

    protected $manager;

    /**
     * Api constructor.
     */

    public function __construct()
    {

        $this->manager = new Manager();
    }

    /**
     * The mapping
     *
     * @return array
     */

    public function mapping()
    {

        return array(
            [
                '/api/@class/@method/', 'process'
            ]
        );
    }

    /**
     * Default page
     */

    public function process( $class, $method )
    {

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
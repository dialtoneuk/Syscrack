<?php
namespace Framework\Views\Pages;

/**
 * Lewis Lancaster 2017
 *
 * Class Api
 *
 * @package Framework\Views\Pages
 */

use Framework\Application\Api\Controller;
use Framework\Application\Api\Manager;
use Framework\Application\Settings;
use Framework\Application\Utilities\PostHelper;
use Framework\Exceptions\ViewException;
use Framework\Views\Structures\Page;
use Flight;

class Api implements Page
{

    /**
     * @var Manager
     */

    protected $manager;

    /**
     * @var Controller
     */

    protected $controller;

    /**
     * @var mixed|string
     */

    public $apikey = "";

    /**
     * Api constructor.
     */

    public function __construct()
    {

        $this->manager = new Manager();

        $this->controller = new Controller();

        if( PostHelper::checkForRequirements( ['apikey'] ) == false )
        {

            Flight::notFound();
        }
        else
        {

            $this->apikey = PostHelper::getPostData('apikey');

            if( $this->manager->hasApiKey( $this->apikey ) == false )
            {

                Flight::redirect('/', 401);
            }
        }
    }

    /**
     * The views mapping
     *
     * @return array
     */

    public function mapping()
    {

        return array(
            [
                '/api/@endpoint/(@method)/', 'process'
            ]
        );
    }

    /**
     * Processes the API request
     *
     * @param $endpoint
     *
     * @param null $method
     */

    public function process( $endpoint, $method=null )
    {

        $result = null;

        try
        {

            if( $method == null )
            {

                $result = $this->controller->processEndpoint( $endpoint, Settings::getSetting('api_default_method') );
            }
            else
            {

                $result = $this->controller->processEndpoint( $endpoint, $method );
            }
        }
        catch( \Exception $error )
        {

            Flight::json(array(
                'error' => true,
                'info' => [
                    'message' => $error->getMessage(),
                    'line'  => $error->getLine(),
                    'file'  => $error->getFile()
                ]
            ));
        }

        if( is_array( $result ) == false )
        {

            throw new ViewException();
        }

        Flight::json( $result );
    }
}
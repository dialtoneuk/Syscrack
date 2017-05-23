<?php
    namespace Framework\Views\Pages;

    /**
     * Lewis Lancaster 2017
     *
     * Class Api
     *
     * @package Framework\Views\Pages
     */

    use Flight;
    use Framework\Application\Api\Controller;
    use Framework\Application\Api\Manager;
    use Framework\Application\Settings;
    use Framework\Application\Utilities\PostHelper;
    use Framework\Exceptions\ViewException;
    use Framework\Views\BaseClasses\Page as BaseClass;
    use Framework\Views\Structures\Page as Structure;

    class Api extends BaseClass implements Structure
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

            parent::__construct( false );

            if( isset( $this->manager ) == false )
            {

                $this->manager = new Manager();
            }

            if( isset( $this->computers ) == false )
            {

                $this->controller = new Controller();
            }

            if (PostHelper::hasPostData())
            {

                if (PostHelper::checkForRequirements(['apikey']) == false)
                {

                    Flight::json(array(
                        'error' => true,
                        'code'  => 401,
                        'info'  => [
                            'message' => 'Apikey required as post key'
                        ]
                    ));

                    exit;
                }
                else
                {

                    $this->apikey = PostHelper::getPostData('apikey');

                    if ($this->manager->hasApiKey($this->apikey) == false)
                    {

                        Flight::redirect('/api/', 401);
                    }
                }
            }
            else
            {

                Flight::json(array(
                    'error' => true,
                    'code'  => 401,
                    'info'  => [
                        'message' => 'Apikey required as post key'
                    ]
                ));

                exit;
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

        public function process($endpoint, $method = null)
        {

            $result = null;

            try
            {

                if ($method == null)
                {

                    $result = $this->controller->processEndpoint($endpoint, Settings::getSetting('api_default_method'));
                }
                else
                {

                    $result = $this->controller->processEndpoint($endpoint, $method);
                }
            }
            catch (\Exception $error)
            {

                Flight::json(array(
                    'error' => true,
                    'code'  => 502,
                    'info'  => [
                        'message' => $error->getMessage(),
                        'line' => $error->getLine(),
                        'file' => $error->getFile()
                    ]
                ));
            }

            if (is_array($result) == false)
            {

                throw new ViewException();
            }

            Flight::json($result);
        }
    }
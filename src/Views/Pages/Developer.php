<?php
    namespace Framework\Views\Pages;

    /**
     * Lewis Lancaster 2017
     *
     * Class Developer
     *
     * @package Framework\Views\Pages
     */

    use Flight;
    use Framework\Application\ErrorHandler;
    use Framework\Application\Settings;
    use Framework\Application\Utilities\PostHelper;
    use Framework\Views\BaseClasses\Page as BaseClass;
    use Framework\Views\Structures\Page as Structure;

    class Developer extends BaseClass implements Structure
    {

        /**
         * @var ErrorHandler
         */

        protected $errorhandler;

        /**
         * Developer constructor.
         */

        public function __construct()
        {

            parent::__construct( false );


            if( isset( $this->errorhandler ) == false )
            {

                $this->errorhandler = new ErrorHandler();
            }

            //Used to display errors

            if( session_status() !== PHP_SESSION_ACTIVE )
            {

                session_start();
            }
        }

        /**
         * The mapping of the class, on the left is the url of which will be mapped to the method, which is given on the right,
         * for example, if you a user goes to www.syscrack.com/developer/databasecreator/ the framework will first look for the
         * 'developer' class identifier, this is known as the page class ( which is this class! ), the mapping function is then called and the array
         * of mapped url are returned. We then simply compare the url, if it is '/databasecreator/' then the method databaseCreator will be
         * called!
         *
         * @return array
         */

        public function mapping()
        {

            return array(
                [
                    '/developer/', 'index'
                ],
                [
                    '/developer/connection/creator/', 'connectionCreator'
                ],
                [
                    '/developer/connection/', 'connection'
                ],
                [
                    'GET /developer/errors/', 'errors'
                ],
                [
                    'POST /developer/errors/', 'errorsProcess'
                ],
                [
                    '/developer/errors/@id:[0-9]{0,9}/', 'errorsView'
                ],
                [
                    '/developer/disable/', 'disable'
                ],
                [
                    'GET /developer/settings/', 'settings'
                ],
                [
                    'POST /developer/settings/', 'settingsProcess'
                ],
                [
                    '/developer/routes/', 'routes'
                ],
                [
                    '/developer/migrator/', 'databaseMigrator'
                ]
            );
        }

        /**
         * Renders the index page
         */

        public function index()
        {

            $this->getRender('page.developer');
        }

        /**
         * Renders the database migrator page
         */

        public function databaseMigrator()
        {

            $this->getRender('page.migrator');
        }

        /**
         * Renders the page viewer page
         */

        public function routes()
        {

            $this->getRender('page.routes');
        }

        /**
         * Renders the logger page
         */

        public function errors()
        {

            $this->getRender('page.errors');
        }

        public function errorsProcess()
        {

            if( PostHelper::hasPostData() == false )
            {

                $this->errors();
            }
            else
            {

                if( PostHelper::checkForRequirements(['action'] ) == false )
                {

                    $this->redirectError('Missing information', $this->getRedirect('errors') );
                }
                else
                {

                    $action = PostHelper::getPostData('action');

                    if( $action == "delete" )
                    {

                        $this->errorhandler->deleteErrorLog();

                        $this->redirectSuccess( $this->getRedirect('errors') );
                    }
                }
            }
        }
        /**
         * Renders the detailed logger page
         *
         * @param $id
         */

        public function errorsView( $id )
        {

            $errors = $this->errorhandler->getErrorLog();

            if( isset( $errors[ $id ] ) == false )
            {

                $this->redirectError('Error does not exist', $this->getRedirect('errors') );
            }
            else
            {

                Flight::render('developer/page.errors.view', array( 'id' => $id ) );
            }
        }

        /**
         * Renders the disable developer section page
         */

        public function disable()
        {

            $this->getRender('page.disable');
        }

        /**
         * Renders the settings manager
         */

        public function settings()
        {

            $this->getRender('page.settings');
        }

        /**
         * Processes a post request to the settings page
         */

        public function settingsProcess()
        {

            if( PostHelper::hasPostData() == false )
            {

                $this->settings();
            }
            else
            {

                if( PostHelper::checkForRequirements(['action','setting_name','setting_value'] ) == false )
                {

                    $this->redirectError('Missing information', $this->getRedirect('settings') );
                }

                $action = PostHelper::getPostData('action');

                if( $action == "create" )
                {

                    $settings_name = PostHelper::getPostData('setting_name');
                    $settings_value = PostHelper::getPostData('setting_value');

                    if( Settings::hasSetting( $settings_name ) )
                    {

                        $this->redirectError('Setting already exists under that name');
                    }

                    Settings::addSetting( $settings_name, $settings_value, true );

                    $this->redirectSuccess( $this->getRedirect('settings') );
                }
                elseif( $action == "save" )
                {

                    $settings_name = PostHelper::getPostData('setting_name');
                    $settings_value = PostHelper::getPostData('setting_value');

                    if( Settings::hasSetting( $settings_name ) == false )
                    {

                        $this->redirectError('This setting does not exist', $this->getRedirect('settings') );
                    }

                    Settings::updateSetting( $settings_name, $this->parseSetting( $settings_value ) );

                    $this->redirectSuccess( $this->getRedirect('settings') );
                }
                elseif( $action == "delete" )
                {

                    $settings_name = PostHelper::getPostData('setting_name');

                    if( Settings::hasSetting( $settings_name ) == false )
                    {

                        $this->redirectError('This setting does not exist', $this->getRedirect('settings') );
                    }

                    Settings::removeSetting( $settings_name );

                    $this->redirectSuccess( $this->getRedirect('settings') );
                }
            }
        }

        /**
         * Renders the connection creator
         */

        public function connectionCreator()
        {

            $this->getRender('page.connection.creator');
        }

        /**
         * Renders the database creator
         */

        public function connection()
        {

            $this->getRender('page.connection');
        }

        /**
         * Parses a setting and returns the correct value
         *
         * @param $setting_value
         *
         * @return bool|mixed
         */

        private function parseSetting( $setting_value )
        {

            if( strtolower( $setting_value ) == 'true' )
            {

                return true;
            }
            elseif( strtolower( $setting_value ) == 'false' )
            {

                return false;
            }

            if( $this->isJson( $setting_value ) && Settings::hasParsableData( $setting_value ) == false )
            {

                return json_decode( $setting_value, true );
            }

            return $setting_value;
        }

        /**
         * Returns true if the string is json
         *
         * @param $setting_value
         *
         * @return bool
         */

        private function isJson( $setting_value )
        {

            if( is_string( $setting_value ) == false )
            {

                return false;
            }

            json_decode( $setting_value );

            if( json_last_error() !== JSON_ERROR_NONE )
            {

                return false;
            }

            return true;
        }

        /**
         * Gets where to redirect the user too
         *
         * @param $path
         *
         * @return string
         */

        private function getRedirect( $path )
        {

            return Settings::getSetting('developer_page') . '/' . $path;
        }

        /**
         * Tells flight to render the page but with a prefix to save typing
         *
         * @param $file
         */

        private function getRender( $file, array $data=[])
        {

            Flight::render('developer/' . $file, $data );
        }
    }
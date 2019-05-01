<?php
    namespace Framework\Views\Pages;

    /**
     * Lewis Lancaster 2017
     *
     * Class Developer
     *
     * @package Framework\Views\Pages
     */

    use Framework\Application\ErrorHandler;
    use Framework\Application\Settings;
    use Framework\Application\Utilities\Cyphers;
    use Framework\Application\Utilities\FileSystem;
    use Framework\Application\Utilities\PostHelper;
    use Framework\Application\Render;
    use Framework\Database\Manager;
    use Framework\Exceptions\ViewException;
    use Framework\Views\BaseClasses\Page as BaseClass;
    use Framework\Views\Controller;
    use Framework\Views\Structures\Page;
    use Framework\Views\Structures\Page as Structure;
    use Illuminate\Database\Schema\Blueprint;
    use ReflectionClass;

    class Developer extends BaseClass implements Structure
    {

        /**
         * @var ErrorHandler
         */

        protected $errorhandler;

        /**
         * @var Controller
         */

        protected $controller;

        /**
         * @var Manager
         */

        protected $database;

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

            if( isset( $this->controller ) == false )
            {

                $this->controller = new Controller();
            }

            if( isset( $this->database ) == false )
            {

                try
                {

                    $this->database = new Manager();
                }
                catch( \Exception $error )
                {

                    //Stop redirect error
                }
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
                    'GET /developer/connection/creator/', 'connectionCreator'
                ],
                [
                    'POST /developer/connection/creator/', 'connectionCreatorProcess'
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
                    'GET /developer/disable/', 'disable'
                ],
                [
                    'POST /developer/disable/', 'disableProcess'
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
                    'GET /developer/migrator/', 'migrator'
                ],
                [
                    'POST /developer/migrator/', 'migratorProcess'
                ]
            );
        }

        /**
         * Renders the index page
         */

        public function index()
        {

            $this->getRender('developer/page.developer');
        }

        /**
         * Renders the database migrator page
         */

        public function migrator()
        {

            if( $this->database == null )
            {

                $this->redirectError('The database class failed to be created, this is usually due to the connection file not existing, maybe you should create one?', 'developer');
            }

            if( $this->hasDatabaseConnection() == false )
            {

                $this->redirectError('Your database connection is invalid, please make sure it is valid before using the migrator', 'developer' );
            }

            $this->getRender('developer/page.migrator');
        }

        /**
         * Processes the migrators post request
         */

        public function migratorProcess()
        {

            if( $this->database == null )
            {

                $this->redirectError('The database class failed to be created, this is usually due to the connection file not existing, maybe you should create one?', 'developer');
            }

            if( $this->hasDatabaseConnection() == false )
            {

                $this->redirectError('Your database connection is invalid, please make sure it is valid before using the migrator', 'developer' );
            }

            if( PostHelper::hasPostData() == false )
            {

                $this->migrator();
            }
            else
            {

                if( PostHelper::checkForRequirements(['json']) == false )
                {

                    $this->redirectError('Missing information', $this->getRedirect('migrator') );
                }

                $json = PostHelper::getPostData('json');

                if( $this->isJson( $json ) == false )
                {

                    $this->redirectError('Invalid Json: ' . json_last_error_msg(), $this->getRedirect('migrator') );
                }

                try
                {

                    $this->migrateDatabase( json_decode( $json, true ) );
                }
                catch( \Exception $error )
                {

                    $this->redirectError('Migrator Error: ' . $error->getMessage(), $this->getRedirect('migrator') );
                }

                $this->redirectSuccess( $this->getRedirect('migrator') );
            }
        }

        /**
         * Renders the page viewer page
         */

        public function routes()
        {

            $routes = $this->getRoutes();

            if( $routes == null )
            {

                $this->redirectError('No routes were found', 'developer' );
            }
            else
            {

                $this->getRender('developer/page.routes', array( 'routes' => $routes ) );
            }
        }

        /**
         * Renders the logger page
         */

        public function errors()
        {

            $this->getRender('developer/page.errors');
        }

        /**
         * Processes the post request to the error page
         */

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

                $this->getRender( 'developer/page.errors.view', array( 'id' => $id ) );
            }
        }

        /**
         * Renders the disable developer section page
         */

        public function disable()
        {

            $this->getRender('developer/page.disable');
        }

        public function disableProcess()
        {

            if( PostHelper::hasPostData() == false )
            {

                $this->disable();
            }
            else
            {

                if( PostHelper::checkForRequirements(['action'] ) == false )
                {

                    $this->redirectError('Missing information', $this->getRedirect('settings') );
                }

                $action = PostHelper::getPostData('action');

                if( $action == 'disable' )
                {

                    Settings::updateSetting('developer_disabled', true );

                    $this->redirectSuccess('index');
                }
            }
        }

        /**
         * Renders the settings manager
         */

        public function settings()
        {

            $this->getRender('developer/page.settings');
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

                    Settings::addSetting( $settings_name, $this->parseSetting( $settings_value ), true );

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

            $this->getRender('developer/page.connection.creator');
        }

        /**
         * Processes the connection creators post route
         */

        public function connectionCreatorProcess()
        {

            if( PostHelper::hasPostData() == false )
            {

                $this->connectionCreator();
            }
            else
            {

                if( PostHelper::checkForRequirements(['username','password','host','database'] ) == false )
                {

                    $this->redirectError('Missing information', $this->getRedirect('connection/creator') );
                }

                if( FileSystem::directoryExists('config/database/') == false )
                {

                    FileSystem::createDirectory('config/database/');
                }

                $array = $this->mergeDatabaseArrays( array(
                    'username'  => PostHelper::getPostData('username'),
                    'password'  => PostHelper::getPostData('password'),
                    'host'      => PostHelper::getPostData('host'),
                    'database'  => PostHelper::getPostData('database')
                ));

                if( Settings::getSetting('database_encrypt_connection') == true )
                {

                    FileSystem::writeJson( Settings::getSetting('database_connection_file'), Cyphers::encryptArray( $array, null, false ) );
                }
                else
                {

                    FileSystem::writeJson( Settings::getSetting('database_connection_file'), $array );
                }

                if( FileSystem::fileExists( Settings::getSetting('database_connection_file') ) == false )
                {

                    $this->redirectError('Failed to create connection file, this could be due to a permissions error', $this->getRedirect('connection/creator') );
                }

                $this->redirectSuccess( $this->getRedirect('connection/creator') );
            }
        }

        /**
         * Renders the database creator`
         */

        public function connection()
        {

            if( $this->database == null )
            {

                $this->redirectError('The database class failed to be created, this is usually due to the connection file not existing, maybe you should create one?', 'developer');
            }

            $this->getRender('developer/page.connection');
        }

        /**
         * Merges the database arrays together
         *
         * @param array $array
         *
         * @return array
         */

        private function mergeDatabaseArrays( array $array )
        {

            $merged = array(
                'driver'    =>  Settings::getSetting('database_driver'),
                'charset'   =>  Settings::getSetting('database_charset'),
                'collation' =>  Settings::getSetting('database_collation'),
                'prefix'    =>  Settings::getSetting('database_prefix')
            );

            return array_merge( $array, $merged );
        }

        /**
         * Migrates the database
         *
         * @param $payload
         */

        private function migrateDatabase( $payload )
        {

            foreach( $payload as $table=>$columns )
            {

                if( $this->tableExists( $table ) )
                {

                    continue;
                }

                Manager::$capsule->getConnection()->getSchemaBuilder()->create( $table, function( Blueprint $blueprint ) use ( $columns )
                {
                    foreach ($columns as $column => $type)
                    {
                        $blueprint->{$type}($column);
                    }
                });
            }
        }

        /**
         * Returns true if this table exits
         *
         * @param $table
         *
         * @return bool
         */

        private function tableExists( $table )
        {

            try
            {

                Manager::$capsule->getConnection()->table( strtolower( $table ) )->get();
            }
            catch( \Exception $error )
            {

                return false;
            }
            return true;
        }

        /**
         * Checks if we have a database connection
         *
         * @return bool
         */

        private function hasDatabaseConnection()
        {

            try
            {

                Manager::getCapsule()->getConnection()->getPdo();
            }
            catch( \Exception $error )
            {

                return false;
            }

            return true;
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

            if( is_numeric( $setting_value ) )
            {

                if(is_numeric( $setting_value ) && strpos( $setting_value, ".") !== false)
                {

                    return (float)$setting_value;
                }
                else
                {

                    return (int)$setting_value;
                }
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
         * Gets a list of the routes for all of the page files
         *
         * @return array|null
         */

        private function getRoutes()
        {

            $files = FileSystem::getFilesInDirectory( Settings::getSetting('controller_page_folder') );

            if( $files == null )
            {

                return null;
            }

            $routes = array();

            foreach( $files as $file )
            {

                $file = FileSystem::getFileName( $file );

                if( class_exists( Settings::getSetting('controller_namespace') . ucfirst( $file ) ) == false )
                {

                    throw new ViewException();
                }

                $reflection = new ReflectionClass( Settings::getSetting('controller_namespace') . ucfirst( $file )  );

                if( empty( $reflection ))
                {

                    throw new ViewException();
                }

                $class = $reflection->newInstanceWithoutConstructor();

                if( $class instanceof Page !== true )
                {

                    throw new ViewException();
                }

                $routes[ ucfirst( $file ) ] = $class->mapping();
            }

            if( empty( $routes ) )
            {

                throw new ViewException();
            }

            return $routes;
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
    }
<?php
    namespace Framework\Views\Pages;

    /**
     * Lewis Lancaster 2017
     *
     * Class Admin
     *
     * @package Framework\Views\Pages
     */

    use Flight;
    use Framework\Application\Container;
    use Framework\Application\Settings;
    use Framework\Application\Utilities\FileSystem;
    use Framework\Application\Utilities\PostHelper;
    use Framework\Syscrack\Game\Utilities\Startup;
    use Framework\Syscrack\User;
    use Framework\Views\BaseClasses\Page as BaseClass;
    use Framework\Views\Structures\Page as Structure;

    class Admin extends BaseClass implements Structure
    {

        /**
         * @var User
         */

        protected $user;

        /**
         * @var Startup
         */

        protected $startup;

        /**
         * Admin Error constructor.
         */

        public function __construct()
        {

            parent::__construct( true, true, true, true  );

            if( isset( $this->user ) == false )
            {

                $this->user = new User();
            }

            if( isset( $this->startup ) == false )
            {

                $this->startup = new Startup( null, false );
            }

            if( $this->user->isAdmin( Container::getObject('session')->getSessionUser() ) == false )
            {

                Flight::redirect( Settings::getSetting('controller_index_root') . Settings::getSetting('controller_index_page') );

                exit;
            }
        }

        /**
         * Returns the pages mapping
         *
         * @return array
         */

        public function mapping()
        {

            return array(
                [
                    '/admin/', 'page'
                ],
                [
                    'GET /admin/computer/', 'computerViewer'
                ],
                [
                    'POST /admin/computer/', 'computerSearch'
                ],
                [
                    'GET /admin/computer/creator/', 'computerCreator'
                ],
                [
                    'POST /admin/computer/creator/', 'computerCreatorProcess'
                ]
            );
        }

        /**
         * Default page
         */

        public function page()
        {

            Flight::render('syscrack/page.admin');
        }

        public function computerViewer()
        {

            Flight::render('syscrack/page.admin.computer');
        }

        public function computerSearch()
        {

            if( PostHelper::hasPostData() == false )
            {

                $this->computerViewer();
            }
            else
            {

                if( PostHelper::checkForRequirements( ['query'] ) == false )
                {

                    $this->redirectError('Please enter a search query', 'admin/computer');
                }

                $query = PostHelper::getPostData('query');

                if( filter_var( $query, FILTER_VALIDATE_IP) )
                {

                    if( $this->internet->ipExists( $query ) == false )
                    {

                        $this->redirectError('Address is invalid', 'admin/computer');
                    }

                    $this->redirect('admin/computer/' . $this->internet->getComputer( $query )->computerid );
                }
                else
                {

                    if( is_numeric( $query ) == false )
                    {

                        $this->redirectError('Invalid query', 'admin/computer' );
                    }

                    if( $this->computer->computerExists( $query ) == false )
                    {

                        $this->redirectError('Computer not found', 'admin/computer');
                    }

                    $this->redirect('admin/computer/' . $this->computer->getComputer( $query )->computerid );
                }
            }
        }

        /**
         * Renders the computer creator page
         */

        public function computerCreator()
        {

            Flight::render('syscrack/page.admin.computer.creator');
        }

        public function computerCreatorProcess()
        {

            if( PostHelper::hasPostData() == false )
            {

                $this->redirectError('Please fill in at least a few boxes..', 'admin/computer/creator');
            }

            if( PostHelper::checkForRequirements(['userid','ipaddress','type','hardwares','softwares'] ) == false )
            {

                $this->redirectError('Please fill in all of the fields required', 'admin/computer/creator');
            }
            else
            {

                $userid = PostHelper::getPostData('userid');
                $ipaddress = PostHelper::getPostData('ipaddress');
                $type = PostHelper::getPostData('type');
                $hardwares = PostHelper::getPostData('hardwares');
                $softwares = PostHelper::getPostData('softwares');

                if( $this->user->userExists( $userid ) == false )
                {

                    $this->redirectError('Please enter a userid that exists', 'admin/computer/creator');
                }

                if( $this->validAddress( $ipaddress ) == false )
                {

                    $this->redirectError('Please enter a valid address or one that does not exist', 'admin/computer/creator');
                }

                if( $this->isValidJson( $hardwares ) == false || $this->isValidJson( $softwares ) == false )
                {

                    $this->redirectError('Json Error: ' . $this->getLastJsonError(), 'admin/computer/creator');
                }

                $computerid = $this->startup->createComputer( $userid, $type, $ipaddress, [], json_decode( $hardwares, true ) );

                if( $this->startup->log->hasLog( $computerid ) == false )
                {

                    $this->startup->createComputerLog( $computerid );
                }

                $this->startup->createComputerSoftware( $userid, $computerid, json_decode( $softwares, true ) );

                if( $this->computer->getComputerType( $computerid ) == Settings::getSetting('syscrack_computer_market_type') )
                {

                    $this->startupMarket( $computerid );
                }

                if( $this->computer->getComputerType( $computerid ) == Settings::getSetting('syscrack_computer_retailer_type') )
                {

                    $this->startupRetailer( $computerid );
                }

                if( PostHelper::checkForRequirements( ['schema'] ) == true  )
                {

                    if( PostHelper::getPostData('schema') == true )
                    {

                        if( PostHelper::checkForRequirements(['name']) == false )
                        {

                            $name = Settings::getSetting('syscrack_default_computer_name');
                        }
                        else
                        {

                            $name = PostHelper::getPostData('name');
                        }

                        if( PostHelper::checkForRequirements(['page']) == false )
                        {

                            $page = Settings::getSetting('syscrack_default_computer_page');
                        }
                        else
                        {

                            $page = PostHelper::getPostData('page');
                        }

                        if( PostHelper::checkForRequirements(['riddle']) == false )
                        {

                            $this->startup->createSchema( $computerid, array(
                                'name' => $name,
                                'page'  => $page,
                                'softwares' => json_decode( $softwares, true ),
                                'hardwares' => json_decode( $hardwares, true )
                            ));
                        }
                        else
                        {

                            if( PostHelper::checkForRequirements( ['riddleaddress'] ) == false )
                            {

                                $riddle = null;
                            }
                            else
                            {

                                $riddle = PostHelper::getPostData('riddleaddress');
                            }

                            $this->startup->createSchema( $computerid, array(
                                'name'      => $name,
                                'page'      => $page,
                                'riddle'    => $riddle,
                                'softwares' => json_decode( $softwares, true ),
                                'hardwares' => json_decode( $hardwares, true )
                            ));
                        }
                    }
                }

                $this->redirectSuccess('admin/computer/creator');
            }
        }

        /**
         * Start ups a market server
         *
         * @param $computerid
         */

        private function startupMarket( $computerid )
        {

            if( FileSystem::directoryExists( $this->getFilePath( $computerid ) ) == false )
            {

                FileSystem::createDirectory(  $this->getFilePath( $computerid ) );
            }

            if( FileSystem::fileExists( $this->getFilePath( $computerid, true, 'stocks.json' ) ) == false )
            {

                FileSystem::writeJson( $this->getFilePath( $computerid, true, 'stocks.json') );
            }

            if( FileSystem::fileExists( $this->getFilePath( $computerid, true, 'purchases.json' ) ) == false )
            {

                FileSystem::writeJson( $this->getFilePath( $computerid, true, 'purchases.json') );
            }
        }

        /**
         * Starts up the retailer server
         *
         * @param $computerid
         */

        private function startupRetailer( $computerid )
        {

            if( FileSystem::directoryExists( $this->getFilePath( $computerid , false) ) == false )
            {

                FileSystem::createDirectory(  $this->getFilePath( $computerid ) );
            }

            if( FileSystem::fileExists( $this->getFilePath( $computerid, false, 'stocks.json' ) ) == false )
            {

                FileSystem::writeJson( $this->getFilePath( $computerid, true, 'stocks.json') );
            }
        }

        /**
         * Gets the filepath of both the market and retailer servers for setup
         *
         * //TODO: move this to an automated class based computer system
         *
         * @param $computerid
         *
         * @param bool $market
         *
         * @param string $file
         *
         * @return string
         */

        private function getFilePath( $computerid, $market=true, $file='' )
        {

            if( $market )
            {

                return Settings::getSetting('syscrack_market_location') . $computerid . '/' . $file;
            }
            else
            {

                return Settings::getSetting('syscrack_retailer_location') . $computerid . '/' . $file;
            }
        }

        /**
         * Gets the last json error
         *
         * @return string
         */

        private function getLastJsonError()
        {

            return json_last_error_msg();
        }

        /**
         * Retuns true the address given is valid
         *
         * @param $ipaddress
         *
         * @return bool
         */

        private function validAddress( $ipaddress )
        {

            if (filter_var($ipaddress, FILTER_VALIDATE_IP) == false)
            {

                return false;
            }

            if ($this->internet->ipExists($ipaddress) == true)
            {

                return false;
            }

            return true;
        }

        /**
         * Returns true if this is valid json
         *
         * @param $data
         *
         * @return bool
         */

        private function isValidJson( $data )
        {

            $array = json_decode( $data, true );

            if( json_last_error() !== JSON_ERROR_NONE )
            {

                return false;
            }

            return true;
        }
    }
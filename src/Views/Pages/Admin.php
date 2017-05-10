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
                    'GET /admin/computer/creator', 'computerCreator'
                ],
                [
                    'POST /admin/computer/creator', 'computerCreatorProcess'
                ]
            );
        }

        /**
         * Default page
         */

        public function page()
        {

            Flight::render('syscrack/page.admin.php');
        }

        /**
         * Renders the computer creator page
         */

        public function computerCreator()
        {

            Flight::render('syscrack/page.admin.computer.creator.php');
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

                //We don't add the softwares as they need to be created using the createComputerSoftware method instead
                $computerid = $this->startup->createComputer( $userid, $type, $ipaddress, [], json_decode( $hardwares, true ) );

                if( $this->startup->log->hasLog( $computerid ) == false )
                {

                    $this->startup->createComputerLog( $computerid );
                }

                $this->startup->createComputerSoftware( $userid, $computerid, json_decode( $softwares, true ) );

                if( PostHelper::checkForRequirements( ['schema'] ) == true  )
                {

                    if( PostHelper::getPostData('schema') == true )
                    {

                        if( PostHelper::checkForRequirements(['name']) == false )
                        {

                            $this->redirectError('Unable to create schema due to missing information, but the computer was created...', 'admin/computer/creator');
                        }

                        if( PostHelper::checkForRequirements(['page'] ) == false )
                        {

                            $name = PostHelper::getPostData('name');
                            $page = Settings::getSetting('syscrack_default_computer_page');

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

                                    $this->redirectError('Unable to create schema due to missing information, but the computer was created', 'admin/computer/creator');
                                }

                                $riddle = PostHelper::getPostData('riddladdress');

                                $this->startup->createSchema( $computerid, array(
                                    'name'      => $name,
                                    'page'      => $page,
                                    'riddle'    => $riddle,
                                    'softwares' => json_decode( $softwares, true ),
                                    'hardwares' => json_decode( $hardwares, true )
                                ));
                            }
                        }
                        else
                        {

                            $name = PostHelper::getPostData('name');
                            $page = PostHelper::getPostData('page');

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

                                    $this->redirectError('Unable to create schema due to missing information, but the computer was created', 'admin/computer/creator');
                                }

                                $riddle = PostHelper::getPostData('riddladdress');

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
                }

                $this->redirectSuccess('admin/computer/creator');
            }
        }

        private function getLastJsonError()
        {

            return json_last_error_msg();
        }

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
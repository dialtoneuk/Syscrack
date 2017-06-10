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
    use Framework\Exceptions\SyscrackException;
    use Framework\Syscrack\Game\Finance;
    use Framework\Syscrack\Game\Schema;
    use Framework\Syscrack\Game\Structures\Computer;
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
         * @var Schema
         */

        protected $schema;

        /**
         * @var Finance
         */

        protected $finance;

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

            if ($this->user->isAdmin(Container::getObject('session')->getSessionUser()) == false)
            {

                Flight::redirect(Settings::getSetting('controller_index_root') . Settings::getSetting('controller_index_page'));

                exit;
            }

            if (isset( $this->schema ) == false)
            {

                $this->schema = new Schema();
            }

            if (isset($this->finance) == false)
            {

                $this->finance = new Finance();
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
                    'GET /admin/computer/@computerid:[0-9]{9}/', 'computerEditor'
                ],
                [
                    'POST /admin/computer/@computerid:[0-9]{9}/', 'computerEditorProcess'
                ],
                [
                    'GET /admin/riddles/','riddlesViewer'
                ],
                [
                    'POST /admin/riddles','riddlesViewerProcess'
                ],
                [
                    'GET /admin/riddles/creator/','riddlesCreator'
                ],
                [
                    'POST /admin/riddles/creator','riddlesCreatorProcess'
                ],
                [
                    'GET /admin/computer/creator/', 'computerCreator'
                ],
                [
                    'POST /admin/computer/creator/', 'computerCreatorProcess'
                ],
                [
                    'GET /admin/reset/', 'reset'
                ],
                [
                    'POST /admin/reset/', 'resetprocess'
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

        public function computerEditor()
        {


        }

        public function computerEditorProcess()
        {


        }

        public function riddlesViewer()
        {

            Flight::render('syscrack/page.admin.riddles');
        }

        public function riddlesViewerProcess()
        {


        }

        public function riddlesCreator()
        {

            Flight::render('syscrack/page.admin.riddles.creator');
        }

        public function riddlesCreatorProcess()
        {

        }

        public function reset()
        {

            Flight::render('syscrack/page.admin.reset');
        }

        public function resetProcess()
        {

            if (PostHelper::hasPostData() == false)
            {

                $this->reset();
            }
            else
            {

                if( PostHelper::checkForRequirements(['resetip'] ) == true )
                {

                    $computers = $this->computers->getAllComputers( $this->computers->getComputerCount() );

                    foreach( $computers as $computer )
                    {

                        $this->internet->changeAddress( $computer->computerid );
                    }
                }

                $this->resetComputers();

                if (PostHelper::checkForRequirements(['clearfinance']) == true)
                {

                    $this->cleanAccounts();
                }

                $this->redirectSuccess('admin/reset');
            }
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

                    if( $this->computers->computerExists( $query ) == false )
                    {

                        $this->redirectError('Computer not found', 'admin/computer');
                    }

                    $this->redirect('admin/computer/' . $this->computers->getComputer( $query )->computerid );
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

        /**
         * Processes a post request to the computer creator
         */

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

                if( $this->computers->hasComputerClass( $type ) == false )
                {

                    $this->redirectError('Type of computer is invalid and does not exist');
                }

                $class = $this->computers->getComputerClass( $type );

                if( $class instanceof Computer == false )
                {

                    throw new SyscrackException();
                }

                $computerid = $this->computers->createComputer( $userid, $type, $ipaddress );

                $class->onStartup( $computerid, $userid, json_decode( $softwares, true ), json_decode( $hardwares, true ) );

                if( PostHelper::checkForRequirements( ['schema'] ) == true )
                {

                    if( PostHelper::checkForRequirements(['name','page']) == false )
                    {

                        $this->redirectError('Schema failed to be created but your computer was created', 'admin/computer/creator' );
                    }

                    $name = PostHelper::getPostData('name');

                    $page = PostHelper::getPostData('page');

                    if( PostHelper::checkForRequirements( ['riddle'] ) )
                    {

                        if( PostHelper::checkForRequirements(['riddleid','riddlecomputer'] ) == false )
                        {

                            $this->redirectError('Schema failed to be created but your computer was created', 'admin/computer/creator' );
                        }

                        $riddles = array(
                            'riddleid'          => PostHelper::getPostData('riddleid'),
                            'riddlecomputer'    => PostHelper::getPostData('riddlecomputer')
                        );
                    }
                    else
                    {

                        $riddles = array();
                    }

                    $this->schema->createSchema( $computerid, $name, $page, $riddles, json_decode( $hardwares, true ), json_decode( $softwares, true ) );
                }

                $this->redirectSuccess('admin/computer/creator');
            }
        }

        /**
         * Resets all the computers using the schema file if it is found
         */

        private function resetComputers()
        {

            $computers = $this->computers->getAllComputers($this->computers->getComputerCount());

            foreach( $computers as $computer )
            {

                if( $this->computers->hasComputerClass( $computer->type ) == false )
                {

                    continue;
                }

                $class = $this->computers->getComputerClass( $computer->type );

                if( $class instanceof Computer == false )
                {

                    throw new SyscrackException();
                }

                $class->onReset( $computer->computerid );
            }
        }

        /**
         * Cleans all the bank accounts in the game
         */

        private function cleanAccounts()
        {

            $accounts = $this->finance->getAllAccounts($this->finance->getAccountCount());

            foreach ($accounts as $account)
            {

                $this->finance->removeAccount($account->computerid, $account->userid);
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
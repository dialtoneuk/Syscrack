<?php
    namespace Framework\Views\Pages;

    /**
     * Lewis Lancaster 2017
     *
     * Class Admin
     *
     * @package Framework\Views\Pages
     */

    use Framework\Application\Render;
    use Framework\Application\Container;
    use Framework\Application\Settings;
    use Framework\Application\Utilities\PostHelper;
    use Framework\Exceptions\SyscrackException;
    use Framework\Exceptions\ViewException;
    use Framework\Syscrack\Game\Finance;
    use Framework\Syscrack\Game\Market;
    use Framework\Syscrack\Game\Riddles;
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

                Render::redirect(Settings::getSetting('controller_index_root') . Settings::getSetting('controller_index_page'));

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
                    'GET /admin/computer/edit/@computerid/', 'computerEditor'
                ],
                [
                    'POST /admin/computer/edit/@computerid/', 'computerEditorProcess'
                ],
                [
                    'GET /admin/users/','usersViewer'
                ],
                [
                    'POST /admin/users/','usersSearch'
                ],
                [
                    'GET /admin/users/edit/@userid/','usersEdit'
                ],
                [
                    'POST /admin/users/edit/@userid/','usersEditProcess'
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

            Render::view('syscrack/page.admin');
        }

        public function usersViewer()
        {

            Render::view('syscrack/page.admin.users');
        }

        public function usersSearch()
        {


        }

        public function usersEdit( $userid )
        {

            if ( $this->user->userExists( $userid ) == false )
            {

                $this->redirectError('This user does not exist, please try another', '/admin/users/');
            }
            else
            {

                Render::view('syscrack/page.admin.users.edit', array('userid' => $userid, 'users' => $this->user ));
            }
        }

        public function usersEditProcess( $userid )
        {

            if ( $this->user->userExists( $userid ) == false )
            {

                $this->redirectError('This user does not exist', "admin/users/edit/" . $userid. "/"  );
            }
            else
            {

                if ( PostHelper::hasPostData() == false )
                {

                    $this->redirectError( 'Post data is false', "admin/users/edit/" . $userid  );
                }
                else
                {

                    if ( PostHelper::checkForRequirements(['action']) == false )
                    {

                        $this->redirectError( 'Post data is false', "admin/users/edit/" . $userid  );
                    }
                    else
                    {

                        $action = PostHelper::getPostData('action', true );

                        if ( $action == "group" )
                        {

                            if ( PostHelper::checkForRequirements(['group'] ) == false )
                            {

                                $this->redirectError( 'Post data is false', "admin/users/edit/" . $userid  );
                            }
                            else
                            {

                                $this->user->updateGroup( $userid, PostHelper::getPostData('group', true ) );

                                $this->redirectSuccess('admin/users/edit/' . $userid );
                            }
                        }
                    }
                }
            }
        }

        public function computerEditor( $computerid )
        {

            if( $this->computers->computerExists( $computerid ) == false )
            {

                $this->redirectError('This computer does not exist, please try another');
            }

            $computer = $this->computers->getComputer( $computerid );

            Render::view('syscrack/page.admin.computer.edit', array( 'computer' => $computer ), $this->model() );
        }

        public function computerEditorProcess( $computerid )
        {

            if ( $this->computers->computerExists( $computerid ) == false )
            {

                $this->redirectError('This computer does not exist, please try another', "admin/computers/edit/" . $computerid );
            }

            if ( PostHelper::hasPostData() == false )
            {

                $this->redirect('admin/computer');
            }
            else
            {

                if ( isset( $_POST["action"]) == false )
                {

                    $this->redirectError('Incomplete Data', "admin/computer/edit/" . $computerid );
                }
                else
                {

                    $action = PostHelper::getPostData('action', true );

                    if ( $action == "add" )
                    {

                        $requirements = [
                            'name',
                            'level',
                            'uniquename',
                            'size'
                        ];

                        if ( PostHelper::checkForRequirements( $requirements ) == false )
                        {

                            $this->redirectError('Incomplete Data', "admin/computer/edit/" . $computerid );
                        }
                        else
                        {

                            if ( isset( $_POST['customdata'] ) && empty( $_POST['customdata'] ) == false )
                            {

                                $customdata = json_decode( $_POST['customdata'], true );

                                if ( json_last_error() !==  JSON_ERROR_NONE )
                                {

                                    $this->redirectError('Invalid data array',"admin/computer/edit/" . $computerid );
                                }
                            }
                            else
                            {

                                $customdata = [];
                            }

                            if ( isset( $_POST['editable'] ) )
                            {

                                $customdata['editable'] = true;
                            }
                            else
                            {

                                $customdata['editable'] = false;
                            }

                            if ( isset( $_POST['anondownloads'] ) )
                            {

                                $customdata['allowanondownloads'] = true;
                            }

                            $softwareid = $this->softwares->createSoftware(
                                $this->softwares->getNameFromClass(
                                    $this->softwares->findSoftwareByUniqueName( PostHelper::getPostData('uniquename', true ) )),
                                    $this->computers->getComputer( $computerid )->userid,
                                    $computerid,
                                    PostHelper::getPostData('name', true ),
                                    PostHelper::getPostData('level', true ),
                                    PostHelper::getPostData('size', true ),
                                    $customdata
                                );

                            $software = $this->softwares->getSoftware( $softwareid );

                            $this->computers->addSoftware( $computerid, $software->softwareid, $software->type);

                            if ( isset( $_POST['schema'] ) )
                            {

                                $this->addToSchema( $computerid, $software );
                            }

                            $this->redirectSuccess('admin/computer/edit/' . $computerid);
                        }
                    }
                    elseif ( $action == "stall")
                    {

                        $requirements = [
                            'softwareid',
                            'task'
                        ];

                        if ( PostHelper::checkForRequirements( $requirements ) == false )
                        {

                            $this->redirectError('Incomplete Data', "admin/computer/edit/" . $computerid );
                        }
                        else
                        {

                            if ( $this->softwares->softwareExists( PostHelper::getPostData('softwareid', true ) ) == false )
                            {

                                $this->redirectError('Invalid Software', "admin/computer/edit/" . $computerid );
                            }

                            if ( PostHelper::getPostData('task', true ) == 'install' )
                            {

                                $this->softwares->installSoftware( PostHelper::getPostData('softwareid', true ),
                                    $this->computers->getComputer( $computerid )->userid );

                                $this->computers->installSoftware( $computerid,  PostHelper::getPostData('softwareid', true ) );
                            }
                            else
                            {

                                $this->softwares->uninstallSoftware( PostHelper::getPostData('softwareid', true ) );

                                $this->computers->uninstallSoftware( $computerid,  PostHelper::getPostData('softwareid', true ) );
                            }

                            $this->redirectSuccess('admin/computer/edit/' . $computerid);
                        }
                    }
                    elseif ( $action == "delete")
                    {

                        $requirements = [
                            'softwareid',
                        ];

                        if ( PostHelper::checkForRequirements( $requirements ) == false )
                        {

                            $this->redirectError('Incomplete Data', "admin/computer/edit/" . $computerid );
                        }
                        else
                            {

                            if ($this->softwares->softwareExists(PostHelper::getPostData('softwareid', true)) == false) {

                                $this->redirectError('Invalid Software', "admin/computer/edit/" . $computerid);
                            }

                            $this->softwares->deleteSoftware(   PostHelper::getPostData('softwareid', true ) );

                            $this->computers->removeSoftware( $computerid,  PostHelper::getPostData('softwareid', true ) );

                            $this->redirectSuccess('admin/computer/edit/' . $computerid);
                        }
                    }
                    elseif ( $action == "stock")
                    {

                        $requirements = [
                            'name',
                            'type',
                            'cost',
                            'quantity'
                        ];

                        if ( PostHelper::checkForRequirements( $requirements ) == false )
                        {

                            $this->redirectError('Incomplete Data', "admin/computer/edit/" . $computerid );
                        }
                        else
                        {

                            $market = new Market();

                            if ( $this->computers->isMarket( $computerid ) == false )
                            {

                                $this->redirectError('Wrong computer type', "admin/computer/edit/" . $computerid );
                            }
                            else
                            {

                                if ( PostHelper::getPostData('type', true ) == 'hardware' )
                                {

                                    if ( empty( $_POST['value'] ) || empty( $_POST['hardware'] ) )
                                    {

                                        $this->redirectError('Incomplete Data', "admin/computer/edit/" . $computerid );
                                    }
                                    else
                                    {

                                        $stock = [
                                            'name' => PostHelper::getPostData('name', true ),
                                            'type' => PostHelper::getPostData('type', true ),
                                            'price' => PostHelper::getPostData('cost', true ),
                                            'quantity' => PostHelper::getPostData('quantity', true ),
                                            'hardware' => PostHelper::getPostData('hardware', true ),
                                            'value' => PostHelper::getPostData('value', true )
                                        ];

                                        $market->addStockItem( $computerid, base64_encode( openssl_random_pseudo_bytes(16) ), $stock );

                                        $this->redirectSuccess( "admin/computer/edit/" . $computerid );
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        public function riddlesViewer()
        {

            Render::view('syscrack/page.admin.riddles', [], $this->model());
        }

        public function riddlesViewerProcess()
        {


        }

        public function riddlesCreator()
        {

            Render::view('syscrack/page.admin.riddles.creator', [], $this->model());
        }

        public function riddlesCreatorProcess()
        {

            if ( PostHelper::hasPostData() == false )
            {

                $this->redirectError();
            }
            else
            {

                if ( PostHelper::checkForRequirements(['question', 'answer'] ) == false )
                {

                    $this->redirectError();
                }
                else
                {

                    $riddles = new Riddles();

                    $riddles->addRiddle( PostHelper::getPostData('question', true), PostHelper::getPostData('answer', true ));

                    $this->redirectSuccess();
                }
            }
        }

        public function reset()
        {

            Render::view('syscrack/page.admin.reset', [], $this->model());
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

            Render::view('syscrack/page.admin.computer', [], $this->model());
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

            Render::view('syscrack/page.admin.computer.creator', [], $this->model());
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

                    $this->schema->createSchema( $computerid, $name, $page, $riddles, json_decode( $softwares, true ), json_decode( $hardwares, true ) );
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

        private function addToSchema( $computerid, $software )
        {

            $schema = new Schema();

            if ( $schema->hasSchema( $computerid ) == false )
            {

                return null;
            }

            $computerschema = $schema->getSchema( $computerid );

            if ( isset( $computerschema['softwares'] ) == false )
            {

                throw new ViewException();
            }

            $computerschema['softwares'][] = [
                "installed"     => $software->installed,
                "level"         => $software->level,
                "name"          => $software->softwarename,
                "size"          => $software->size,
                "uniquename"    => $software->uniquename,
                "data"          => json_decode( $software->data, true )
            ];

            $schema->setSchema( $computerid, $computerschema );
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
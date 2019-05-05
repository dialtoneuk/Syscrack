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
    use Framework\Application\UtilitiesV2\Conventions\CreatorData;
    use Framework\Exceptions\SyscrackException;
    use Framework\Exceptions\ViewException;
    use Framework\Syscrack\Game\Finance;
    use Framework\Syscrack\Game\Market;
    use Framework\Syscrack\Game\Riddles;
    use Framework\Syscrack\Game\Schema;
    use Framework\Syscrack\Game\Structures\Computer;
    use Framework\Syscrack\User;
    use Framework\Syscrack\Game\Themes;
    use Framework\Views\BaseClasses\Page as BaseClass;
    use Framework\Views\Structures\Page as Structure;

    class Admin extends BaseClass implements Structure
    {

        /**
         * @var Schema
         */

        protected $schema;

        /**
         * @var Finance
         */

        protected $finance;

        /**
         * @var Themes
         */

        protected $themes;

        /**
         * Admin Error constructor.
         */

        public function __construct()
        {

            if (isset( $this->schema ) == false)
                $this->schema = new Schema();

            if (isset($this->finance) == false)
                $this->finance = new Finance();

            if( isset( $this->themes ) == false )
                $this->themes = new Themes( true );

            parent::__construct( true, true, true, false, true );
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
                    'POST /admin/computer/', 'computerearch'
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
                    'POST /admin/reset/', 'resetProcess'
                ],
                [
                    'GET /admin/themes/', 'themes'
                ],
                [
                    'POST /admin/themes/', 'themesProcess'
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

        /**
         *
         */

        public function usersViewer()
        {

            Render::view('syscrack/page.admin.users');
        }

        /**
         *
         */

        public function usersSearch()
        {


        }

        /**
         * @param $userid
         */

        public function usersEdit( $userid )
        {

            if ( $this->isUser( $userid ) )
                Render::view('syscrack/page.admin.users.edit', array('userid' => $userid, 'users' => $this->user ));
            else
                $this->redirectError('This user does not exist, please try another', 'admin/users/');
        }

        /**
         * @param $userid
         */

        public function usersEditProcess( $userid )
        {

            if ( $this->isUser( $userid ) == false )
                $this->redirectError('This user does not exist', "admin/users/edit/" . $userid. "/"  );
            else
            {

                if ( PostHelper::hasPostData() == false )
                    $this->redirectError( 'Post data is false', "admin/users/edit/" . $userid  );
                else
                {

                    if ( PostHelper::checkForRequirements(['action']) == false )
                        $this->redirectError( 'Post data is false', "admin/users/edit/" . $userid  );
                    else
                    {

                        $action = PostHelper::getPostData('action', true );
                        if ( $action == "group" )
                        {

                            if ( PostHelper::checkForRequirements(['group'] ) == false )
                                $this->redirectError( 'Post data is false', "admin/users/edit/" . $userid  );
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

        /**
         * Themes View
         */

        public function themes()
        {

            Render::view('syscrack/page.admin.themes', array("themes" => $this->themes->getThemes( false ) ), $this->model() );
        }

        /**
         * @param $theme
         */

        public function themesProcess()
        {

            if( PostHelper::checkForRequirements(['theme']) == false )
                $this->redirectError("This theme does not exist");
            else
                $theme = PostHelper::getPostData('theme', true );

            if( $this->themes->themeExists( $theme ) == false )
                $this->redirectError("This theme does not exist");
            else
                $this->themes->set( $theme );
        }

        /**
         * @param $computerid
         */

        public function computerEditor( $computerid )
        {

            if( $this->computer->computerExists( $computerid ) == false )
                $this->redirectError('This computer does not exist, please try another');
            else
            {
                $computer = $this->computer->getComputer($computerid);
                Render::view('syscrack/page.admin.computer.edit', array('computer' => $computer, 'ipaddress' => $computer->ipaddress), $this->model());
            }
        }

        /**
         * @param $computerid
         */

        public function computerEditorProcess( $computerid )
        {

            if ( $this->computer->computerExists( $computerid ) == false )
                $this->redirectError('This computer does not exist, please try another', "admin/computer/edit/" . $computerid );

            if ( PostHelper::hasPostData() == false )
                $this->redirect('admin/computer');
            else
            {

                if ( isset( $_POST["action"]) == false )
                    $this->redirectError('Incomplete Data', "admin/computer/edit/" . $computerid );
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
                            $this->redirectError('Incomplete Data', "admin/computer/edit/" . $computerid );
                        else
                        {

                            if ( isset( $_POST['customdata'] ) && empty( $_POST['customdata'] ) == false )
                            {

                                $customdata = json_decode( $_POST['customdata'], true );

                                if ( json_last_error() !==  JSON_ERROR_NONE )
                                    $this->redirectError('Invalid data array',"admin/computer/edit/" . $computerid );
                            }
                            else
                                $customdata = [];

                            if ( isset( $_POST['editable'] ) )
                                $customdata['editable'] = true;
                            else
                                $customdata['editable'] = false;

                            if ( isset( $_POST['anondownloads'] ) )
                                $customdata['allowanondownloads'] = true;

                            $softwareid = $this->software->createSoftware(
                                $this->software->getNameFromClass(
                                    $this->software->findSoftwareByUniqueName( PostHelper::getPostData('uniquename', true ) )),
                                    $this->computer->getComputer( $computerid )->userid,
                                    $computerid,
                                    PostHelper::getPostData('name', true ),
                                    PostHelper::getPostData('level', true ),
                                    PostHelper::getPostData('size', true ),
                                    $customdata
                                );

                            $software = $this->software->getSoftware( $softwareid );

                            $this->computer->addSoftware( $computerid, $software->softwareid, $software->type);

                            if ( isset( $_POST['schema'] ) )
                                $this->addToSchema( $computerid, $software );

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

                            if ( $this->software->softwareExists( PostHelper::getPostData('softwareid', true ) ) == false )
                            {

                                $this->redirectError('Invalid Software', "admin/computer/edit/" . $computerid );
                            }

                            if ( PostHelper::getPostData('task', true ) == 'install' )
                            {

                                $this->software->installSoftware( PostHelper::getPostData('softwareid', true ),
                                    $this->computer->getComputer( $computerid )->userid );

                                $this->computer->installSoftware( $computerid,  PostHelper::getPostData('softwareid', true ) );
                            }
                            else
                            {

                                $this->software->uninstallSoftware( PostHelper::getPostData('softwareid', true ) );

                                $this->computer->uninstallSoftware( $computerid,  PostHelper::getPostData('softwareid', true ) );
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

                            if ($this->software->softwareExists(PostHelper::getPostData('softwareid', true)) == false) {

                                $this->redirectError('Invalid Software', "admin/computer/edit/" . $computerid);
                            }

                            $this->software->deleteSoftware(   PostHelper::getPostData('softwareid', true ) );

                            $this->computer->removeSoftware( $computerid,  PostHelper::getPostData('softwareid', true ) );

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

                            if ( $this->computer->isMarket( $computerid ) == false )
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

        /**
         *
         */

        public function riddlesViewer()
        {

            Render::view('syscrack/page.admin.riddles', [], $this->model());
        }

        /**
         *
         */

        public function riddlesViewerProcess()
        {


        }

        /**
         *
         */

        public function riddlesCreator()
        {

            Render::view('syscrack/page.admin.riddles.creator', [], $this->model());
        }

        /**
         *
         */

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

        /**
         *
         */

        public function reset()
        {

            Render::view('syscrack/page.admin.reset', [], $this->model());
        }

        /**
         *
         */

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

                    $computer = $this->computer->getAllComputers( $this->computer->getComputerCount() );

                    foreach( $computer as $computers )
                    {

                        $this->internet->changeAddress( $computers->computerid );
                    }
                }

                $this->resetcomputer();

                if (PostHelper::checkForRequirements(['clearfinance']) == true)
                {

                    $this->cleanAccounts();
                }

                $this->redirectSuccess('admin/reset');
            }
        }

        /**
         *
         */

        public function computerViewer()
        {

            Render::view('syscrack/page.admin.computer', [], $this->model());
        }

        /**
         *
         */

        public function computerearch()
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

        private $path = 'admin/computer/creator';

        public function computerCreator()
        {

            Render::view('syscrack/page.admin.computer.creator', [], $this->model());
        }

        /**
         * Creates a new computer
         */

        public function computerCreatorProcess()
        {

            if( PostHelper::hasPostData() == false )
                $this->redirectError( 'Missing information', $this->path );
            elseif( PostHelper::checkForRequirements(['userid','ipaddress','type','hardware','software']) == false )
                $this->redirectError('Missing information', $this->path );
            else
            {

                $values = [
                    'userid'       => PostHelper::getPostData('userid'),
                    'ipaddress'    => PostHelper::getPostData('ipaddress'),
                    'type'         => PostHelper::getPostData('type'),
                    'hardware'    => PostHelper::getPostData('hardware'),
                    'software'    => PostHelper::getPostData('software')
                ];

                if( $this->isValidJson( $values["hardware"] ) == false || $this->isValidJson( $values["software"] ) == false )
                    $this->redirectError("Json is invalid");
                else
                {

                    if( is_numeric( $values["userid"] ) == false )
                        $this->redirectError("Invalid userid must be numerical");
                    else
                    {

                        $values["userid"] = (int)$values["userid"];
                        $values["hardware"] = json_decode( $values["hardware"], true );
                        $values["software"] = json_decode( $values["software"], true );

                        $object = new CreatorData( $values );

                        if( $this->isUser( $object->userid ) == false )
                            $this->redirectError("Userid is invalid", $this->path );
                        elseif( $this->validAddress( $object->ipaddress ) == false )
                            $this->redirectError("Address is invalid", $this->path);
                        elseif( $this->computer->hasComputerClass( $object->type ) == false )
                            $this->redirectError("Unknown type of computer");

                        $computerid = $this->computer->createComputer( $object->userid,  $object->type,  $object->ipaddress, $object->software, $object->hardware );
                        $class      = $this->computer->getComputerClass( $object->type );

                        if( $class instanceof Computer == false )
                            throw new \Error("Instanceof check returned false");

                        $class->onStartup( $computerid, $object->userid, $object->software, $object->hardware );
                        $this->redirectSuccess( $this->path );
                    }
                }
            }
        }

        /**
         * Resets all the computer using the schema file if it is found
         */

        private function resetcomputer()
        {

            $computer = $this->computer->getAllComputers($this->computer->getComputerCount());

            foreach( $computer as $computers )
            {

                if( $this->computer->hasComputerClass( $computers->type ) == false )
                {

                    continue;
                }

                $class = $this->computer->getComputerClass( $computers->type );

                if( $class instanceof Computer == false )
                {

                    throw new SyscrackException();
                }

                $class->onReset( $computers->computerid );
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

            $computerchema = $schema->getSchema( $computerid );

            if ( isset( $computerchema['software'] ) == false )
            {

                throw new ViewException();
            }

            $computerchema['software'][] = [
                "installed"     => $software->installed,
                "level"         => $software->level,
                "name"          => $software->softwarename,
                "size"          => $software->size,
                "uniquename"    => $software->uniquename,
                "data"          => json_decode( $software->data, true )
            ];

            $schema->setSchema( $computerid, $computerchema );
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
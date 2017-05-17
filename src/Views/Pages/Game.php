<?php
    namespace Framework\Views\Pages;

    /**
     * Lewis Lancaster 2017
     *
     * Class Game
     *
     * @package Framework\Views\Pages
     */

    use Flight;
    use Framework\Application\Container;
    use Framework\Application\Session;
    use Framework\Application\Settings;
    use Framework\Application\Utilities\PostHelper;
    use Framework\Exceptions\SyscrackException;
    use Framework\Syscrack\Game\Operations;
    use Framework\Views\BaseClasses\Page as BaseClass;
    use Framework\Views\Structures\Page as Structure;

    class Game extends BaseClass implements Structure
    {


        /**
         * @var Operations
         */

        protected $operations;

        /**
         * @var Session
         */

        protected $session;

        /**
         * Game constructor.
         */

        public function __construct()
        {

            parent::__construct( true, true, true, true );

            if( isset( $this->operations ) == false )
            {

                $this->operations = new Operations();
            }

            if( isset( $this->session ) == false )
            {

                if( Container::hasObject('session') == false )
                {

                    Container::setObject('session', new Session() );
                }

                $this->session = Container::getObject('session');
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
                    'GET /game/', 'page'
                ],
                [
                    'POST /game/', 'pageProcess'
                ],
                [
                    '/game/internet/', 'internetBrowser'
                ],
                [
                    '/game/addressbook/', 'addressBook'
                ],
                [
                    '/game/internet/@ipaddress/', 'viewAddress'
                ],
                [
                    '/game/internet/@ipaddress/@process', 'process'
                ],
                [
                    '/game/internet/@ipaddress/@process/@softwareid', 'processSoftware'
                ]
            );
        }

        /**
         * Switches your computer
         */

        public function pageProcess()
        {

            if (PostHelper::hasPostData() == false || PostHelper::checkForRequirements(['action', 'computerid']) == false)
            {

                $this->page();
            }
            else
            {

                $action = PostHelper::getPostData('action');

                $computerid = PostHelper::getPostData('computerid');

                if ($this->computer->computerExists($computerid) == false)
                {

                    $this->page();
                }
                else
                {

                    if ($action == "switch")
                    {

                        if ($this->computer->getComputer($computerid)->userid != Container::getObject('session')->getSessionUser())
                        {

                            $this->page();
                        }
                        else
                        {

                            $this->computer->setCurrentUserComputer($computerid);

                            $this->internet->setCurrentConnectedAddress( null );

                            Flight::redirect('/game/');
                        }
                    }
                }
            }
        }

        /**
         * Game Index
         */

        public function page()
        {

            $this->getRender('page.game');
        }

        /**
         * The address book
         */

        public function addressBook()
        {

            $this->getRender('page.game.addressbook');
        }

        /**
         * Default page
         */

        public function internetBrowser()
        {

            if (PostHelper::hasPostData())
            {

                if ($this->validAddress() == false)
                {

                    $this->redirectError('404 Not Found', $this->getRedirect() . '/internet' );
                }

                $this->redirect( $this->getRedirect( PostHelper::getPostData('ipaddress') ) );
            }
            else
            {

                $this->getRender('page.game.internet', array('ipaddress' => $this->internet->getComputerAddress( Settings::getSetting('syscrack_whois_computer') ) ) );
            }
        }

        /**
         * Views a specific address
         *
         * @param $ipaddress
         */

        public function viewAddress($ipaddress)
        {

            if ($this->validAddress($ipaddress) == false)
            {

                $this->redirectError('404 Not Found', $this->getRedirect() . '/internet' );
            }

            $this->getRender('page.game.internet', array('ipaddress' => $ipaddress));
        }

        /**
         * returns true if the IP address is valid
         *
         * @param null $ipaddress
         *
         * @return bool
         */

        private function validAddress($ipaddress = null)
        {

            if ($ipaddress == null)
            {

                if (PostHelper::checkForRequirements(['ipaddress']) == false)
                {

                    return false;
                }

                $ipaddress = PostHelper::getPostData('ipaddress');
            }

            if (filter_var($ipaddress, FILTER_VALIDATE_IP) == false)
            {

                return false;
            }

            if ($this->internet->ipExists($ipaddress) == false)
            {

                return false;
            }

            return true;
        }

        /**
         * Processes an action
         *
         * @param $ipaddress
         *
         * @param $process
         */

        public function process( $ipaddress, $process )
        {

            if( $this->validAddress( $ipaddress ) == false )
            {

                if( $this->internet->hasCurrentConnection() )
                {

                    if( $this->internet->getCurrentConnectedAddress() == $ipaddress )
                    {

                        $this->internet->setCurrentConnectedAddress( null );
                    }
                }

                $this->redirectError('404 Not Found', $this->getRedirect() . '/internet' );
            }

            if( $this->operations->hasProcessClass( $process ) == false )
            {

                $this->redirectError('Invalid action', $this->getRedirect( $ipaddress ) );
            }

            $computerid = $this->computer->getCurrentUserComputer();

            if( $this->operations->hasProcess( $computerid, $process, $ipaddress ) == true )
            {

                $this->redirectError('You already have an action of this nature processing', 'computer/processes');
            }

            if( $this->operations->allowLocal( $process ) == false )
            {

                if( $ipaddress == $this->computer->getComputer( $computerid )->ipaddress )
                {

                    $this->redirectError('This action must be ran on a remote computer');
                }
            }

            if( $this->operations->requireSoftwares( $process ) )
            {

                $this->redirectError('A software is required to preform this action', $this->getRedirect( $ipaddress ) );
            }

            $class = $this->operations->findProcessClass($process);

            if( $this->operations->allowPost( $process ) == true )
            {

                if( PostHelper::hasPostData() == true )
                {

                    if( $this->operations->hasPostRequirements( $process ) == true )
                    {

                        $requirements = $this->operations->getPostRequirements( $process );

                        if( PostHelper::checkForRequirements( $requirements ) == false )
                        {

                            $this->redirectError('Missing information', $this->getRedirect( $ipaddress ) );
                        }

                        $result = $class->onPost( PostHelper::returnRequirements( $requirements ), $ipaddress, $this->session->getSessionUser() );
                    }
                    else
                    {

                        $result = $class->onPost( PostHelper::getPost(), $ipaddress, $this->session->getSessionUser() );
                    }

                    if( $result == false )
                    {

                        $this->redirectError('Unable to complete process', $this->getRedirect( $ipaddress ) );
                    }
                }
            }

            if( $this->operations->allowCustomData( $process ) == true )
            {

                $data = $this->getCustomData( $process, $ipaddress, $this->session->getSessionUser() );
            }
            else
            {

                $data = [];
            }

            $result = $class->onCreation(time(), $this->computer->getCurrentUserComputer(), $this->session->getSessionUser(), $process, array(
                'ipaddress'     => $ipaddress,
                'custom'        => $data
            ));

            if( $result == false )
            {

                $this->redirectError('Unable to complete process', $this->getRedirect( $ipaddress ) );
            }

            $completiontime = $class->getCompletionSpeed($this->computer->getCurrentUserComputer(), $process );

            if( $completiontime !== null )
            {

                $processid = $this->operations->createProcess($completiontime, $this->computer->getCurrentUserComputer(), $this->session->getSessionUser(), $process, array(
                    'ipaddress'     => $ipaddress,
                    'custom'        => $data
                ));

                $this->redirect('processes/' . $processid );
            }

            $class->onCompletion(time(), time(), $this->computer->getCurrentUserComputer(), $this->session->getSessionUser(), $process, array(
                'ipaddress'     => $ipaddress,
                'custom'        =>  $data
            ));
        }

        /**
         * Processes a software action
         *
         * @param $ipaddress
         *
         * @param $process
         *
         * @param $softwareid
         */

        public function processSoftware( $ipaddress, $process, $softwareid )
        {

            if( $this->validAddress( $ipaddress ) == false )
            {

                if( $this->internet->hasCurrentConnection() )
                {

                    if( $this->internet->getCurrentConnectedAddress() == $ipaddress )
                    {

                        $this->internet->setCurrentConnectedAddress( null );
                    }
                }

                $this->redirectError('404 Not Found', $this->getRedirect() . '/internet' );
            }

            if( $this->operations->hasProcessClass( $process ) == false )
            {

                $this->redirectError('Invalid action', $this->getRedirect( $ipaddress ) );
            }

            $computerid = $this->computer->getCurrentUserComputer();

            if( $this->operations->hasProcess( $computerid, $process, $ipaddress, $softwareid ) == true )
            {

                $this->redirectError('You already have an action of this nature processing', 'computer/processes');
            }

            if( $this->operations->allowLocal( $process ) == false )
            {

                if( $ipaddress == $this->computer->getComputer( $computerid )->ipaddress )
                {

                    $this->redirectError('This action must be ran on a remote computer');
                }
            }

            if( $this->operations->allowSoftwares( $process ) == false )
            {

                $this->redirect( $this->getRedirect( $ipaddress ) . $process );
            }

            if( $this->operations->requireLoggedIn( $process ) )
            {

                if ($this->internet->hasCurrentConnection() == false || $this->internet->getCurrentConnectedAddress() != $ipaddress )
                {

                    $this->redirectError('You must be logged into this computer', $this->getRedirect( $ipaddress ) );
                }
            }

            if( $this->operations->allowAnonymous( $process ) == true )
            {

                //Hides the software exists error

                if ($this->softwares->softwareExists($softwareid) == false)
                {

                    $this->redirectError('Unable to preform action', $this->getRedirect( $ipaddress ) );
                }
            }
            else
            {

                if ($this->softwares->softwareExists($softwareid) == false)
                {

                    $this->redirectError('Software does not exist', $this->getRedirect( $ipaddress ) );
                }
            }

            $target = $this->internet->getComputer( $ipaddress );

            $software = $this->softwares->getSoftware( $softwareid );

            if( $target->computerid !== $software->computerid )
            {

                $this->redirectError('Software does not exist', $this->getRedirect( $ipaddress ) );
            }

            if( $this->softwares->isEditable( $software->softwareid ) == false )
            {

                if( $process == Settings::getSetting('syscrack_operations_view_process') || $process == Settings::getSetting('syscrack_operations_download_process') )
                {

                    if( $this->softwares->canView( $software->softwareid ) == false )
                    {

                        if( $process != Settings::getSetting('syscrack_operations_download_process') )
                        {

                            $this->redirectError('This software cannot be modified or edited', $this->getRedirect( $ipaddress ) );
                        }
                    }
                }
                else
                {

                    if( $this->operations->allowAnonymous( $process ) == false )
                    {

                        $this->redirectError('This software cannot be modified or edited', $this->getRedirect( $ipaddress ));
                    }
                }
            }

            $class = $this->operations->findProcessClass($process);

            if( $this->operations->allowPost( $process ) == true )
            {

                if( PostHelper::hasPostData() == true )
                {

                    if( $this->operations->hasPostRequirements( $process ) == true )
                    {

                        $requirements = $this->operations->getPostRequirements( $process );

                        if( PostHelper::checkForRequirements( $requirements ) == false )
                        {

                            $this->redirectError('Missing information', $this->getRedirect( $ipaddress ) );
                        }

                        $result = $class->onPost( PostHelper::returnRequirements( $requirements ), $ipaddress, $this->session->getSessionUser() );
                    }
                    else
                    {

                        $result = $class->onPost( PostHelper::getPost(), $ipaddress, $this->session->getSessionUser() );
                    }

                    if( $result == false )
                    {

                        $this->redirectError('Unable to complete process', $this->getRedirect( $ipaddress ) );
                    }
                }
            }

            if( $this->operations->allowCustomData( $process ) == true )
            {

                $data = $this->getCustomData( $process, $ipaddress, $this->session->getSessionUser() );
            }
            else
            {

                $data = [];
            }

            $result = $class->onCreation(time(), $this->computer->getCurrentUserComputer(), $this->session->getSessionUser(), $process, array(
                'ipaddress'     => $ipaddress,
                'softwareid'    => $softwareid,
                'custom'        => $data
            ));

            if( $result == false )
            {

                $this->redirectError('Unable to complete process', $this->getRedirect( $ipaddress ) );
            }

            $completiontime = $class->getCompletionSpeed($this->computer->getCurrentUserComputer(), $process,  $softwareid );

            if( $completiontime !== null )
            {

                $processid = $this->operations->createProcess($completiontime, $this->computer->getCurrentUserComputer(), $this->session->getSessionUser(), $process, array(
                    'ipaddress'     => $ipaddress,
                    'softwareid'    => $softwareid,
                    'custom'        => $data
                ));

                $this->redirect('processes/' . $processid );
            }

            $class->onCompletion(time(), time(), $this->computer->getCurrentUserComputer(), $this->session->getSessionUser(), $process, array(
                'ipaddress'     => $ipaddress,
                'softwareid'    => $softwareid,
                'custom'        => $data
            ));
        }

        /**
         * Gets the custom data for this operation
         *
         * @param $process
         *
         * @param $ipaddress
         *
         * @param $userid
         *
         * @return array|null
         */

        private function getCustomData( $process, $ipaddress, $userid )
        {

            if( $this->operations->allowCustomData( $process ) == false )
            {

                return null;
            }

            $data = $this->operations->getCustomData( $process, $ipaddress, $userid );

            if( empty( $data ) || $data == null )
            {

                return null;
            }

            if( is_array( $data ) == false )
            {

                throw new SyscrackException();
            }

            return $data;
        }

        /**
         * Gets the game page to redirect too
         *
         * @param null $ipaddress
         *
         * @return string
         */

        private function getRedirect($ipaddress=null )
        {

            if( $ipaddress )
            {

                return Settings::getSetting('syscrack_game_page') . '/' . Settings::getSetting('syscrack_internet_page') . '/' . $ipaddress;
            }

            return Settings::getSetting('syscrack_game_page');
        }
    }
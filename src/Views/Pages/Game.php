<?php
    namespace Framework\Views\Pages;

    /**
     * Lewis Lancaster 2017
     *
     * Class Game
     *
     * @package Framework\Views\Pages
     */

    use Framework\Application\Render;
    use Framework\Application\Container;
    use Framework\Syscrack\Game\Metadata;
    use Framework\Application\Settings;
    use Framework\Application\Utilities\PostHelper;
    use Framework\Exceptions\SyscrackException;
    use Framework\Syscrack\Game\Finance;
    use Framework\Syscrack\Game\Operations;
    use Framework\Views\BaseClasses\Page as BaseClass;
    use Framework\Views\Structures\Page as Structure;

    class Game extends BaseClass implements Structure
    {


        /**
         * @var Operations
         */

        protected static $operations;

        /**
         * @var Metadata
         */

        protected static $metadata;


        /**
         * Game constructor.
         */

        public function __construct()
        {

            if( isset( self::$operations ) == false )
                self::$operations = new Operations();
            
            if( isset( self::$metadata ) == false )
                self::$metadata = new Metadata();

            parent::__construct( true, true, true, true );
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
                    'GET /game/computer/', 'computer'
                ],
                [
                    'POST /game/computer/switch/@computerid', 'switchProcess'
                ],
                [
                    'POST /game/computer/', 'computerPurchase'
                ],
                [
                    '/game/internet/', 'internetBrowser'
                ],
                [
                    '/game/addressbook/', 'addressBook'
                ],
                [
                    '/game/accountbook/', 'accountBook'
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
                $this->page();
            else
            {

                $action = PostHelper::getPostData('action');
                $computerid = PostHelper::getPostData('computerid');

                if (self::$computer->computerExists($computerid) == false)
                    $this->page();
                else
                {

                    if ($action == "switch")
                    {

                        if (self::$computer->getComputer($computerid)->userid != Container::getObject('session')->getSessionUser())
                            $this->page();
                        else
                        {

                            self::$computer->setCurrentUserComputer($computerid);
                            self::$internet->setCurrentConnectedAddress( null );
                            Render::redirect('/game/' );
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

            $this->getRender('syscrack/page.game');
        }

        /**
         * Computer
         */

        public function computer()
        {

            $this->getRender('syscrack/page.game.computer');
        }

        /**
         *
         */

        public function computerPurchase()
        {

            if ( PostHelper::hasPostData() == false )
                $this->redirectError('Missing Information', 'game/computer/');
            else
            {

                if ( PostHelper::checkForRequirements(['accountnumber']) == false )
                    $this->redirectError('Account number is invalid', 'game/computer/');
                else
                {

                    $finance = new Finance();

                    if ( $finance->accountNumberExists(  PostHelper::getPostData('accountnumber', true ) ) == false )
                        $this->redirectError('Account number is invalid', 'game/computer/');
                    else
                    {

                        $account = $finance->getByAccountNumber( PostHelper::getPostData('accountnumber', true ) );

                        if ( $account->userid !== self::$session->getSessionUser() )
                            $this->redirectError('Invalid', 'game/computer/');
                        else if ( $finance->canAfford( $account->computerid, $account->userid, $this->getVPCPrice( self::$session->getSessionUser() ) ) == false )
                            $this->redirectError('Sorry, you cannot afford this transaction!', 'game/computer/');
                        else
                        {

                            $computerid = self::$computer->createComputer( self::$session->getSessionUser(), Settings::getSetting('syscrack_startup_default_computer'), self::$internet->getIP() );

                            if( empty( $computerid ) )
                                throw new SyscrackException();

                            /**
                             * @var $class \Framework\Syscrack\Game\Structures\Computer
                             */

                            $class = self::$computer->getComputerClass( Settings::getSetting('syscrack_startup_default_computer') );
                            $class->onStartup( $computerid, self::$session->getSessionUser(), [], Settings::getSetting('syscrack_default_hardware') );
                            $finance->withdraw( $account->computerid, $account->userid, $this->getVPCPrice( self::$session->getSessionUser() ) );
                            $this->redirectSuccess('game/computer/');
                        }
                    }
                }
            }
        }

        /**
         * @param $userid
         * @return float|int
         */

        private function getVPCPrice( $userid )
        {

            $computer = self::$computer->getUserComputers( $userid );

            if ( empty( $computer ) )
                return 0;

            return( count( $computer ) * ( Settings::getSetting('syscrack_vpc_purchase_price') * Settings::getSetting('syscrack_vpc_purchase_increase' ) ));
        }

        /**
         * Switches a users computer
         *
         * @param $computerid
         */

        public function switchProcess( $computerid )
        {

            if ( self::$computer->computerExists( $computerid ) == false )
                $this->redirectError('Invalid computer', 'game/computer/');
            else
            {

                if (self::$computer->getComputer($computerid)->userid != Container::getObject('session')->getSessionUser())
                    $this->page();
                else
                {

                    self::$computer->setCurrentUserComputer($computerid);
                    self::$internet->setCurrentConnectedAddress( null );
                    $this->redirectSuccess('game/computer/');
                }
            }
        }

        /**
         * The address book
         */

        public function addressBook()
        {

            $this->getRender('syscrack/page.game.addressbook');
        }

        /**
         * The address book
         */

        public function accountBook()
        {

            $this->getRender('syscrack/page.game.accountbook');
        }

        /**
         * Default page
         */

        public function internetBrowser()
        {

            if (PostHelper::hasPostData())
            {

                if ($this->validAddress() == false)
                    $this->redirectError('404 Not Found', $this->getRedirect() . '/internet' );
                else
                    $this->redirect( $this->getRedirect( PostHelper::getPostData('ipaddress') ) );
            }
            else
                $this->getRender('syscrack/page.game.internet', array('ipaddress' => self::$internet->getComputerAddress( Settings::getSetting('syscrack_whois_computer') ) ) );
        }

        /**
         * Views a specific address
         *
         * @param $ipaddress
         */

        public function viewAddress($ipaddress)
        {

            if ($this->validAddress($ipaddress) == false)
                $this->redirectError('404 Not Found', $this->getRedirect() . '/internet' );
            else
            {

                $computer = $this->getComputerByAddress( $ipaddress );

                if( self::$metadata->exists( $computer->computerid ) )
                    $this->getRender('syscrack/page.game.internet', array('ipaddress' => $ipaddress, 'computer' => $computer, 'metadata' => self::$metadata->get( $computer->computerid )));
                else
                    $this->getRender('syscrack/page.game.internet', array('ipaddress' => $ipaddress, 'computer' => $computer ) );
            }

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
                    return false;

                $ipaddress = PostHelper::getPostData('ipaddress');
            }

            if (filter_var($ipaddress, FILTER_VALIDATE_IP) == false)
                return false;

            if (self::$internet->ipExists($ipaddress) == false)
                return false;

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
                $this->redirectError('404 Not Found', $this->getRedirect() . '/internet' );

            if( self::$operations->hasProcessClass( $process ) == false )
                $this->redirectError('Invalid action', $this->getRedirect( $ipaddress ) );
            else
            {

                $computerid = self::$computer->getCurrentUserComputer();

                if( self::$operations->hasProcess( $computerid, $process, $ipaddress ) == true )
                    $this->redirectError('You already have an action of this nature processing', $this->getRedirect( $ipaddress ) );
                elseif( self::$operations->allowLocal( $process ) == false && $ipaddress == self::$computer->getComputer( $computerid )->ipaddress )
                    $this->redirectError('This action must be ran on a remote computer', $this->getRedirect( $ipaddress ) );
                elseif( self::$operations->localOnly( $process ) && $ipaddress !== self::$computer->getComputer( $computerid )->ipaddress )
                    $this->redirectError('This action can only be ran locally', $this->getRedirect( $ipaddress ) );
                elseif( self::$operations->requireSoftware( $process ) )
                    $this->redirectError('A software is required to preform this action', $this->getRedirect( $ipaddress ) );
                else
                {

                    $class = self::$operations->findProcessClass($process);

                    if( self::$operations->allowPost( $process ) == true && PostHelper::hasPostData() == true )
                    {

                        if( self::$operations->hasPostRequirements( $process ) == true )
                            if( PostHelper::checkForRequirements( self::$operations->getPostRequirements( $process ) ) == false )
                                $this->redirectError('Missing information', $this->getRedirect( $ipaddress ) );
                            else
                                $result = $class->onPost( PostHelper::getPost(), $ipaddress, self::$session->getSessionUser() );
                        else
                            $result = $class->onPost( PostHelper::getPost(), $ipaddress, self::$session->getSessionUser() );
                    }
                    else
                        $result = true;

                    if( $result === false )
                        $this->redirectError('Unable to complete process', $this->getRedirect( $ipaddress ) );
                    else
                    {

                        $class = self::$operations->findProcessClass($process);

                        if( self::$operations->allowCustomData( $process ) == true )
                            $data = $this->getCustomData( $process, $ipaddress, self::$session->getSessionUser() );
                        else
                            $data = [];

                        if( $class->onCreation(time(), self::$computer->getCurrentUserComputer(), self::$session->getSessionUser(), $process, array(
                            'ipaddress'     => $ipaddress,
                            'custom'        => $data
                        )) == false )
                            $this->redirectError('Unable to complete process', $this->getRedirect( $ipaddress ) );
                        else
                        {

                            $time = $class->getCompletionSpeed( self::$computer->getCurrentUserComputer(), $process );

                            if( $time === null )
                                $class->onCompletion(time(), time(), self::$computer->getCurrentUserComputer(), self::$session->getSessionUser(), $process, array(
                                    'ipaddress'     => $ipaddress,
                                    'custom'        =>  $data
                                ));
                            else
                                $this->redirect('processes/' . self::$operations->createProcess($time, self::$computer->getCurrentUserComputer(), self::$session->getSessionUser(), $process, array(
                                        'ipaddress'     => $ipaddress,
                                        'custom'        => $data
                                    )) );
                        }
                    }
                }
            }
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

                if( self::$internet->hasCurrentConnection() )
                {

                    if( self::$internet->getCurrentConnectedAddress() == $ipaddress )
                    {

                        self::$internet->setCurrentConnectedAddress( null );
                    }
                }

                $this->redirectError('404 Not Found', $this->getRedirect() . '/internet' );
            }

            if( self::$operations->hasProcessClass( $process ) == false )
            {

                $this->redirectError('Invalid action', $this->getRedirect( $ipaddress ) );
            }

            $computerid = self::$computer->getCurrentUserComputer();

            if( self::$operations->hasProcess( $computerid, $process, $ipaddress, $softwareid ) == true )
            {

                $this->redirectError('You already have an action of this nature processing', 'computer/processes');
            }

            if( self::$operations->allowLocal( $process ) == false )
            {

                if( $ipaddress == self::$computer->getComputer( $computerid )->ipaddress )
                {

                    $this->redirectError('This action must be ran on a remote computer');
                }
            }

            if( self::$operations->localOnly( $process ) )
            {
                if( $ipaddress !== self::$computer->getComputer( $computerid )->ipaddress )
                {

                    $this->redirectError('This action can only be ran locally');
                }
            }

            if( self::$operations->allowSoftware( $process ) == false )
            {

                $this->redirect( $this->getRedirect( $ipaddress ) . $process );
            }

            if( self::$operations->requireLoggedIn( $process ) )
            {

                if (self::$internet->hasCurrentConnection() == false || self::$internet->getCurrentConnectedAddress() != $ipaddress )
                {

                    $this->redirectError('You must be logged into this computer', $this->getRedirect( $ipaddress ) );
                }
            }

            if( self::$operations->allowAnonymous( $process ) == true )
            {

                //Hides the software exists error

                if (self::$software->softwareExists($softwareid) == false)
                {

                    $this->redirectError('Unable to preform action', $this->getRedirect( $ipaddress ) );
                }
            }
            else
            {

                if (self::$software->softwareExists($softwareid) == false)
                {

                    $this->redirectError('Software does not exist', $this->getRedirect( $ipaddress ) );
                }
            }

            $target = self::$internet->getComputer( $ipaddress );

            $software = self::$software->getSoftware( $softwareid );

            if( $target->computerid !== $software->computerid )
            {

                $this->redirectError('Software does not exist', $this->getRedirect( $ipaddress ) );
            }

            if( self::$software->isEditable( $software->softwareid ) == false )
            {

                if( $process == Settings::getSetting('syscrack_operations_view_process') || $process == Settings::getSetting('syscrack_operations_download_process') )
                {

                    if( self::$software->canView( $software->softwareid ) == false )
                    {

                        if( $process != Settings::getSetting('syscrack_operations_download_process') )
                        {

                            $this->redirectError('This software cannot be modified or edited', $this->getRedirect( $ipaddress ) );
                        }
                    }
                }
                else
                {

                    if( self::$operations->allowAnonymous( $process ) == false )
                    {

                        $this->redirectError('This software cannot be modified or edited', $this->getRedirect( $ipaddress ));
                    }
                }
            }

            $class = self::$operations->findProcessClass($process);

            if( self::$operations->allowPost( $process ) == true )
            {

                if( PostHelper::hasPostData() == true )
                {

                    if( self::$operations->hasPostRequirements( $process ) == true )
                    {

                        $requirements = self::$operations->getPostRequirements( $process );

                        if( PostHelper::checkForRequirements( $requirements ) == false )
                        {

                            $this->redirectError('Missing information', $this->getRedirect( $ipaddress ) );
                        }

                        $result = $class->onPost( PostHelper::returnRequirements( $requirements ), $ipaddress, self::$session->getSessionUser() );
                    }
                    else
                    {

                        $result = $class->onPost( PostHelper::getPost(), $ipaddress, self::$session->getSessionUser() );
                    }

                    if( $result == false )
                    {

                        $this->redirectError('Unable to complete process', $this->getRedirect( $ipaddress ) );
                    }
                }
            }

            if( self::$operations->allowCustomData( $process ) == true )
            {

                $data = $this->getCustomData( $process, $ipaddress, self::$session->getSessionUser() );
            }
            else
            {

                $data = [];
            }

            $result = $class->onCreation(time(), self::$computer->getCurrentUserComputer(), self::$session->getSessionUser(), $process, array(
                'ipaddress'     => $ipaddress,
                'softwareid'    => $softwareid,
                'custom'        => $data
            ));

            if( $result == false )
            {

                $this->redirectError('Unable to complete process', $this->getRedirect( $ipaddress ) );
            }

            $completiontime = $class->getCompletionSpeed(self::$computer->getCurrentUserComputer(), $process,  $softwareid );

            if( $completiontime !== null )
            {

                $processid = self::$operations->createProcess($completiontime, self::$computer->getCurrentUserComputer(), self::$session->getSessionUser(), $process, array(
                    'ipaddress'     => $ipaddress,
                    'softwareid'    => $softwareid,
                    'custom'        => $data
                ));

                $this->redirect('processes/' . $processid );
            }

            $class->onCompletion(time(), time(), self::$computer->getCurrentUserComputer(), self::$session->getSessionUser(), $process, array(
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

            if( self::$operations->allowCustomData( $process ) == false )
            {

                return null;
            }

            $data = self::$operations->getCustomData( $process, $ipaddress, $userid );

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
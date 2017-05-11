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
    use Framework\Application\Settings;
    use Framework\Application\Utilities\PostHelper;
    use Framework\Exceptions\ViewException;
    use Framework\Syscrack\Game\Operations;
    use Framework\Syscrack\Game\Structures\Operation;
    use Framework\Views\BaseClasses\Page as BaseClass;
    use Framework\Views\Structures\Page as Structure;

    class Game extends BaseClass implements Structure
    {


        /**
         * @var Operations
         */

        protected $operations;

        /**
         * Game constructor.
         */

        public function __construct()
        {

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
                    '/game/internet/', 'internet'
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

        public function internet()
        {

            if (PostHelper::hasPostData())
            {

                if ($this->validAddress() == false)
                {

                    $this->redirectError('404 Not Found', $this->getRedirect() . 'internet' );
                }

                Flight::redirect( Settings::getSetting('controller_index_root') . $this->getRedirect( PostHelper::getPostData('ipaddress') ) );
            }
            else
            {

                $this->getRender('page.game.internet', array('ipaddress' => $this->internet->getComputerAddress(Settings::getSetting('syscrack_whois_computer'))));
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
         * Processes a game action
         *
         * @param $ipaddress
         *
         * @param $process
         */

        public function process($ipaddress, $process)
        {

            if ($this->validAddress($ipaddress) == false)
            {

                $this->redirectError('404 Not Found', $this->getRedirect() . 'internet' );
            }
            else
            {

                $this->operations = new Operations();

                if ($this->operations->hasProcessClass($process) == false)
                {

                    $this->redirectError('Action not found', $this->getRedirect( $ipaddress ) );
                }

                if( $this->operations->hasProcess( $this->computer->getCurrentUserComputer(), $process, $ipaddress ) == true )
                {

                    $this->redirectError('You already have a process of this nature processing, complete that one first', $this->getRedirect( $ipaddress ));
                }

                if( $this->operations->requireSoftwares( $process ) )
                {

                    $this->redirectError('A software is required to preform this action', $this->getRedirect( $ipaddress ) );
                }

                $class = $this->operations->findProcessClass($process);

                if ($class instanceof Operation == false)
                {

                    throw new ViewException();
                }

                $completiontime = $class->getCompletionSpeed($this->computer->getCurrentUserComputer(), $process, null);

                if ($completiontime == null)
                {

                    $result = $class->onCreation(time(), $this->computer->getCurrentUserComputer(), Container::getObject('session')->getSessionUser(), $process, array(
                        'ipaddress' => $ipaddress
                    ));

                    if ($result == false)
                    {

                        $this->redirectError('Unable to preform action', $this->getRedirect( $ipaddress ) );
                    }
                    else
                    {

                        $class->onCompletion(time(), time(), $this->computer->getCurrentUserComputer(), Container::getObject('session')->getSessionUser(), $process, array(
                            'ipaddress' => $ipaddress
                        ));
                    }
                }
                else
                {

                    $processid = $this->operations->createProcess($completiontime, $this->computer->getCurrentUserComputer(), Container::getObject('session')->getSessionUser(), $process, array(
                        'ipaddress' => $ipaddress
                    ));

                    if ($processid == false)
                    {

                        $this->redirectError('Failed to create process', $this->getRedirect( $ipaddress ) );
                    }

                    Flight::redirect('/processes/' . $processid);
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

        public function processSoftware($ipaddress, $process, $softwareid)
        {

            if ($this->validAddress($ipaddress) == false)
            {

                $this->redirectError('404 Not Found', $this->getRedirect() . 'internet' );
            }
            else
            {

                $this->operations = new Operations();

                if ($this->operations->hasProcessClass($process) == false)
                {

                    $this->redirectError('Action not found', $this->getRedirect( $ipaddress ) );
                }

                if( $this->operations->hasProcess( $this->computer->getCurrentUserComputer(), $process, $ipaddress, $softwareid ) == true )
                {

                    $this->redirectError('You already have a process of this nature processing, complete that one first', $this->getRedirect( $ipaddress ) );
                }

                if( $this->operations->allowSoftwares( $process ) == false )
                {

                    Flight::redirect( Settings::getSetting('controller_index_root') . $this->getRedirect( $ipaddress ) . $process );
                }
                else
                {

                    $class = $this->operations->findProcessClass($process);

                    if ($class instanceof Operation == false)
                    {

                        throw new ViewException();
                    }

                    if( $this->operations->requireLoggedIn( $process ) == false )
                    {

                        if( $this->operations->allowAnonymous( $process ) == true )
                        {

                            if ($this->softwares->softwareExists($softwareid) == false)
                            {

                                $this->redirectError('Unable to preform action', $this->getRedirect( $ipaddress ) );
                            }
                        }
                    }
                    else
                    {

                        if ($this->internet->hasCurrentConnection() == false || $this->internet->getCurrentConnectedAddress() != $ipaddress)
                        {

                            $this->redirectError('You must be connected to this computer to preform this action on its software', $this->getRedirect( $ipaddress ) );
                        }
                    }

                    if ($this->softwares->softwareExists($softwareid) == false)
                    {

                        $this->redirectError('Software does not exist', $this->getRedirect( $ipaddress ) );
                    }

                    if( $this->softwares->getSoftware( $softwareid )->computerid !== $this->internet->getComputer( $ipaddress )->computerid )
                    {

                        $this->redirectError('The you are trying to access does not own this software', $this->getRedirect( $ipaddress ) );
                    }

                    if( $this->softwares->isEditable( $softwareid ) == false )
                    {

                        if( $this->operations->allowAnonymous( $process ) == false )
                        {

                            $this->redirectError('This software cannot be modified or edited', $this->getRedirect( $ipaddress ) );
                        }
                    }

                    if( $this->operations->useLocalSoftware( $process ) )
                    {

                        if( $this->computer->hasSoftware( $this->computer->getCurrentUserComputer(), $softwareid ) == false )
                        {

                            $this->redirectError('This software does not exist on your computer', $this->getRedirect( $ipaddress ) );
                        }
                    }
                    else
                    {

                        if ($this->computer->hasSoftware($this->internet->getComputer($ipaddress)->computerid, $softwareid) == false)
                        {

                            $this->redirectError('Software does not exist', $this->getRedirect( $ipaddress ) );
                        }
                    }

                    $completiontime = $class->getCompletionSpeed($this->computer->getCurrentUserComputer(), $process,  $softwareid );

                    if ($completiontime == null)
                    {

                        $result = $class->onCreation(time(), $this->computer->getCurrentUserComputer(), Container::getObject('session')->getSessionUser(), $process, array(
                            'ipaddress' => $ipaddress,
                            'softwareid' => $softwareid
                        ));

                        if ($result == false)
                        {

                            $this->redirectError('Process cannot be completed', $this->getRedirect( $ipaddress ) );
                        }
                        else
                        {

                            $class->onCompletion(time(), time(), $this->computer->getCurrentUserComputer(), Container::getObject('session')->getSessionUser(), $process, array(
                                'ipaddress' => $ipaddress,
                                'softwareid' => $softwareid
                            ));
                        }
                    }
                    else
                    {

                        $processid = $this->operations->createProcess($completiontime, $this->computer->getCurrentUserComputer(), Container::getObject('session')->getSessionUser(), $process, array(
                            'ipaddress' => $ipaddress,
                            'softwareid' => $softwareid
                        ));

                        if ($processid == false)
                        {

                            $this->redirectError('Process failed to be created', $this->getRedirect( $ipaddress ) );
                        }

                        Flight::redirect('/processes/' . $processid);
                    }
                }
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

                $this->redirectError('404 Not Found', $this->getRedirect() . 'internet' );
            }

            $this->getRender('page.game.internet', array('ipaddress' => $ipaddress));
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

                return Settings::getSetting('syscrack_game_page') . '/' . Settings::getSetting('syscrack_internet_page') . '/' . $ipaddress . '/';
            }

            return Settings::getSetting('syscrack_game_page') . '/';
        }

        /**
         * Renders a page
         *
         * @param $file
         *
         * @param array|null $array
         */

        private function getRender($file, array $array = null)
        {

            Flight::render(Settings::getSetting('syscrack_view_location') . $file, $array);
        }
    }
<?php
    namespace Framework\Views\Pages;

    /**
     * Lewis Lancaster 2016
     *
     * Class Error
     *
     * @package Framework\Views\Pages
     */

    use Flight;
    use Framework\Application\Container;
    use Framework\Application\Session;
    use Framework\Application\Settings;
    use Framework\Application\Utilities\Log;
    use Framework\Application\Utilities\PostHelper;
    use Framework\Exceptions\SyscrackException;
    use Framework\Syscrack\Game\Operations;
    use Framework\Syscrack\Game\Structures\Operation;
    use Framework\Views\BaseClasses\Page as BaseClass;
    use Framework\Views\Structures\Page;

    class Game extends BaseClass implements Page
    {


        protected $operations;

        /**
         * Game constructor.
         */

        public function __construct()
        {

            parent::__construct();

            if (session_status() !== PHP_SESSION_ACTIVE)
            {

                session_start();
            }

            if (Container::hasObject('session') == false)
            {

                Container::setObject('session', new Session());
            }

            if (Container::getObject('session')->isLoggedIn() == false)
            {

                Flight::redirect('/' . Settings::getSetting('controller_index_root'));

                exit;
            }
        }

        /**
         * The index page has a special algorithm which allows it to access the root. Only the index can do this.
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
                    '/game/internet/@ipaddress', 'viewAddress'
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

                    $this->redirectError('404 Not Found');
                }

                $this->getRender('page.game.internet', array('ipaddress' => PostHelper::getPostData('ipaddress')));
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

                $this->redirectError('404 Not Found');
            }
            else
            {

                $this->operations = new Operations();

                if ($this->operations->hasProcessClass($process) == false)
                {

                    $this->redirectError('Action not found', $ipaddress);
                }

                $class = $this->operations->findProcessClass($process);

                if ($class instanceof Operation == false)
                {

                    throw new SyscrackException();
                }

                $completiontime = $class->getCompletionSpeed($this->computer->getCurrentUserComputer(), $process, null);

                if ($completiontime == null)
                {

                    $result = $class->onCreation(time(), $this->computer->getCurrentUserComputer(), Container::getObject('session')->getSessionUser(), $process, array(
                        'ipaddress' => $ipaddress
                    ));

                    if ($result == false)
                    {

                        $this->redirectError('Unable to create process', $ipaddress);
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

                        $this->redirectError('Unable to create process', $ipaddress);
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

                $this->redirectError('404 Not Found');
            }
            else
            {

                $this->operations = new Operations();

                if ($this->internet->hasCurrentConnection() == false || $this->internet->getCurrentConnectedAddress() != $ipaddress)
                {

                    $this->redirectError('You must be connected to this computer to preform actions on its software');
                }

                if ($this->operations->hasProcessClass($process) == false)
                {

                    $this->redirectError('Action not found', $ipaddress);
                }

                if ($this->softwares->softwareExists($softwareid) == false)
                {

                    $this->redirectError('Software does not exist', $ipaddress);
                }

                if ($this->computer->hasSoftware($this->internet->getComputer($ipaddress)->computerid, $softwareid) == false)
                {

                    $this->redirectError('Software does not exist', $ipaddress);
                }

                $class = $this->operations->findProcessClass($process);

                if ($class instanceof Operation == false)
                {

                    throw new SyscrackException();
                }

                $completiontime = $class->getCompletionSpeed($this->computer->getCurrentUserComputer(), $process, null);

                if ($completiontime == null)
                {

                    $result = $class->onCreation(time(), $this->computer->getCurrentUserComputer(), Container::getObject('session')->getSessionUser(), $process, array(
                        'ipaddress' => $ipaddress,
                        'softwareid' => $softwareid
                    ));

                    if ($result == false)
                    {

                        $this->redirectError('Process cannot be completed', $ipaddress);
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

                        $this->redirectError('Process failed to be created', $ipaddress);
                    }

                    Flight::redirect('/processes/' . $processid);
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

                $this->redirectError('404 Not Found');
            }

            $this->getRender('page.game.internet', array('ipaddress' => $ipaddress));
        }

        /**
         * Redirects the user to an error page
         *
         * @param string $message
         *
         * @param string $ipaddress
         */

        public function redirectError( $message='', $ipaddress='' )
        {

            if( $ipaddress !== '' )
            {

                Flight::redirect('/' . Settings::getSetting('syscrack_game_page') . '/' . Settings::getSetting('syscrack_internet_page') . '/' . $ipaddress . '/?error=' . $message);

                exit;
            }

            Flight::redirect( '/' . Settings::getSetting('syscrack_game_page') . '/?error=' . $message);

            exit;
        }

        /**
         * Redirects the user to a success page
         *
         * @param string $ipaddress
         */

        public function redirectSuccess($ipaddress = '' )
        {

            if( $ipaddress !== '' )
            {

                Flight::redirect('/' . Settings::getSetting('syscrack_game_page') . '/' . Settings::getSetting('syscrack_internet_page') . '/' . $ipaddress . '/?success');

                exit;
            }

            Flight::redirect( '/' . Settings::getSetting('syscrack_game_page') . '/?success');

            exit;
        }
    }
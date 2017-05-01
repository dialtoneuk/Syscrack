<?php
    namespace Framework\Views\Pages;

    /**
     * Lewis Lancaster 2016
     *
     * Class Processes
     *
     * @package Framework\Views\Pages
     */

    use Flight;
    use Framework\Application\Container;
    use Framework\Application\Session;
    use Framework\Application\Settings;
    use Framework\Syscrack\Game\Operations;
    use Framework\Views\BaseClasses\Page as BaseClass;
    use Framework\Views\Structures\Page;

    class Processes extends BaseClass implements Page
    {

        /**
         * @var Operations
         */

        protected $operations;

        /**
         * Processes constructor.
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

                Flight::redirect( Settings::getSetting('controller_index_root') . Settings::getSetting('controller_index_page') );

                exit;
            }

            $this->operations = new Operations();
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
                    '/processes/', 'page'
                ],
                [
                    '/processes/@processid', 'viewProcess'
                ],
                [
                    '/processes/@processid/complete', 'completeProcess'
                ]
            );
        }

        /**
         * Default page
         */

        public function page()
        {

            die('page soon');
        }

        /**
         * Views a process
         *
         * @param $processid
         */

        public function viewProcess($processid)
        {

            if ($this->operations->processExists($processid) == false)
            {

                $this->redirectError('This process does not exist');
            }
            else
            {

                $process = $this->operations->getProcess($processid);

                if ($process->userid != Container::getObject('session')->getSessionUser())
                {

                    $this->redirectError('This process isnt yours');
                }
                else
                {

                    if ($process->computerid != $this->computer->getCurrentUserComputer())
                    {

                        $this->redirectError('You are connected as a different computer');
                    }
                    else
                    {

                        $this->getRender('page.process.view', array('processid' => $processid, 'processclass' => $this->operations, 'auto' => true));
                    }
                }
            }
        }

        /**
         * Completes a process
         *
         * @param $processid
         */

        public function completeProcess($processid)
        {

            if ($this->operations->processExists($processid) == false)
            {

                $this->redirectError('This process does not exist');
            }
            else
            {

                $process = $this->operations->getProcess($processid);

                if ($process->userid != Container::getObject('session')->getSessionUser())
                {

                    $this->redirectError('This process isnt yours');
                }
                else
                {

                    if ($process->computerid != $this->computer->getCurrentUserComputer())
                    {

                        $this->redirectError('You are connected as a different computer');
                    }
                    else
                    {

                        if ($this->operations->canComplete($processid) == false)
                        {

                            $this->redirectError('Process has not yet completed');
                        }
                        else
                        {

                            $this->operations->completeProcess($processid);
                        }
                    }
                }
            }
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

            Flight::render( Settings::getSetting('syscrack_view_location') . $file, $array);
        }
    }
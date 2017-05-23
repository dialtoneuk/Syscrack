<?php
    namespace Framework\Views\Pages;

    /**
     * Lewis Lancaster 2017
     *
     * Class Processes
     *
     * @package Framework\Views\Pages
     */

    use Flight;
    use Framework\Application\Container;
    use Framework\Syscrack\Game\Operations;
    use Framework\Views\BaseClasses\Page as BaseClass;
    use Framework\Views\Structures\Page as Structure;

    class Processes extends BaseClass implements Structure
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

            parent::__construct( true, true, true, true );

            if( isset( $this->operations ) == false )
            {

                $this->operations = new Operations();
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
                    '/processes/', 'page'
                ],
                [
                    '/processes/@processid', 'viewProcess'
                ],
                [
                    '/processes/@processid/complete', 'completeProcess'
                ],
                [
                    '/processes/@processid/delete', 'deleteProcess'
                ]
            );
        }

        /**
         * Default page
         */

        public function page()
        {

            Flight::render('syscrack/page.processes');
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

                    if ($process->computerid != $this->computers->getCurrentUserComputer())
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

                    $data = json_decode( $process->data, true );

                    if( isset( $data['ipaddress'] ) == false )
                    {

                        $this->redirectError('Sorry, no ip address for this action was set, try again');
                    }

                    if( $this->internet->ipExists( $data['ipaddress'] ) == false )
                    {

                        $this->redirectError('404 Not found, maybe this IP was changed?');
                    }

                    if( $this->internet->getComputer( $data['ipaddress'] )->computerid != $this->computers->getCurrentUserComputer() )
                    {

                        if( $this->operations->requireLoggedIn( $process->process ) == true )
                        {

                            if( $this->internet->hasCurrentConnection() == false )
                            {

                                $this->redirectError('You must be connected to the computer this process was initiated on');
                            }
                            else
                            {

                                if( $data['ipaddress'] != $this->internet->getCurrentConnectedAddress() )
                                {

                                    $this->redirectError('You must be connected to the computer this process was initiated on');
                                }
                            }
                        }

                        if ($this->operations->canComplete($processid) == false)
                        {

                            $this->redirectError('Process has not yet completed');
                        }
                        else
                        {

                            $this->operations->completeProcess($processid);
                        }
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
         * Deletes a process
         *
         * @param $processid
         */

        public function deleteProcess( $processid )
        {

            if ($this->operations->processExists($processid) == false)
            {

                $this->redirectError('This process does not exist');
            }
            else
            {

                $process = $this->operations->getProcess( $processid );

                if( $process->userid !== Container::getObject('session')->getSessionUser() )
                {

                    $this->redirectError('You do not own this process');
                }

                if( $process->computerid !== $this->computers->getCurrentUserComputer() )
                {

                    $this->redirectError('You need to currently be switched to the computer this process was initiated on');
                }

                $this->operations->deleteProcess( $processid );

                $this->redirectSuccess();
            }
        }
    }
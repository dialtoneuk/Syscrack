<?php
    namespace Framework\Views\Pages;

    /**
     * Lewis Lancaster 2017
     *
     * Class Processes
     *
     * @package Framework\Views\Pages
     */

    use Framework\Application\Container;
    use Framework\Syscrack\Game\Operations;
    use Framework\Views\BaseClasses\Page as BaseClass;
    use Framework\Views\Structures\Page as Structure;

    class Processes extends BaseClass implements Structure
    {

        /**
         * @var Operations
         */

        protected static $operations;

        /**
         * Processes constructor.
         */

        public function __construct()
        {

            if( isset( self::$operations ) == false )
                self::$operations = new Operations();

            parent::__construct( true, true, true, false );
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
                ],
                [
                    '/processes/computer/@computerid/','machineProcess'
                ]
            );
        }

        /**
         * Default page
         */

        public function page()
        {

            $processes = self::$operations->getUserProcesses( Container::getObject('session')->getSessionUser() );

            if ( empty( $processes ) )
            {

                $array = [];
            }
            else
            {

                $array = array();

                foreach( $processes as $key=>$value )
                {

                    $array[ $value->computerid ][] = $value;
                }
            }
            $this->getRender('syscrack/page.process.php', array('processes' => $array, 'operations' => self::$operations, 'computerid' => self::$computer->getCurrentUserComputer() ));
        }

        /**
         * Views a process
         *
         * @param $processid
         */

        public function viewProcess($processid)
        {

            if (self::$operations->processExists($processid) == false)
            {

                $this->redirectError('This process does not exist');
            }
            else
            {

                $process = self::$operations->getProcess($processid);

                if ($process->userid != Container::getObject('session')->getSessionUser())
                {

                    $this->redirectError('This process isnt yours');
                }
                else
                {

                    if ($process->computerid != self::$computer->getCurrentUserComputer())
                    {

                        $this->redirectError('You are connected as a different computer');
                    }
                    else
                    {

                        $this->getRender('syscrack/page.process.view', array('processid' => $processid, 'processclass' => self::$operations, 'auto' => true));
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

            if (self::$operations->processExists($processid) == false)
            {

                $this->redirectError('This process does not exist');
            }
            else
            {

                $process = self::$operations->getProcess($processid);

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

                    if( self::$internet->ipExists( $data['ipaddress'] ) == false )
                    {

                        $this->redirectError('404 Not found, maybe this IP was changed?');
                    }

                    if( self::$internet->getComputer( $data['ipaddress'] )->computerid != self::$computer->getCurrentUserComputer() )
                    {

                        if( self::$operations->requireLoggedIn( $process->process ) == true )
                        {

                            if( self::$internet->hasCurrentConnection() == false )
                            {

                                $this->redirectError('You must be connected to the computer this process was initiated on');
                            }
                            else
                            {

                                if( $data['ipaddress'] != self::$internet->getCurrentConnectedAddress() )
                                {

                                    $this->redirectError('You must be connected to the computer this process was initiated on');
                                }
                            }
                        }

                        if (self::$operations->canComplete($processid) == false)
                        {

                            $this->redirectError('Process has not yet completed');
                        }
                        else
                        {

                            self::$operations->completeProcess($processid);
                        }
                    }
                    else
                    {

                        if (self::$operations->canComplete($processid) == false)
                        {

                            $this->redirectError('Process has not yet completed');
                        }
                        else
                        {

                            self::$operations->completeProcess($processid);
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

            if (self::$operations->processExists($processid) == false)
            {

                $this->redirectError('This process does not exist');
            }
            else
            {

                $process = self::$operations->getProcess( $processid );

                if( $process->userid !== Container::getObject('session')->getSessionUser() )
                {

                    $this->redirectError('You do not own this process');
                }

                if( $process->computerid != self::$computer->getCurrentUserComputer() )
                {

                    $this->redirectError('You need to currently be switched to the computer this process was initiated on');
                }

                self::$operations->deleteProcess( $processid );

                $this->redirectSuccess('processes/computer/' . self::$computer->getCurrentUserComputer() );
            }
        }

        public function machineProcess( $computerid )
        {

            if( self::$computer->computerExists( $computerid ) == false )
            {

                $this->redirect('This computer does not exist');
            }

            $computer = self::$computer->getComputer( $computerid );

            if( $computer->userid !== Container::getObject('session')->getSessionUser() )
            {

                $this->redirect('Sorry, this computer is not yours, please try another one');
            }

            $processes = self::$operations->getComputerProcesses( $computer->computerid );

            $this->getRender('syscrack/page.process.machine', array('processes' => $processes, 'operations' => self::$operations, 'computerid' => $computerid, 'ipaddress' => $computer->ipaddress ));
        }
    }
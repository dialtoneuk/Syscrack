<?php
    namespace Framework\Views\Pages;

    /**
     * Lewis Lancaster 2017
     *
     * Class Computer
     *
     * @package Framework\Views\Pages
     */

    use Flight;
    use Framework\Application\Container;
    use Framework\Application\Settings;
    use Framework\Exceptions\ViewException;
    use Framework\Syscrack\Game\Operations;
    use Framework\Syscrack\Game\Structures\Operation;
    use Framework\Views\BaseClasses\Page as BaseClass;
    use Framework\Views\Structures\Page as Structure;

    class Computer extends BaseClass implements Structure
    {

        /**
         * @var Operations
         */

        protected $operations;

        /**
         * Computer constructor.
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
                    '/computer/', 'page'
                ],
                [
                    '/computer/log/', 'computerLog'
                ],
                [
                    '/computer/software/', 'computerSoftware'
                ],
                [
                    '/computer/processes/', 'computerProcesses'
                ],
                [
                    '/computer/processes/@processid', 'computerViewProcess'
                ],
                [
                    '/computer/actions/@process', 'computerAction'
                ],
                [
                    '/computer/actions/@process/@softwareid', 'computerSoftwareAction'
                ]
            );
        }

        /**
         * Default page
         */

        public function page()
        {

            Flight::render('syscrack/page.computer');
        }

        public function computerLog()
        {

            Flight::render('syscrack/page.computer.log');
        }

        public function computerSoftware()
        {


        }

        public function computerProcesses()
        {

            Flight::render('syscrack/page.computer.processes');
        }

        public function computerViewProcess()
        {

        }

        public function computerAction( $process )
        {

            if( $this->operations->hasProcessClass( $process ) == false )
            {

                $this->redirectError('Action not found');
            }
            else
            {

                if( $this->operations->allowLocal( $process ) == false )
                {

                    $this->redirectError('Action cannot be preformed locally');
                }

                if( $this->operations->hasProcess( $this->computer->getCurrentUserComputer(), $process, $this->getCurrentComputerAddress() ) == true )
                {

                    $this->redirectError('You already have a process of this nature processing, complete that one first');
                }

                $class = $this->operations->findProcessClass($process);

                if ($class instanceof Operation == false)
                {

                    throw new ViewException();
                }

                $completiontime = $class->getCompletionSpeed($this->computer->getCurrentUserComputer(), $process, null );

                if( $completiontime == null )
                {

                    $result = $class->onCreation(time(), $this->computer->getCurrentUserComputer(), Container::getObject('session')->getSessionUser(), $process, array(
                        'ipaddress' => $this->getCurrentComputerAddress()
                    ));

                    if( $result == false )
                    {

                        $this->redirectError('Process failed');
                    }
                    else
                    {

                        $class->onCompletion(time(), time(), $this->computer->getCurrentUserComputer(), Container::getObject('session')->getSessionUser(), $process, array(
                            'ipaddress' => $this->getCurrentComputerAddress(),
                            'redirect'  => 'computer'
                        ));
                    }
                }
                else
                {

                    $processid = $this->operations->createProcess($completiontime, $this->computer->getCurrentUserComputer(), Container::getObject('session')->getSessionUser(), $process, array(
                        'ipaddress' => $this->getCurrentComputerAddress(),
                        'redirect' => 'computer'
                    ));

                    if ($processid == false)
                    {

                        $this->redirectError('Process failed to be created');
                    }

                    Flight::redirect('/processes/' . $processid);
                }
            }
        }

        public function computerSoftwareAction( $process, $softwareid )
        {

            $operations = new Operations();

            if( $operations->hasProcessClass( $process ) == false )
            {

                $this->redirectError('Action not found');
            }
            else
            {

                if( $operations->allowLocal( $process ) == false )
                {

                    $this->redirectError('Action cannot be preformed locally');
                }
                else
                {

                    if( $this->operations->hasProcess( $this->computer->getCurrentUserComputer(), $process, $this->getCurrentComputerAddress(), $softwareid ) == true )
                    {

                        $this->redirectError('You already have a process of this nature processing, complete that one first');
                    }

                    if( $this->softwares->softwareExists( $softwareid ) == false )
                    {

                        $this->redirectError('Software does not exist');
                    }

                    if( $this->computer->hasSoftware( $this->computer->getCurrentUserComputer(), $softwareid ) == false )
                    {

                        $this->redirectError('Software does not exist');
                    }

                    if( $operations->allowSoftwares( $process ) == false )
                    {

                        Flight::redirect('/' . Settings::getSetting('syscrack_computer_page') . '/actions/' . $process );
                    }
                    else
                    {

                        $class = $operations->findProcessClass($process);

                        if ($class instanceof Operation == false)
                        {

                            throw new ViewException();
                        }

                        $completiontime = $class->getCompletionSpeed($this->computer->getCurrentUserComputer(), $process, null );

                        if( $completiontime == null )
                        {

                            $result = $class->onCreation(time(), $this->computer->getCurrentUserComputer(), Container::getObject('session')->getSessionUser(), $process, array(
                                'ipaddress' => $this->getCurrentComputerAddress(),
                                'softwareid' => $softwareid
                            ));

                            if( $result == false )
                            {

                                $this->redirectError('Process failed');
                            }
                            else
                            {

                                $class->onCompletion(time(), time(), $this->computer->getCurrentUserComputer(), Container::getObject('session')->getSessionUser(), $process, array(
                                    'ipaddress' => $this->getCurrentComputerAddress(),
                                    'softwareid' => $softwareid,
                                    'redirect'  => 'computer'
                                ));
                            }
                        }
                        else
                        {

                            $processid = $operations->createProcess($completiontime, $this->computer->getCurrentUserComputer(), Container::getObject('session')->getSessionUser(), $process, array(
                                'ipaddress' => $this->getCurrentComputerAddress(),
                                'softwareid' => $softwareid,
                                'redirect' => 'computer'
                            ));

                            if ($processid == false)
                            {

                                $this->redirectError('Process failed to be created');
                            }

                            Flight::redirect('/processes/' . $processid);
                        }
                    }
                }
            }

        }

        private function getCurrentComputerAddress()
        {

            return $this->computer->getComputer( $this->computer->getCurrentUserComputer() )->ipaddress;
        }
    }
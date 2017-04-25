<?php
    namespace Framework\Views\Pages;

    /**
     * Lewis Lancaster 2016
     *
     * Class Computer
     *
     * @package Framework\Views\Pages
     */

    use Flight;
    use Framework\Application\Container;
    use Framework\Application\Session;
    use Framework\Application\Settings;
    use Framework\Exceptions\ViewException;
    use Framework\Syscrack\Game\Operations;
    use Framework\Syscrack\Game\Structures\Operation;
    use Framework\Views\BaseClasses\Page as BaseClass;
    use Framework\Views\Structures\Page as Structure;

    class Computer extends BaseClass implements Structure
    {

        /**
         * Computer constructor.
         */

        public function __construct()
        {

            parent::__construct( true );

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


        }

        public function computerViewProcess()
        {

        }

        public function computerAction( $process )
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

                    $this->redirectError('Action cannot be preformed on a local machine');
                }

                $class = $operations->findProcessClass($process);

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

                    $processid = $operations->createProcess($completiontime, $this->computer->getCurrentUserComputer(), Container::getObject('session')->getSessionUser(), $process, array(
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


        }

        private function getCurrentComputerAddress()
        {

            return $this->computer->getComputer( $this->computer->getCurrentUserComputer() )->ipaddress;
        }
    }
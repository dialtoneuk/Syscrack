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
    use Framework\Application\Session;
    use Framework\Application\Settings;
    use Framework\Application\Utilities\PostHelper;
    use Framework\Exceptions\SyscrackException;
    use Framework\Syscrack\Game\Operations;
    use Framework\Views\BaseClasses\Page as BaseClass;
    use Framework\Views\Structures\Page as Structure;

    class Computer extends BaseClass implements Structure
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
         * Computer constructor.
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
                    '/computer/', 'page'
                ],
                [
                    '/computer/log/', 'computerLog'
                ],
                [
                    '/computer/processes/', 'computerProcesses'
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

        /**
         * Displays the computer log
         */

        public function computerLog()
        {

            Flight::render('syscrack/page.computer.log');
        }

        /**
         * Displays the comptuer processes
         */

        public function computerProcesses()
        {

            Flight::render('syscrack/page.computer.processes');
        }

        /**
         * Processes a computers action
         *
         * @param $process
         */

        public function computerAction( $process )
        {

            if( $this->operations->hasProcessClass( $process ) == false )
            {

                $this->redirectError('Invalid action');
            }

            $computerid = $this->computer->getCurrentUserComputer();

            $ipaddress = $this->getCurrentComputerAddress();

            if( $this->operations->hasProcess( $computerid, $process, $ipaddress ) == true )
            {

                $this->redirectError('You already have an action of this nature processing', 'computer/processes');
            }

            if( $this->operations->allowLocal( $process ) == false )
            {

                $this->redirectError('This action must be ran on a remote computer');
            }

            if( $this->operations->requireSoftwares( $process ) == true )
            {

                $this->redirectError('A software is required to preform this action');
            }

            if( $this->operations->allowCustomData( $process ) == true )
            {

                $data = $this->getCustomData( $process, $ipaddress, $this->session->getSessionUser() );
            }
            else
            {

                $data = [];
            }

            $class = $this->operations->findProcessClass($process);

            $result = $class->onCreation(time(), $this->computer->getCurrentUserComputer(), $this->session->getSessionUser(), $process, array(
                'ipaddress'     => $ipaddress,
                'custom'        => $data
            ));

            if( $result == false )
            {

                $this->redirectError('Unable to complete process' );
            }

            $completiontime = $class->getCompletionSpeed($this->computer->getCurrentUserComputer(), $process, null );

            if( $completiontime !== null )
            {

                $processid = $this->operations->createProcess($completiontime, $this->computer->getCurrentUserComputer(), $this->session->getSessionUser(), $process, array(
                    'ipaddress'     => $ipaddress,
                    'custom'        => $data,
                    'redirect'      => 'computer'
                ));

                $this->redirect('processes/' . $processid, true );
            }

            $class->onCompletion(time(), time(), $this->computer->getCurrentUserComputer(), $this->session->getSessionUser(), $process, array(
                'ipaddress'     => $ipaddress,
                'custom'        => $data,
                'redirect'      => 'computer'
            ));
        }

        /**
         * Processes a computers software action
         *
         * @param $process
         *
         * @param $softwareid
         */

        public function computerSoftwareAction( $process, $softwareid )
        {

            if( $this->operations->hasProcessClass( $process ) == false )
            {

                $this->redirectError('Invalid action');
            }

            $computerid = $this->computer->getCurrentUserComputer();

            $ipaddress = $this->getCurrentComputerAddress();

            if( $this->operations->hasProcess( $computerid, $process, $ipaddress ) == true )
            {

                $this->redirectError('You already have an action of this nature processing', 'computer/processes');
            }

            if( $this->operations->allowLocal( $process ) == false )
            {

                $this->redirectError('This action must be ran on a remote computer');
            }

            if( $this->operations->allowSoftwares( $process ) == false )
            {

                $this->redirect( 'computer/actions/' . $process );
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

                            $this->redirectError('Missing information');
                    }

                        $result = $class->onPost( PostHelper::returnRequirements( $requirements ), $ipaddress, $this->session->getSessionUser() );
                    }
                    else
                    {

                        $result = $class->onPost( PostHelper::getPost(), $ipaddress, $this->session->getSessionUser() );
                    }

                    if( $result == false )
                    {

                        $this->redirectError('Unable to complete process');
                    }
                }
            }

            $software = $this->softwares->getSoftware( $softwareid );

            if( $this->softwares->isEditable( $software->softwareid ) == false )
            {

                if( $process == Settings::getSetting('syscrack_view_process') )
                {

                    if( $this->softwares->canView( $software->softwareid ) == false )
                    {

                        $this->redirectError('This software cannot be modified or edited' );
                    }
                }
                else
                {

                    if( $this->operations->allowAnonymous( $process ) == false )
                    {

                        $this->redirectError('This software cannot be modified or edited' );
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
                'softwareid'    => $software->softwareid,
                'custom'        => $data
            ));

            if( $result == false )
            {

                $this->redirectError('Unable to complete process' );
            }

            $completiontime = $class->getCompletionSpeed($this->computer->getCurrentUserComputer(), $process,  $softwareid );

            if( $completiontime !== null )
            {

                $processid = $this->operations->createProcess($completiontime, $this->computer->getCurrentUserComputer(), $this->session->getSessionUser(), $process, array(
                    'ipaddress'     => $ipaddress,
                    'softwareid'    => $software->softwareid,
                    'custom'        => $data,
                    'redirect'      => 'computer'
                ));

                $this->redirect('processes/' . $processid, true );
            }

            $class->onCompletion(time(), time(), $this->computer->getCurrentUserComputer(), $this->session->getSessionUser(), $process, array(
                'ipaddress'     => $ipaddress,
                'softwareid'    => $software->softwareid,
                'custom'        => $data,
                'redirect'      => 'computer'
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
         * Gets the current computers address
         *
         * @return mixed
         */

        private function getCurrentComputerAddress()
        {

            return $this->computer->getComputer( $this->computer->getCurrentUserComputer() )->ipaddress;
        }
    }
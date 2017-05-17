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
    use Framework\Exceptions\ViewException;
    use Framework\Syscrack\Game\AddressDatabase;
    use Framework\Syscrack\Game\Finance;
    use Framework\Syscrack\Game\Log;
    use Framework\Syscrack\Game\Operations;
    use Framework\Syscrack\Game\Statistics;
    use Framework\Syscrack\Game\Structures\Software;
    use Framework\Syscrack\Game\Utilities\PageHelper;
    use Framework\Syscrack\Game\Viruses;
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
         * @var Finance
         */

        protected $finance;

        /**
         * @var AddressDatabase
         */

        protected $addressdatabase;

        /**
         * @var Viruses
         */

        protected $viruses;

        /**
         * @var PageHelper
         */

        protected $pagehelper;

        /**
         * @var Log
         */

        protected $log;

        /**
         * @var Statistics
         */

        protected $statistics;

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

            if( isset( $this->finance ) == false )
            {

                $this->finance = new Finance();
            }

            if( isset( $this->session ) == false )
            {

                if( Container::hasObject('session') == false )
                {

                    Container::setObject('session', new Session() );
                }

                $this->session = Container::getObject('session');
            }

            if( isset( $this->addressdatabase ) == false )
            {

                if( isset( $this->session ) == false )
                {

                    throw new ViewException();
                }

                $this->addressdatabase = new AddressDatabase( $this->session->getSessionUser() );
            }

            if( isset ( $this->pagehelper ) == false )
            {

                $this->pagehelper = new PageHelper();
            }

            if( isset( $this->viruses ) == false )
            {

                $this->viruses = new Viruses();
            }

            if( isset( $this->log ) == false )
            {

                $this->log = new Log();
            }

            if( isset( $this->statistics ) == false )
            {

                $this->statistics = new Statistics();
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
                    '/computer/hardware/', 'computerHardware'
                ],
                [
                    'GET /computer/collect', 'computerCollect'
                ],
                [
                    'POST /computer/collect', 'computerCollectProcess'
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

        public function computerHardware()
        {

            Flight::render('syscrack/page.computer.hardware');
        }

        /**
         * Displays the comptuer processes
         */

        public function computerProcesses()
        {

            Flight::render('syscrack/page.computer.processes');
        }

        /**
         * Renders the collection page
         */

        public function computerCollect()
        {

            if( $this->computer->hasType( $this->computer->getCurrentUserComputer(), Settings::getSetting('syscrack_software_collector_type'), true ) == false )
            {

                $this->redirect('computer');
            }

            Flight::render('syscrack/page.computer.collect');
        }

        /**
         * Processes the virus collection process
         */

        public function computerCollectProcess()
        {

            if( $this->computer->hasType( $this->computer->getCurrentUserComputer(), Settings::getSetting('syscrack_software_collector_type'), true ) == false )
            {

                $this->redirect('computer');
            }

            if( PostHelper::hasPostData() == false )
            {

                $this->computerCollect();
            }
            else
            {

                if( PostHelper::checkForRequirements(['accountnumber'] ) == false )
                {

                    $this->redirectError('Please enter an account number', 'computer/collect');
                }

                $accountnumber = PostHelper::getPostData('accountnumber');

                if( $this->finance->accountNumberExists( $accountnumber ) == false )
                {

                    $this->redirectError('This account does not exist', 'computer/collect');
                }

                $addressdatabase = $this->addressdatabase->getDatabase();

                if( empty( $addressdatabase ) )
                {

                    $this->redirectError('Your address database is empty, go hack somebody', 'computer/collect');
                }

                $results = [];

                foreach( $addressdatabase as $address )
                {

                    if( $this->internet->ipExists( $address['ipaddress'] ) == false )
                    {

                        $results[ $address['ipaddress'] ] = array(
                            'ipaddress' => 'null',
                            'message' => 'Failed to connect to address'
                        );
                    }
                    else
                    {

                        $computer = $this->internet->getComputer( $address['ipaddress'] );

                        if( $this->viruses->hasVirusesOnComputer( $computer->computerid, $this->session->getSessionUser() ) == false )
                        {

                            continue;
                        }

                        $viruses = $this->viruses->getVirusesOnComputer( $computer->computerid, $this->session->getSessionUser() );

                        foreach( $viruses as $virus )
                        {

                            if( ( time() - $virus->lastmodified ) <= Settings::getSetting('syscrack_collector_cooldown') )
                            {

                                $results[ $address['ipaddress'] ] = array(
                                    'ipaddress' => $address['ipaddress'],
                                    'message'   => 'Virus needs to run for a longer period'
                                );
                            }
                            else
                            {

                                $class = $this->softwares->getSoftwareClassFromID( $virus->softwareid );

                                if( $class instanceof Software == false )
                                {

                                    throw new SyscrackException();
                                }

                                $result = $class->onCollect( $virus->softwareid, $this->session->getSessionUser(), $computer->computerid, time() - $virus->lastmodified );

                                if( empty( $result ) | $result == null )
                                {

                                    $results[ $address['ipaddress'] ] = array(
                                        'ipaddress' => $address['ipaddress'],
                                        'message'   => 'Virus generated no profits'
                                    );
                                }
                                else
                                {

                                    $results[ $address['ipaddress'] ] = array(
                                        'ipaddress' => $address['ipaddress'],
                                        'message'   =>  $virus->softwarename . ' ran for ' . gmdate("H:i:s", ( time() - $virus->lastmodified ) ) . ' and generated ' . Settings::getSetting('syscrack_currency') . number_format( ( $result * $this->pagehelper->getInstalledCollector()['level'] ) ),
                                        'profits'   => ( $result * $this->pagehelper->getInstalledCollector()['level'] )
                                    );

                                    $this->viruses->updateVirusModified( $virus->softwareid );
                                }
                            }
                        }
                    }
                }

                if( empty( $results ) )
                {

                    $this->redirectError('Nothing was collected, this is probably because you tried to collect too soon. You need to wait ' . Settings::getSetting('syscrack_collector_cooldown') . ' seconds per execution', 'computer/collect');
                }

                $account = $this->finance->getByAccountNumber( $accountnumber );

                if( $account == null )
                {

                    $this->redirectError('Sorry, your account wasnt able to be retrieved, you should tell a developer', 'computer/collect' );
                }

                $total = 0;

                foreach( $results as $profit )
                {

                    if( isset( $profit['profits'] ) == false )
                    {

                        continue;
                    }

                    $total = $total + $profit['profits'];
                }

                if( $total != 0 )
                {

                    if( Settings::getSetting('syscrack_statistics_enabled') == true )
                    {

                        $this->statistics->addStatistic('collected', $total );
                    }

                    $this->finance->deposit( $account->computerid, $this->session->getSessionUser(), $total );

                    $this->log->updateLog('Deposited ' . Settings::getSetting('syscrack_currency') . number_format(  $total ) . ' into account (' . $accountnumber . ') at bank <' . $this->internet->getComputerAddress( $account->computerid ) . '>', $this->computer->getCurrentUserComputer(), 'localhost');
                }

                Flight::render('syscrack/page.computer.collect', array( 'results' => $results, 'total' => $total ));
            }
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

            $completiontime = $class->getCompletionSpeed($this->computer->getCurrentUserComputer(), $ipaddress, null );

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

                if( $process == Settings::getSetting('syscrack_operations_view_process') )
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

            $completiontime = $class->getCompletionSpeed($this->computer->getCurrentUserComputer(), $ipaddress, $software->softwareid );

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
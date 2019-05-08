<?php
    namespace Framework\Views\Pages;

    /**
     * Lewis Lancaster 2017
     *
     * Class Computers
     *
     * @package Framework\Views\Pages
     */

    use Framework\Application\Render;
    use Framework\Application\Settings;
    use Framework\Application\Utilities\PostHelper;
    use Framework\Exceptions\SyscrackException;
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

        protected static $operations;

        /**
         * @var Finance
         */

        protected static $finance;

        /**
         * @var AddressDatabase
         */

        protected static $addressdatabase;

        /**
         * @var Viruses
         */

        protected static $viruses;

        /**
         * @var PageHelper
         */

        protected static $pagehelper;

        /**
         * @var Log
         */

        protected static $log;

        /**
         * @var Statistics
         */

        protected static $statistics;


        /**
         * Computer constructor.
         */

        public function __construct()
        {

            if( isset( self::$operations ) == false )
                self::$operations = new Operations();

            if( isset( self::$finance ) == false )
                self::$finance = new Finance();

            if( isset( self::$addressdatabase ) == false )
                self::$addressdatabase = new AddressDatabase();

            if( isset ( self::$pagehelper ) == false )
                self::$pagehelper = new PageHelper();

            if( isset( self::$viruses ) == false )
                self::$viruses = new Viruses();

            if( isset( self::$log ) == false )
                self::$log = new Log();

            if( isset( self::$statistics ) == false )
                self::$statistics = new Statistics();

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
                    'GET /computer/research', 'computerResearch'
                ],
                [
                    'POST /computer/research', 'computerResearchProcess'
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

            Render::view('syscrack/page.computer', [], $this->model());
        }

        /**
         * Displays the computer log
         */

        public function computerLog()
        {

            Render::view('syscrack/page.computer.log', [], $this->model());
        }

        public function computerHardware()
        {

            Render::view('syscrack/page.computer.hardware', [], $this->model());
        }

        /**
         * Displays the comptuer processes
         */

        public function computerProcesses()
        {

            Render::view('syscrack/page.computer.processes', [] , $this->model());
        }

        /**
         * Renders the collection page
         */

        public function computerCollect()
        {

            if( parent::$computer->hasType( parent::$computer->computerid(), Settings::setting('syscrack_software_collector_type'), true ) == false )
                $this->redirect('computer');

            $collector = parent::$software->getSoftware( parent::$computer->getCollector( parent::$computer->computerid() ) );

            Render::view('syscrack/page.computer.collect', ["collector" => $collector], $this->model());
        }

        protected $path = 'computer/collect';

        /**
         * Processes the virus collection process
         */

        public function computerCollectProcess()
        {

            if( parent::$computer->hasType( parent::$computer->computerid(), Settings::setting('syscrack_software_collector_type'), true ) == false )
                $this->redirect('computer');
            else
                if( PostHelper::hasPostData() == false )
                    $this->computerCollect();
                else
                {

                    if( PostHelper::checkForRequirements(['accountnumber'] ) == false )
                        $this->redirectError('Invalid account number', $this->path );
                    else
                    {

                        $accountnumber = PostHelper::getPostData('accountnumber');

                        if( self::$finance->accountNumberExists( $accountnumber ) )
                        {

                            $addresses = $this->getAddresses( parent::$session->userid() );
                            $information = array();
                            $collection = 0;

                            foreach( $addresses as $address )
                                if( $address["status"] == 3 )
                                {
                                    $this->calculateProfit( $address["virus"], $address["ipaddress"], $collection, $information );
                                    parent::$software->updateLastModified( $address["virus"]->softwareid );
                                }

                            $collector = parent::$software->getSoftware( parent::$computer->getCollector( parent::$computer->computerid() ) );

                            if( empty( $information ) || $collection === 0 )
                                $this->redirectError("You collected zero profits, this could be due to the frequency of which you are collected. Wait a while and try again.", $this->path );
                            else
                            {

                                $collection = $collection * $collector->level;
                                $this->payout( $collection, $accountnumber );
                                self::$statistics->addStatistic("collected", $collection );
                                Render::view('syscrack/page.computer.collect', array( 'results' => $information, 'total' => $collection ), $this->model());
                            }
                        }
                        else
                            $this->redirectError("Account does not exist", $this->path );
                    }
                }
        }

        private function payout( $collection, $accountnumber )
        {

            $account = self::$finance->getByAccountNumber( $accountnumber );
            self::$finance->deposit( $account->computerid, $account->userid, $collection );
            self::$log->updateLog('Deposited '
                . Settings::setting('syscrack_currency')
                . number_format(  $collection ) .
                ' into account (' . $accountnumber . ') at bank <' . parent::$internet->getComputerAddress( $account->computerid )
                . '>', parent::$computer->computerid(), 'localhost');
        }

        private function calculateProfit( $software, $ipaddress, &$collection, &$information )
        {

            /**
             * @var $class Software;
             */

            $class = parent::$software->getSoftwareClassFromID( $software->softwareid );
            $collection += $class->onCollect( $software->softwareid, $software->userid, $software->computerid, ( time() - ( $software->lastmodified ) )  );

            $information[ $ipaddress ] = [
                'ipaddress' => $ipaddress,
                'message'   =>  $software->softwarename . ' ran for ' . gmdate("H:i:s", ( time() - $software->lastmodified ) ) . ' and generated ' . Settings::setting('syscrack_currency') . number_format( ( $collection ) ),
                'profits'   => ( $software )
            ];
        }

        private function getAddresses( $userid )
        {

            $addresses = self::$addressdatabase->getUserAddresses( $userid );
            $results = [];

            if( empty( $addresses ) )
                return null;

            foreach( $addresses as $address )
                if( parent::$internet->ipExists( $address["ipaddress"] ) == false )
                    $results[] = array_merge( $address,  [ "status" => 0 ] );
                else
                {

                    $computerid = parent::$internet->getComputer( $address["ipaddress"] )->computerid;

                    if( self::$viruses->hasVirusesOnComputer( $computerid, $userid ) == false )
                        $results[] = array_merge(  $address, [ "status" => 1 ] );
                    else
                    {

                        $viruses = self::$viruses->getVirusesOnComputer( $computerid, $userid );

                        foreach( $viruses as $virus )
                            if( $virus->installed && ( time() - $virus->lastmodified ) >= Settings::setting('syscrack_collector_cooldown') )
                                $results[] = array_merge( $address, [ "status" => 3, "virus" => $virus] );
                            else
                                $results[] = array_merge( $address, [ "status" => 2, "virus" => $virus] );
                    }
                }

            return $results;
        }

        /**
        public function computerCollectProcess()
        {

            if( parent::$computer->hasType( parent::$computer->getCurrentUserComputer(), Settings::getSetting('syscrack_software_collector_type'), true ) == false )
                $this->redirect('computer');


            if( PostHelper::hasPostData() == false )
                parent::$computerCollect();

            else
            {

                if( PostHelper::checkForRequirements(['accountnumber'] ) == false )
                    $this->redirectError('Please enter an account number', 'computer/collect');

                $accountnumber = PostHelper::getPostData('accountnumber');

                if( self::$finance->accountNumberExists( $accountnumber ) == false )
                    $this->redirectError('This account does not exist', 'computer/collect');


                $addressdatabase = self::$addressdatabase->getUserAddresses( parent::$session->getSessionUser() );

                if( empty( $addressdatabase ) )
                {

                    $this->redirectError('Your address database is empty, go hack somebody', 'computer/collect');
                }

                $results = [];

                foreach( $addressdatabase as $address )
                {

                    if( parent::$internet->ipExists( $address['ipaddress'] ) == false )
                    {

                        $results[ $address['ipaddress'] ] = array(
                            'ipaddress' => 'null',
                            'message' => 'Failed to connect to address, address removed from addressbook'
                        );

                        self::$addressdatabase->deleteAddress( $address['ipaddress'], parent::$session->getSessionUser() );
                    }
                    else
                    {

                        $computer = parent::$internet->getComputer( $address['ipaddress'] );

                        if( self::$viruses->hasVirusesOnComputer( $computer->computerid, parent::$session->getSessionUser() ) == false )
                        {

                            continue;
                        }

                        $viruses = self::$viruses->getVirusesOnComputer( $computer->computerid, parent::$session->getSessionUser() );

                        foreach( $viruses as $virus )
                        {

                            if ( $virus->installed == false )
                            {

                                continue;
                            }

                            if( ( time() - $virus->lastmodified ) <= Settings::getSetting('syscrack_collector_cooldown') )
                            {

                                $results[ $address['ipaddress'] ] = array(
                                    'ipaddress' => $address['ipaddress'],
                                    'message'   => 'Virus needs to run for a longer period'
                                );
                            }
                            else
                            {

                                $class = parent::$software->getSoftwareClassFromID( $virus->softwareid );

                                if( $class instanceof Software == false )
                                {

                                    throw new SyscrackException();
                                }

                                $result = $class->onCollect( $virus->softwareid, parent::$session->getSessionUser(), $computer->computerid, time() - $virus->lastmodified );

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
                                        'message'   =>  $virus->softwarename . ' ran for ' . gmdate("H:i:s", ( time() - $virus->lastmodified ) ) . ' and generated ' . Settings::getSetting('syscrack_currency') . number_format( ( $result * self::$pagehelper->getInstalledCollector()['level'] ) ),
                                        'profits'   => ( $result * self::$pagehelper->getInstalledCollector()['level'] )
                                    );

                                    self::$viruses->updateVirusModified( $virus->softwareid );
                                }
                            }
                        }
                    }
                }

                if( empty( $results ) )
                {

                    $this->redirectError('Nothing was collected, this is probably because you tried to collect too soon. You need to wait ' . Settings::getSetting('syscrack_collector_cooldown') . ' seconds per execution', 'computer/collect');
                }

                $account = self::$finance->getByAccountNumber( $accountnumber );

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

                        self::$statistics->addStatistic('collected', $total );
                    }

                    self::$finance->deposit( $account->computerid, parent::$session->getSessionUser(), $total );

                    self::$log->updateLog('Deposited ' . Settings::getSetting('syscrack_currency') . number_format(  $total ) . ' into account (' . $accountnumber . ') at bank <' . parent::$internet->getComputerAddress( $account->computerid ) . '>', parent::$computer->getCurrentUserComputer(), 'localhost');
                }

                Render::view('syscrack/page.computer.collect', array( 'results' => $results, 'total' => $total ), $this->model());
            }
        }
        **/

        public function computerResearch()
        {

            if( parent::$computer->hasType( parent::$computer->computerid(), Settings::setting('syscrack_software_research_type'), true ) == false )
            {

                $this->redirect('computer');
            }

            Render::view('syscrack/page.computer.research', [], $this->model());
        }

        public function computerResearchProcess()
        {

            if( PostHelper::hasPostData() == false )
            {

                $this->computerResearch();
            }
            else
            {

                if( PostHelper::checkForRequirements(['action'] ) == false )
                {

                    $this->redirectError('Missing Information', 'computer/research');
                }

                $action = PostHelper::getPostData('action');

                if( $action == 'licensesoftware' )
                {

                    if( PostHelper::checkForRequirements(['softwareid','accountnumber'] ) == false )
                    {

                        $this->redirectError('Missing Information','computer/research');
                    }

                    $softwareid = PostHelper::getPostData('softwareid');

                    if( parent::$software->softwareExists( $softwareid ) == false )
                    {

                        $this->redirectError('Software does not exist', 'computer/research');
                    }

                    $software = parent::$software->getSoftware( $softwareid );

                    if( parent::$computer->hasSoftware( parent::$computer->computerid(), $softwareid ) == false )
                    {

                        $this->redirectError('This computer does not have this current software', 'computer/research');
                    }

                    $data = parent::$software->getSoftwareData( $software->softwareid );

                    if( isset( $data['license'] ) )
                    {

                        if( $data['license'] !== null )
                        {

                            $this->redirectError('This software is already licensed', 'computer/research');
                        }
                    }

                    $accountnumber = PostHelper::getPostData('accountnumber');

                    if( self::$finance->accountNumberExists( $accountnumber ) == false )
                    {

                        $this->redirectError('Account does not exist', 'computer/research');
                    }

                    $account = self::$finance->getByAccountNumber( $accountnumber );

                    if( self::$finance->canAfford( $account->computerid, $account->userid, $this->getLicensePrice( $softwareid ) ) == false )
                    {

                        $this->redirectError('You cannot afford to license this software', 'computer/research' );
                    }

                    self::$finance->withdraw( $account->computerid, $account->userid, $this->getLicensePrice( $softwareid ) );

                    parent::$software->licenseSoftware( $softwareid, parent::$session->userid() );

                    self::$log->updateLog('Purchased license for ' . Settings::setting('syscrack_currency') . number_format( $this->getLicensePrice( $softwareid ) ) . ' payed with account (' . $accountnumber . ') at bank <' . parent::$internet->getComputerAddress( $account->computerid ) . '>', parent::$computer->computerid(), 'localhost');

                    $this->redirectSuccess('computer/research');
                }
                if ( $action == 'research' )
                {

                    if( PostHelper::checkForRequirements(['softwareid','accountnumber'] ) == false )
                    {

                        $this->redirectError('Missing Information','computer/research');
                    }

                    $softwareid = PostHelper::getPostData('softwareid');

                    if( parent::$software->softwareExists( $softwareid ) == false )
                    {

                        $this->redirectError('Software does not exist', 'computer/research');
                    }

                    $software = parent::$software->getSoftware( $softwareid );

                    if( parent::$computer->hasSoftware( parent::$computer->computerid(), $softwareid ) == false )
                    {

                        $this->redirectError('This computer does not have this current software', 'computer/research');
                    }

                    $data = parent::$software->getSoftwareData( $software->softwareid );

                    if( isset( $data['license'] ) )
                    {

                        if( $data['license'] !== null )
                        {

                            $this->redirectError('This software is already licensed', 'computer/research');
                        }
                    }

                    $accountnumber = PostHelper::getPostData('accountnumber');

                    if( self::$finance->accountNumberExists( $accountnumber ) == false )
                    {

                        $this->redirectError('Account does not exist', 'computer/research');
                    }

                    $account = self::$finance->getByAccountNumber( $accountnumber );

                    if( self::$finance->canAfford( $account->computerid, $account->userid, $this->getLicensePrice( $softwareid ) ) == false )
                    {

                        $this->redirectError('You cannot afford to research this software', 'computer/research' );
                    }


                }
            }
        }

        /**
         * Processes a computer action
         *
         * @param $process
         */

        public function computerAction( $process )
        {

            if( self::$operations->hasProcessClass( $process ) == false )
            {

                $this->redirectError('Invalid action');
            }

            $computerid = parent::$computer->computerid();

            $ipaddress = $this->getCurrentComputerAddress();

            if( self::$operations->hasProcess( $computerid, $process, $ipaddress ) == true )
            {

                $this->redirectError('You already have an action of this nature processing', 'computer/processes');
            }

            if( self::$operations->allowLocal( $process ) == false )
            {

                $this->redirectError('This action must be ran on a remote computer');
            }

            if( self::$operations->requireSoftware( $process ) == true )
            {

                $this->redirectError('A software is required to preform this action');
            }

            if( self::$operations->allowCustomData( $process ) == true )
            {

                $data = $this->getCustomData( $process, $ipaddress, parent::$session->userid() );
            }
            else
            {

                $data = [];
            }

            $class = self::$operations->findProcessClass($process);

            $result = $class->onCreation(time(), parent::$computer->computerid(), parent::$session->userid(), $process, array(
                'ipaddress'     => $ipaddress,
                'custom'        => $data
            ));

            if( $result == false )
            {

                $this->redirectError('Unable to complete process' );
            }

            $completiontime = $class->getCompletionSpeed(parent::$computer->computerid(), $ipaddress, null );

            if( $completiontime !== null )
            {

                $processid = self::$operations->createProcess($completiontime, parent::$computer->computerid(), parent::$session->userid(), $process, array(
                    'ipaddress'     => $ipaddress,
                    'custom'        => $data,
                    'redirect'      => 'computer'
                ));

                $this->redirect('processes/' . $processid, true );
            }

            $class->onCompletion(time(), time(), parent::$computer->computerid(), parent::$session->userid(), $process, array(
                'ipaddress'     => $ipaddress,
                'custom'        => $data,
                'redirect'      => 'computer'
            ));
        }

        /**
         * Processes a computer software action
         *
         * @param $process
         *
         * @param $softwareid
         */

        public function computerSoftwareAction( $process, $softwareid )
        {

            if( self::$operations->hasProcessClass( $process ) == false )
            {

                $this->redirectError('Invalid action');
            }

            $computerid = parent::$computer->computerid();

            $ipaddress = $this->getCurrentComputerAddress();

            if( self::$operations->hasProcess( $computerid, $process, $ipaddress ) == true )
            {

                $this->redirectError('You already have an action of this nature processing', 'computer/processes');
            }

            if( self::$operations->allowLocal( $process ) == false )
            {

                $this->redirectError('This action must be ran on a remote computer');
            }

            if( self::$operations->allowSoftware( $process ) == false )
            {

                $this->redirect( 'computer/actions/' . $process );
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

                            $this->redirectError('Missing information');
                    }

                        $result = $class->onPost( PostHelper::returnRequirements( $requirements ), $ipaddress, parent::$session->userid() );
                    }
                    else
                    {

                        $result = $class->onPost( PostHelper::getPost(), $ipaddress, parent::$session->userid() );
                    }

                    if( $result == false )
                    {

                        $this->redirectError('Unable to complete process');
                    }
                }
            }

            if ( parent::$software->softwareExists( $softwareid ) == false )
            {

                $this->redirectError('Software does not exist');
            }

            $software = parent::$software->getSoftware( $softwareid );

            if( parent::$software->isEditable( $software->softwareid ) == false )
            {

                if( $process == Settings::setting('syscrack_operations_view_process') )
                {

                    if( parent::$software->canView( $software->softwareid ) == false )
                    {

                        $this->redirectError('This software cannot be modified or edited' );
                    }
                }
                else
                {

                    if( self::$operations->allowAnonymous( $process ) == false )
                    {

                        $this->redirectError('This software cannot be modified or edited' );
                    }
                }
            }

            if( self::$operations->allowCustomData( $process ) == true )
            {

                $data = $this->getCustomData( $process, $ipaddress, parent::$session->userid() );
            }
            else
            {

                $data = [];
            }

            $result = $class->onCreation(time(), parent::$computer->computerid(), parent::$session->userid(), $process, array(
                'ipaddress'     => $ipaddress,
                'softwareid'    => $software->softwareid,
                'custom'        => $data
            ));

            if( $result == false )
            {

                $this->redirectError('Unable to complete process' );
            }

            $completiontime = $class->getCompletionSpeed(parent::$computer->computerid(), $ipaddress, $software->softwareid );

            if( $completiontime !== null )
            {

                $processid = self::$operations->createProcess($completiontime, parent::$computer->computerid(), parent::$session->userid(), $process, array(
                    'ipaddress'     => $ipaddress,
                    'softwareid'    => $software->softwareid,
                    'custom'        => $data,
                    'redirect'      => 'computer'
                ));

                $this->redirect('processes/' . $processid, true );
            }

            $class->onCompletion(time(), time(), parent::$computer->computerid(), parent::$session->userid(), $process, array(
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
         * Gets the license price of a software
         *
         * @param $softwareid
         *
         * @return int
         */

        private function getLicensePrice( $softwareid )
        {

            $software = parent::$software->getSoftware( $softwareid );

            if( $software->level * Settings::setting('syscrack_research_price_multiplier') <= 0 )
            {

                return 0;
            }

            return $software->level * Settings::setting('syscrack_research_price_multiplier');
        }

        /**
         * Gets the current computer address
         *
         * @return mixed
         */

        private function getCurrentComputerAddress()
        {

            return parent::$computer->getComputer( parent::$computer->computerid() )->ipaddress;
        }
    }
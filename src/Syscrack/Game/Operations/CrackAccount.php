<?php
    namespace Framework\Syscrack\Game\Operations;

    /**
     * Lewis Lancaster 2017
     *
     * Class CrackAccount
     *
     * @package Framework\Syscrack\Game\Operations
     */

    use Framework\Application\Settings;
    use Framework\Application\Utilities\PostHelper;
    use Framework\Exceptions\SyscrackException;
    use Framework\Syscrack\Game\AccountDatabase;
    use Framework\Syscrack\Game\BaseClasses\Operation as BaseClass;
    use Framework\Syscrack\Game\Finance;
    use Framework\Syscrack\Game\Structures\Operation as Structure;

    class CrackAccount extends BaseClass implements Structure
    {

        /**
         * @var Finance
         */

        protected static $finance;

        /**
         * @var AccountDatabase;
         */

        protected static $bankdatabase;

        /**
         * CrackAccount constructor.
         */

        public function __construct()
        {

            if( isset( self::$finance ) == false )
                self::$finance = new Finance();


            if( isset( self::$bankdatabase ) == false )
                self::$bankdatabase = new AccountDatabase();

            parent::__construct( true );
        }

        /**
         * Returns the configuration
         *
         * @return array
         */

        public function configuration()
        {

            return array(
                'allowsoftware'    => false,
                'allowlocal'        => false,
                'requiresoftware'  => false,
                'requireloggedin'   => true,
                'allowpost'         => false,
                'allowcustomdata'   => true,
            );
        }

        /**
         * Called when this operation is created
         *
         * @param $timecompleted
         *
         * @param $computerid
         *
         * @param $userid
         *
         * @param $process
         *
         * @param array $data
         *
         * @return bool
         */

        public function onCreation($timecompleted, $computerid, $userid, $process, array $data)
        {

            self::$bankdatabase->loadDatabase( $userid );

            if( $this->checkData( $data, ['ipaddress','custom'] ) == false )
            {

                return false;
            }

            if( $this->checkCustomData( $data, ['accountnumber'] ) == false )
            {

                return false;
            }

            if( self::$finance->accountNumberExists( $data['custom']['accountnumber'] ) == false )
            {

                $this->redirectError('Account does not exist', $this->getRedirect( $data['ipaddress'] ) );
            }

            if( self::$finance->hasCurrentActiveAccount() )
            {

                if( self::$finance->getCurrentActiveAccount() == $data['custom']['accountnumber'] )
                {

                    $this->redirectError('You have already hacked this bank account', $this->getRedirect( $data['ipaddress'] ) );
                }
            }

            if( self::$finance->getByAccountNumber( $data['custom']['accountnumber'] )->computerid !== $this->getComputerId( $data['ipaddress'] ) )
            {

                $this->redirectError('This account does not exist in this banks database', $this->getRedirect( $data['ipaddress'] ) );
            }

            if( self::$finance->getByAccountNumber( $data['custom']['accountnumber'] )->userid == $userid )
            {

                $this->redirectError('You cant crack your own account, stupid', $this->getRedirect( $data['ipaddress'] ) );
            }

            return true;
        }

        /**
         * Called when the process is completed
         *
         * @param $timecompleted
         *
         * @param $timestarted
         *
         * @param $computerid
         *
         * @param $userid
         *
         * @param $process
         *
         * @param array $data
         */

        public function onCompletion($timecompleted, $timestarted, $computerid, $userid, $process, array $data)
        {

            if( $this->checkData( $data, ['ipaddress','custom'] ) == false )
            {

                throw new SyscrackException();
            }

            if( self::$internet->ipExists( $data['ipaddress'] ) == false )
            {

                $this->redirectError('Sorry, this ip address does not exist anymore', $this->getRedirect() );
            }

            if( $this->checkCustomData( $data, ['accountnumber'] ) == false )
            {

                throw new SyscrackException();
            }

            self::$finance->setCurrentActiveAccount( $data['custom']['accountnumber'] );

            self::$bankdatabase->addAccountNumber( $data['custom']['accountnumber'], $data['ipaddress'] );

            $this->logCrack( $data['custom']['accountnumber'], $this->getComputerId( $data['ipaddress'] ), self::$computers->getComputer( $computerid )->ipaddress );

            $this->logLocal( $computerid, $data['custom']['accountnumber'], $data['ipaddress'] );

            $this->redirectSuccess( $this->getRedirect($data['ipaddress'] ) );
        }

        /**
         * Gets the completion speed for this action
         *
         * @param $computerid
         *
         * @param $ipaddress
         *
         * @param $softwareid
         *
         * @return null
         */

        public function getCompletionSpeed($computerid, $ipaddress, $softwareid=null )
        {

            return $this->calculateProcessingTime( $computerid, Settings::getSetting('syscrack_hardware_cpu_type'), Settings::getSetting('syscrack_operations_hack_speed') );
        }

        /**
         * Returns the custom data for this operation
         *
         * @param $ipaddress
         *
         * @param $userid
         *
         * @return array|null
         */

        public function getCustomData($ipaddress, $userid)
        {

            if( PostHelper::hasPostData() == false )
            {

                return null;
            }

            if( PostHelper::checkForRequirements( ['accountnumber'] ) == false )
            {

                return null;
            }

            return array(
                'accountnumber' => PostHelper::getPostData('accountnumber')
            );
        }

        /**
         * Called when the operation has a post request
         *
         * @param $data
         *
         * @param $ipaddress
         *
         * @param $userid
         *
         * @return bool
         */

        public function onPost($data, $ipaddress, $userid)
        {

            return false;
        }

        /**
         * Account Number
         *
         * @param $accountnumber
         *
         * @param $computerid
         *
         * @param $ipaddress
         */

        private function logCrack( $accountnumber, $computerid, $ipaddress )
        {

            $this->logToComputer('Granted remote admin access to account (' . $accountnumber . ')', $computerid, $ipaddress );
        }

        /**
         * @param $computerid
         * @param $accountnumber
         * @param $ipaddress
         */

        private function logLocal( $computerid, $accountnumber, $ipaddress )
        {

            $this->logToComputer('Granted remote admin access to account (' . $accountnumber . ') at <' . $ipaddress . '>', $computerid, 'localhost' );
        }
    }
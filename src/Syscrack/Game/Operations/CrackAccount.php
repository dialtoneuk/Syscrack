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
    use Framework\Syscrack\Game\AccountDatabase;
    use Framework\Syscrack\Game\BaseClasses\BaseOperation;
    use Framework\Syscrack\Game\Finance;

    class CrackAccount extends BaseOperation
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
                return false;

            if( $this->checkCustomData( $data, ['accountnumber'] ) == false )
                return false;

            if( self::$finance->accountNumberExists( $data['custom']['accountnumber'] ) == false )
                return false;

            if( self::$finance->hasCurrentActiveAccount() )
                if( self::$finance->getCurrentActiveAccount() == $data['custom']['accountnumber'] )
                    return false;

            if( self::$finance->getByAccountNumber( $data['custom']['accountnumber'] )->computerid !== $this->getComputerId( $data['ipaddress'] ) )
                return false;

            if( self::$finance->getByAccountNumber( $data['custom']['accountnumber'] )->userid == $userid )
                return false;

            return true;
        }

        /**
         * @param $timecompleted
         * @param $timestarted
         * @param $computerid
         * @param $userid
         * @param $process
         * @param array $data
         * @return bool
         */

        public function onCompletion($timecompleted, $timestarted, $computerid, $userid, $process, array $data)
        {

            if( $this->checkData( $data, ['ipaddress','custom'] ) == false )
                return false;

            if( self::$internet->ipExists( $data['ipaddress'] ) == false )
                return false;

            if( $this->checkCustomData( $data, ['accountnumber'] ) == false )
                return false;

            self::$finance->setCurrentActiveAccount( $data['custom']['accountnumber'] );
            self::$bankdatabase->addAccountNumber( $data['custom']['accountnumber'], $data['ipaddress'] );
            $this->logCrack( $data['custom']['accountnumber'], $this->getComputerId( $data['ipaddress'] ), self::$computer->getComputer( $computerid )->ipaddress );
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

            return $this->calculateProcessingTime( $computerid, Settings::setting('syscrack_hardware_cpu_type'), Settings::setting('syscrack_operations_hack_speed') );
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
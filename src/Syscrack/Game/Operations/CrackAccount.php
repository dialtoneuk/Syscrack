<?php
    namespace Framework\Syscrack\Game\Operations;

    /**
     * Lewis Lancaster 2017
     *
     * Class CrackAccount
     *
     * @package Framework\Syscrack\Game\Operations
     */

    use Framework\Application\Container;
    use Framework\Application\Session;
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

        protected $finance;

        /**
         * @var AccountDatabase;
         */

        protected $bankdatabase;

        /**
         * CrackAccount constructor.
         */

        public function __construct()
        {

            parent::__construct( true );

            if( isset( $this->finance ) == false )
            {

                $this->finance = new Finance();
            }

            if( isset( $this->bankdatabase ) == false )
            {

                if( Container::hasObject('session') == false )
                {

                    Container::setObject('session', new Session() );
                }

                $this->bankdatabase = new AccountDatabase( Container::getObject('session')->getSessionUser() );
            }
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

            if( $this->checkData( $data, ['ipaddress','custom'] ) == false )
            {

                return false;
            }

            if( $this->checkCustomData( $data, ['accountnumber'] ) == false )
            {

                return false;
            }

            if( $this->finance->accountNumberExists( $data['custom']['accountnumber'] ) == false )
            {

                $this->redirectError('Account does not exist', $this->getRedirect( $data['ipaddress'] ) );
            }

            if( $this->finance->hasCurrentActiveAccount() )
            {

                if( $this->finance->getCurrentActiveAccount() == $data['custom']['accountnumber'] )
                {

                    $this->redirectError('You have already hacked this bank account', $this->getRedirect( $data['ipaddress'] ) );
                }
            }

            if( $this->finance->getByAccountNumber( $data['custom']['accountnumber'] )->computerid !== $this->getComputerId( $data['ipaddress'] ) )
            {

                $this->redirectError('This account does not exist in this banks database', $this->getRedirect( $data['ipaddress'] ) );
            }

            if( $this->finance->getByAccountNumber( $data['custom']['accountnumber'] )->userid == $userid )
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

            if( $this->internet->ipExists( $data['ipaddress'] ) == false )
            {

                $this->redirectError('Sorry, this ip address does not exist anymore', $this->getRedirect() );
            }

            if( $this->checkCustomData( $data, ['accountnumber'] ) == false )
            {

                throw new SyscrackException();
            }

            $this->finance->setCurrentActiveAccount( $data['custom']['accountnumber'] );

            $this->bankdatabase->addAccountNumber( $data['custom']['accountnumber'], $data['ipaddress'] );

            $this->logCrack( $data['custom']['accountnumber'], $this->getComputerId( $data['ipaddress'] ), $this->computers->getComputer( $computerid )->ipaddress );

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
         * Logs a local crack action
         *
         * @param $computerid
         *
         * @param $accountnumber
         */

        private function logLocal( $computerid, $accountnumber, $ipaddress )
        {

            $this->logToComputer('Granted remote admin access to account (' . $accountnumber . ') at <' . $ipaddress . '>', $computerid, 'localhost' );
        }
    }
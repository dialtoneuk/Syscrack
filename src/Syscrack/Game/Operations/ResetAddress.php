<?php
    namespace Framework\Syscrack\Game\Operations;

    /**
     * Lewis Lancaster 2017
     *
     * Class ResetAddress
     *
     * @package Framework\Syscrack\Game\Operations
     */

    use Framework\Application\Settings;
    use Framework\Application\Utilities\PostHelper;
    use Framework\Exceptions\SyscrackException;
    use Framework\Syscrack\Game\BaseClasses\Operation as BaseClass;
    use Framework\Syscrack\Game\Finance;
    use Framework\Syscrack\Game\Structures\Operation as Structure;
    use Framework\Syscrack\Game\Utilities\TimeHelper;

    class ResetAddress extends BaseClass implements Structure
    {

        /**
         * @var Finance
         */

        protected $finance;

        /**
         * ResetAddress constructor.
         */

        public function __construct()
        {

            parent::__construct(true);

            if( isset( $this->finance ) == false )
            {

                $this->finance = new Finance();
            }
        }

        /**
         * @return array
         */

        public function configuration()
        {

            return array(
                'allowlocal'        => false,
                'allowsoftware'    => false,
                'requiresoftware'  => false,
                'requireloggedin'   => false,
                'allowcustomdata'   => true
            );
        }

        /**
         * @param $timecompleted
         * @param $computerid
         * @param $userid
         * @param $process
         * @param array $data
         * @return bool
         */

        public function onCreation($timecompleted, $computerid, $userid, $process, array $data)
        {

            if( $this->checkData( $data, ['ipaddress'] ) == false )
            {

                return false;
            }

            if( $this->checkCustomData( $data, ['accountnumber'] ) == false )
            {

                return false;
            }

            if( $this->internet->getComputer( $data['ipaddress'] )->type != Settings::getSetting('syscrack_computers_isp_type') )
            {

                return false;
            }

            if( $this->finance->accountNumberExists( $data['custom']['accountnumber'] ) == false )
            {

                $this->redirectError('Account does not exist', $this->getRedirect( $data['ipaddress'] ) );
            }

            $account = $this->finance->getByAccountNumber( $data['custom']['accountnumber'] );

            if( $this->finance->canAfford( $account->computerid, $account->userid, Settings::getSetting('syscrack_operations_resetaddress_price') ) == false )
            {

                $this->redirectError('You cannot afford this transaction');
            }

            return true;
        }

        /**
         * @param $timecompleted
         * @param $timestarted
         * @param $computerid
         * @param $userid
         * @param $process
         * @param array $data
         */

        public function onCompletion($timecompleted, $timestarted, $computerid, $userid, $process, array $data)
        {

            if( $this->checkData( $data, ['ipaddress'] ) == false )
            {

                throw new SyscrackException();
            }

            if( $this->checkCustomData( $data, ['accountnumber'] ) == false )
            {

                throw new SyscrackException();
            }

            if( $this->finance->accountNumberExists( $data['custom']['accountnumber'] ) == false )
            {

                $this->redirectError('Account does not exist, maybe it has been deleted?', $this->getRedirect( $data['ipaddress'] ) );
            }

            $account = $this->finance->getByAccountNumber( $data['custom']['accountnumber'] );

            if( $this->finance->canAfford( $account->computerid, $account->userid, Settings::getSetting('syscrack_operations_resetaddress_price') ) == false )
            {

                $this->redirectError('You cannot afford this transaction');
            }

            $this->finance->withdraw( $account->computerid, $account->userid, Settings::getSetting('syscrack_operations_resetaddress_price') );

            $this->internet->changeAddress( $computerid );

            $this->log->updateLog('Changed ip address for ' . Settings::getSetting('syscrack_currency') . number_format( Settings::getSetting('syscrack_operations_resetaddress_price') ). ' using account ' . $account->accountnumber,
                    $this->computers->getCurrentUserComputer(),
                    'localhost');

            $this->redirectSuccess( $this->getRedirect( $data['ipaddress'] ) );
        }

        /**
         * @param $computerid
         * @param $ipaddress
         * @param null $softwareid
         * @return int
         */

        public function getCompletionSpeed($computerid, $ipaddress, $softwareid = null)
        {

            return TimeHelper::getSecondsInFuture( Settings::getSetting('syscrack_operations_resetaddress_time') );
        }

        /**
         * @param $ipaddress
         * @param $userid
         * @return array|null
         */

        public function getCustomData($ipaddress, $userid)
        {

            if( PostHelper::hasPostData() == false )
            {

                return null;
            }

            if( PostHelper::checkForRequirements(['accountnumber'] ) == false )
            {

                return null;
            }

            return array(
                'accountnumber' => PostHelper::getPostData('accountnumber')
            );
        }

        /**
         * @param $data
         * @param $ipaddress
         * @param $userid
         */

        public function onPost($data, $ipaddress, $userid)
        {
            // TODO: Implement onPost() method.
        }
    }
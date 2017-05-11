<?php
    namespace Framework\Syscrack\Game\Operations;

    /**
     * Lewis Lancaster 2017
     *
     * Class CrackAccount
     *
     * @package Framework\Syscrack\Game\Operations
     */

    use Framework\Application\Utilities\PostHelper;
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
         * CrackAccount constructor.
         */

        public function __construct()
        {

            parent::__construct( true );

            if( isset( $this->finance ) == false )
            {

                $this->finance = new Finance();
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
                'allowsoftwares'    => false,
                'allowlocal'        => false,
                'requiresoftwares'  => false,
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

            $computer = $this->internet->getComputer( $data['ipaddress'] );

            if( $this->finance->getByAccountNumber( $data['custom']['accountnumber'] )->computerid !== $computer->computerid )
            {

                return false;
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
            // TODO: Implement onCompletion() method.
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

        public function getCompletionSpeed($computerid, $ipaddress, $softwareid)
        {

            return null;
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
    }
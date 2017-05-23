<?php
    namespace Framework\Syscrack\Game\Operations;

    /**
     * Lewis Lancaster 2017
     *
     * Class Buy
     *
     * @package Framework\Syscrack\Game\Operations
     */

    use Framework\Application\Utilities\PostHelper;
    use Framework\Exceptions\SyscrackException;
    use Framework\Syscrack\Game\BaseClasses\Operation as BaseClass;
    use Framework\Syscrack\Game\Finance;
    use Framework\Syscrack\Game\Market;
    use Framework\Syscrack\Game\Structures\Operation as Structure;
    use Framework\Syscrack\Game\Utilities\TimeHelper;

    class Buy extends BaseClass implements Structure
    {

        /**
         * @var Market
         */

        protected $market;


        /**
         * @var Finance
         */

        protected $finance;

        /**
         * View constructor.
         */

        public function __construct()
        {

            parent::__construct();

            if( isset( $this->market ) == false )
            {

                $this->market = new Market();
            }

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
                'requireloggedin'   => false,
                'allowpost'         => false,
                "allowcustomdata"   => true,
            );
        }

        /**
         * Called on creation
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

            if( $this->checkData( $data, ['ipaddress'] ) == false )
            {

                return false;
            }

            if( $this->checkCustomData( $data, ['itemid','accountnumber' ] ) == false )
            {

                return false;
            }

            if( $this->finance->accountNumberExists( $data['custom']['accountnumber'] ) == false )
            {

                return false;
            }

            $computer = $this->internet->getComputer( $data['ipaddress'] );

            if( $this->computers->isMarket( $computer->computerid ) == false )
            {

                return false;
            }

            if( $this->market->checkMarket( $computer->computerid ) == false )
            {

                return false;
            }

            if( $this->market->hasStockItem( $computer->computerid, $data['custom']['itemid'] ) == false )
            {

                $this->redirectError('Sorry, this stock item does not exist', $this->getRedirect( $data['ipaddress'] ) . '/market' );
            }

            $item = $this->market->getStockItem( $computer->computerid, $data['custom']['itemid'] );

            $account = $this->finance->getByAccountNumber( $data['custom']['accountnumber'] );

            if( $this->finance->canAfford( $account->computerid, $userid, $item['price'] ) == false )
            {

                $this->redirectError('Sorry, you cannot afford this purchase', $this->getRedirect( $data['ipaddress'] ) . '/market' );
            }

            return true;
        }

        /**
         * When we complete a purchase
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

            if( $this->checkData( $data, ['ipaddress'] ) == false )
            {

                throw new SyscrackException();
            }

            if( $this->internet->ipExists( $data['ipaddress'] ) == false )
            {

                $this->redirectError('Sorry, this ip address does not exist anymore', $this->getRedirect() );
            }

            if( $this->checkCustomData( $data, ['itemid','accountnumber' ] ) == false )
            {

                throw new SyscrackException();
            }

            if( $this->finance->accountNumberExists( $data['custom']['accountnumber'] ) == false )
            {

                $this->redirectError('Account does not exist, maybe it has been deleted?', $this->getRedirect( $data['ipaddress'] ) );
            }

            $account = $this->finance->getByAccountNumber( $data['custom']['accountnumber'] );

            $item = $this->market->getStockItem( $this->getComputerId( $data['ipaddress'] ), $data['custom']['itemid'] );

            if( $this->finance->canAfford( $account->computerid, $account->userid, $item['price'] ) == false )
            {

                $this->redirectError('You cannot afford this transaction');
            }

            if( isset( $item['hardware'] ) == false )
            {

                throw new SyscrackException();
            }

            if( $this->hardware->hasHardwareType( $computerid, $item['hardware'] ) )
            {

                $this->hardware->updateHardware( $computerid, $item['hardware'], $item['value'] );
            }
            else
            {

                $this->hardware->addHardware( $computerid, $item['hardware'], $item['value'] );
            }

            $this->finance->withdraw( $account->computerid, $userid, $item['price'] );

            $this->market->addPurchase( $this->getComputerId( $data['ipaddress'] ), $computerid, $data['custom']['itemid'] );

            $this->logLocal( $computerid, $data['custom']['accountnumber'], $data['ipaddress'] );

            $this->redirectSuccess( $this->getRedirect( $data['ipaddress'] ) . '/market' );
        }

        /**
         * Gets the completion speed
         *
         * @param $computerid
         *
         * @param $ipaddress
         *
         * @param null $softwareid
         *
         * @return null
         */

        public function getCompletionSpeed($computerid, $ipaddress, $softwareid = null)
        {

            return TimeHelper::getSecondsInFuture( 3 );
        }

        /**
         * Gets the custom data for this operation
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

            if( PostHelper::checkForRequirements(['accountnumber','itemid'] ) == false )
            {

                return null;
            }

            return array(
                'accountnumber' => PostHelper::getPostData('accountnumber'),
                'itemid'        => PostHelper::getPostData('itemid')
            );
        }

        /**
         * What to do when this operation recieves a post request
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

            return true;
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

            $this->logToComputer('Successfully initiated online payment using account (' . $accountnumber . ') to server <' . $ipaddress . '>', $computerid, 'localhost' );
        }
    }
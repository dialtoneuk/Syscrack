<?php
    namespace Framework\Syscrack\Game\Computers;

    /**
     * Lewis Lancaster 2017
     *
     * Class Vpc
     *
     * @package Framework\Syscrack\Game\Computer
     */

    use Framework\Application\Settings;
    use Framework\Exceptions\SyscrackException;
    use Framework\Syscrack\Game\AddressDatabase;
    use Framework\Syscrack\Game\AccountDatabase;
    use Framework\Syscrack\Game\BaseClasses\Computer as BaseClass;
    use Framework\Syscrack\Game\Structures\Computer as Structure;

    class Vpc extends BaseClass implements Structure
    {

        /**
         * @var AddressDatabase
         */

        protected $addressdatabase;

        /**
         * @var AccountDatabase
         */

        protected $accountdatabase;

        /**
         * Vpc constructor.
         */

        public function __construct()
        {

            if( isset( $this->addressdatabase ) == false )
                $this->addressdatabase = new AddressDatabase();


            if( isset( $this->accountdatabase ) == false )
                $this->accountdatabase = new AccountDatabase();

            parent::__construct( true );
        }

        /**
         * The configuration
         *
         * @return array
         */

        public function configuration()
        {

            return array(
                'installable'   => true,
                'type'          => 'vpc'
            );
        }

        /**
         * @param $computerid
         * @param $userid
         * @param array $software
         * @param array $hardware
         * @param array $custom
         */

        public function onStartup($computerid, $userid, array $software = [], array $hardware = [], array $custom = [])
        {

            if( $this->addressdatabase->hasDatabase( $userid ) == false )
                $this->addressdatabase->saveDatabase( $userid );

            if( $this->accountdatabase->hasDatabase( $userid ) == false )
                $this->accountdatabase->saveDatabase( $userid, [] );

            parent::onStartup( $computerid, $userid, $software, $hardware, $custom );
        }

        /**
         * What to do on reset
         *
         * @param $computerid
         */

        public function onReset($computerid)
        {

            $userid = $this->computer->getComputer( $computerid )->userid;

            if( $this->addressdatabase->hasDatabase( $userid ) == false )
                $this->addressdatabase->saveDatabase( $userid, [] );

            if( $this->accountdatabase->hasDatabase( $userid ) == false )
                $this->accountdatabase->saveDatabase( $userid, [] );

            parent::onReset( $computerid );
        }

        /**
         * What to do on login
         *
         * @param $computerid
         *
         * @param $ipaddress
         */

        public function onLogin($computerid, $ipaddress)
        {

            if( $this->internet->ipExists( $ipaddress ) == false )
                throw new SyscrackException();

            $this->internet->setCurrentConnectedAddress( $ipaddress );

            $this->log( $computerid, 'Logged in as root', $this->getCurrentComputerAddress() );
            $this->logToIP( $this->getCurrentComputerAddress(), 'Logged in as root at <' . $ipaddress . '>');
        }

        /**
         * What do on logout
         *
         * @param $computerid
         *
         * @param $ipaddress
         */

        public function onLogout($computerid, $ipaddress)
        {

            if( $this->internet->ipExists( $ipaddress ) == false )
                throw new SyscrackException();

            $this->internet->setCurrentConnectedAddress( null );
        }
    }
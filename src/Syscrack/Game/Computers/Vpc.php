<?php
    namespace Framework\Syscrack\Game\Computers;

    /**
     * Lewis Lancaster 2017
     *
     * Class Vpc
     *
     * @package Framework\Syscrack\Game\Computers
     */

    use Framework\Application\Settings;
    use Framework\Exceptions\SyscrackException;
    use Framework\Syscrack\Game\AddressDatabase;
    use Framework\Syscrack\Game\BankDatabase;
    use Framework\Syscrack\Game\BaseClasses\Computer as BaseClass;
    use Framework\Syscrack\Game\Structures\Computer as Structure;

    class Vpc extends BaseClass implements Structure
    {

        /**
         * @var AddressDatabase
         */

        protected $addressdatabase;

        /**
         * @var BankDatabase
         */

        protected $bankdatabase;

        /**
         * Vpc constructor.
         */

        public function __construct()
        {

            parent::__construct();

            if( isset( $this->addressdatabase ) == false )
            {

                $this->addressdatabase = new AddressDatabase();
            }

            if( isset( $this->bankdatabase ) == false )
            {

                $this->bankdatabase = new BankDatabase();
            }
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
         * What to do on startup
         *
         * @param $computerid
         *
         * @param $userid
         *
         * @param array $softwares
         *
         * @param array $hardwares
         */

        public function onStartup($computerid, $userid, array $softwares = [], array $hardwares = [])
        {

            if( $this->log->hasLog( $computerid ) == false )
            {

                $this->log->createLog( $computerid );
            }

            if( $this->addressdatabase->hasDatabase( $userid ) == false )
            {

                $this->addressdatabase->saveDatabase( $userid );
            }

            if( $this->bankdatabase->hasDatabase( $userid ) == false )
            {

                $this->bankdatabase->saveDatabase( $userid, [] );
            }

            if( empty( $softwares ) == false )
            {

                $this->addSoftwares( $computerid, $userid, $softwares );
            }

            if( empty( $hardwares ) == false )
            {

                $this->addHardwares( $computerid, $hardwares );
            }
        }

        /**
         * What to do on reset
         *
         * @param $computerid
         */

        public function onReset($computerid)
        {

            $this->clearSoftwares( $computerid );

            $this->computers->resetHardware( $computerid );

            $this->addHardwares( $computerid, Settings::getSetting('syscrack_default_hardware') );
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
            {

                throw new SyscrackException();
            }

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
            {

                throw new SyscrackException();
            }

            $this->internet->setCurrentConnectedAddress( null );
        }
    }
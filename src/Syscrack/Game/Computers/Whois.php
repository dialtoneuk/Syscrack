<?php
    namespace Framework\Syscrack\Game\Computers;

    /**
     * Lewis Lancaster 2017
     *
     * Class Whois
     *
     * @package Framework\Syscrack\Game\Computers
     */

    use Framework\Application\Settings;
    use Framework\Exceptions\SyscrackException;
    use Framework\Syscrack\Game\BaseClasses\Computer as BaseClass;
    use Framework\Syscrack\Game\Schema;
    use Framework\Syscrack\Game\Structures\Computer as Structure;

    class Whois extends BaseClass implements Structure
    {

        /**
         * @var Schema
         */

        protected $schema;

        /**
         * Npc constructor.
         */

        public function __construct()
        {

            parent::__construct( true );

            if( isset( $this->schema ) == false )
            {

                $this->schema = new Schema();
            }
        }

        /**
         * The configuration of this computer
         *
         * @return array
         */

        public function configuration()
        {

            return array(
                'installable' => false,
                'type'        => 'whois'
            );
        }

        /**
         * What to do when this computer resets
         *
         * @param $computerid
         */

        public function onReset( $computerid )
        {

            $userid = $this->computers->getComputer( $computerid )->userid;

            if( $this->schema->hasSchema( $computerid ) == false )
            {

                $this->clearSoftwares( $computerid );

                $this->computers->resetHardware( $computerid );

                $this->addHardwares( $computerid, Settings::getSetting('syscrack_default_hardware') );
            }
            else
            {

                $schema = $this->schema->getSchema( $computerid );

                if( empty( $schema['softwares'] ) || empty( $schema['hardwares'] ) )
                {

                    throw new SyscrackException();
                }

                $this->clearSoftwares( $computerid );

                $this->computers->resetHardware( $computerid );

                $this->addSoftwares( $computerid, $userid, $schema['softwares'] );

                $this->addHardwares( $computerid, $schema['hardwares'] );
            }
        }

        /**
         * What to do when this computer starts up
         *
         * @param $computerid
         *
         * @param $userid
         *
         * @param array $softwares
         *
         * @param array $hardwares
         */

        public function onStartup($computerid, $userid, array $softwares = [], array $hardwares = [] )
        {

            if( $this->log->hasLog( $computerid ) == false )
            {

                $this->log->createLog( $computerid );
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
         * What to do when you login to this computer
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
         * What to do when you logout of a computer
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
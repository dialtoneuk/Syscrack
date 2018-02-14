<?php
    namespace Framework\Syscrack\Game\Computers;

    /**
     * Lewis Lancaster 2017
     *
     * Class Market
     *
     * @package Framework\Syscrack\Game\Computers
     */

    use Framework\Application\Settings;
    use Framework\Application\Utilities\FileSystem;
    use Framework\Exceptions\SyscrackException;
    use Framework\Syscrack\Game\BaseClasses\Computer as BaseClass;
    use Framework\Syscrack\Game\Schema;
    use Framework\Syscrack\Game\Structures\Computer as Structure;

    class Market extends BaseClass implements Structure
    {

        /**
         * @var Schema
         */

        protected $schema;

        /**
         * @var \Framework\Syscrack\Game\Market
         */

        protected $market;

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

            if( isset( $this->market ) == false )
            {

                $this->market = new \Framework\Syscrack\Game\Market();
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
                'type'        => 'bank'
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

                    $schema['softwares'] = [];
                    $schema['hardwares'] = Settings::getSetting('syscrack_default_hardware');
                }

                $this->clearSoftwares( $computerid );

                $this->computers->resetHardware( $computerid );

                $this->addSoftwares( $computerid, $userid, $schema['softwares'] );

                $this->addHardwares( $computerid, $schema['hardwares'] );
            }

            if( empty( $this->market->getPurchases( $computerid ) ) == false )
            {

                $this->market->save( $computerid, [] );
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

            if( FileSystem::directoryExists( $this->market->getFilePath( $computerid ) ) == false )
            {

                FileSystem::createDirectory( $this->market->getFilePath( $computerid ) );
            }

            if( $this->market->hasStock( $computerid ) == false )
            {

                $this->market->save( $computerid, [], 'stock.json');
            }

            if( empty( $this->market->getPurchases( $computerid ) ) )
            {

                $this->market->save( $computerid, [] );
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
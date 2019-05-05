<?php

    namespace Framework\Syscrack\Game\BaseClasses;

    /**
     * Lewis Lancaster 2017
     *
     * Class Computer
     *
     * @package Framework\Syscrack\Game\BaseClasses
     */

    use Framework\Application\Settings;
    use Framework\Exceptions\SyscrackException;
    use Framework\Syscrack\Game\Computer as ComputerController;
    use Framework\Syscrack\Game\Internet;
    use Framework\Syscrack\Game\Log;
    use Framework\Syscrack\Game\Metadata;
    use Framework\Syscrack\Game\Software;
    use Framework\Application\UtilitiesV2\Conventions\ComputerData;

    class Computer
    {

        /**
         * @var ComputerController
         */

        protected $computer;

        /**
         * @var Software
         */

        protected $software;

        /**
         * @var Internet
         */

        protected $internet;

        /**
         * @var Log
         */

        protected $log;

        /**
         * @var Metadata
         */

        protected static $metadata;

        /**
         * Computer constructor.
         * @param bool $createclasses
         */

        public function __construct( $createclasses = true )
        {

            if( isset( self::$metadata ) == false )
                self::$metadata = new Metadata();

            if( $createclasses == true )
            {

                $this->computer = new ComputerController();
                $this->software = new Software();
                $this->internet = new Internet();
                $this->log = new Log();
            }
        }

        /**
         * @return Metadata
         */

        public function metadata() : Metadata
        {

            return( self::$metadata );
        }

        /**
         * @param $computerid
         * @param ComputerData $metadata
         */

        public function reload($computerid, ComputerData $metadata )
        {

            $this->addHardwares( $computerid, $metadata->hardware );
            $this->addSoftware( $computerid, null, $metadata->software );

            $array = $metadata->info;
            $array["reset"] = microtime( true );
            $this->metadata()->update( $computerid, array("info" => $array ) );
        }


        /**
         * Adds the software
         *
         * @param $computerid
         *
         * @param int $userid
         *
         * @param array $software
         */

        public function addSoftware( $computerid, $userid=null, array $software=[] )
        {

            if( $userid == null )
                $userid = Settings::getSetting("syscrack_master_user");

            foreach( $software as $softwares )
            {

                if ( isset( $software['uniquename'] ) == false )
                    continue;

                $class = $this->software->findSoftwareByUniqueName( $software['uniquename'] );

                if( $class == null )
                    continue;

                $name = $this->software->getNameFromClass( $class );

                if( isset( $software['data'] ) == false )
                    $software['data'] = [];

                $softwareid = $this->software->createSoftware(
                    $name,
                    $userid,
                    $computerid,
                    $software['name'],
                    $software['level'],
                    $software['size'],
                    $software['data'] );

                $this->computer->addSoftware(
                    $computerid,
                    $softwareid,
                    $this->software->getSoftwareType( $name )
                );


                if( isset( $software['installed'] ) && $software['installed'] )
                {

                    $this->computer->installSoftware( $computerid, $softwareid );
                    $this->software->installSoftware( $softwareid, $userid );
                }
            }
        }

        /**
         * @param $computerid
         */

        public function onReset($computerid)
        {

            $this->clearSoftware( $computerid );
            $this->computer->resetHardware( $computerid );

            if( $this->log->hasLog( $computerid ) )
                $this->log->saveLog( $computerid, [] );

            if( $this->metadata()->exists( $computerid ) )
                $this->reload( $computerid, $this->metadata()->get( $computerid ) );
            else
                $this->addHardwares( $computerid, Settings::getSetting('syscrack_default_hardware' ) );
        }

        /**
         * @param $computerid
         * @param $userid
         * @param array $software
         * @param array $hardware
         */

        public function onStartup($computerid, $userid, array $software = [], array $hardware = [] )
        {

            if( $this->log->hasLog( $computerid ) == false )
                $this->log->createLog( $computerid );

            $this->addSoftware( $computerid, $userid, $software );
            $this->addHardwares( $computerid, $hardware );

            $this->metadata()->create( $computerid, Metadata::generateData( "Computer_" . $computerid, $this->configuration()["type"], $software, $hardware, [] ) );
        }

        /**
         * Clears the software
         *
         * @param $computerid
         */

        public function clearSoftware( $computerid )
        {

            $software = $this->computer->getComputerSoftware( $computerid );

            foreach( $software as $softwares )
            {
                if( $this->software->softwareExists( $software['softwareid'] ) )
                    $this->software->deleteSoftware( $software['softwareid'] );

                $this->computer->removeSoftware( $computerid, $software['softwareid'] );
            }
        }

        /**
         * Sets the computer hardware
         *
         * @param $computerid
         *
         * @param array $hardware
         */

        public function setHardwares( $computerid, array $hardware )
        {

            $this->computer->setHardware( $computerid, $hardware );
        }

        /**
         * Adds a hardware to the computer
         *
         * @param $computerid
         *
         * @param array $hardware
         */

        public function addHardwares( $computerid, array $hardware )
        {

            $hardware = $this->computer->getComputerHardware( $computerid );

            foreach( $hardware as $item=>$value )
            {

                if( isset( $hardware[ $item ] ) )
                    continue;

                $hardware[ $item ] = $value;
            }

            $this->setHardwares( $computerid, $hardware );
        }

        /**
         * @param $uniquename
         * @return \Framework\Syscrack\Game\Structures\Software
         */

        public function getSoftwareClass( $uniquename )
        {

            return $this->software->findSoftwareByUniqueName( $uniquename );
        }

        /**
         * @param $computerid
         * @param $message
         * @param $ipaddress
         */

        public function log( $computerid, $message, $ipaddress )
        {

            $this->log->updateLog( $message, $computerid, $ipaddress );
        }

        /**
         * @param $ipaddress
         * @param $message
         */

        public function logToIP( $ipaddress, $message )
        {

            $computer = $this->internet->getComputer( $ipaddress );

            if( $computer == null )
                throw new SyscrackException();

            $this->log( $computer->computerid, $message, Settings::getSetting('syscrack_log_localhost_address') );
        }

        /**
         * @return mixed
         */

        public function getCurrentComputerAddress()
        {

            return $this->computer->getComputer( $this->computer->getCurrentUserComputer() )->ipaddress;
        }

        /**
         * @param $computerid
         * @return mixed
         */

        public function getComputerOwner( $computerid )
        {

            return $this->computer->getComputer( $computerid )->userid;
        }
    }
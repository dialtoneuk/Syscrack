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
    use Framework\Syscrack\Game\Computers;
    use Framework\Syscrack\Game\Internet;
    use Framework\Syscrack\Game\Log;
    use Framework\Syscrack\Game\Softwares;

    class Computer
    {

        protected $computers;

        protected $softwares;

        protected $internet;

        protected $log;

        public function __construct( $createclasses = true )
        {

            if( $createclasses == true )
            {

                $this->computers = new Computers();

                $this->softwares = new Softwares();

                $this->internet = new Internet();

                $this->log = new Log();
            }
        }

        /**
         * Adds the softwares
         *
         * @param $computerid
         *
         * @param int $userid
         *
         * @param array $softwares
         */

        public function addSoftwares( $computerid, $userid=1, array $softwares )
        {

            foreach( $softwares as $software )
            {

                $class = $this->softwares->findSoftwareByUniqueName( $software['uniquename'] );

                if( $class == null )
                {

                    continue;
                }

                $name = $this->softwares->getNameFromClass( $class );

                if( isset( $software['data'] ) == false )
                {

                    $software['data'] = [];
                }

                $softwareid = $this->softwares->createSoftware( $name, $userid, $computerid, $software['name'], $software['level'], $software['size'], $software['data'] );

                if( $this->softwares->softwareExists( $softwareid ) == false )
                {

                    throw new SyscrackException();
                }

                $this->computers->addSoftware( $computerid, $softwareid, $this->softwares->getSoftwareType( $name ) );

                if( isset( $software['installed'] ) )
                {

                    if( $software['installed'] == true )
                    {

                        $this->computers->installSoftware( $computerid, $softwareid );

                        $this->softwares->installSoftware( $softwareid, $userid );
                    }
                }
            }
        }

        /**
         * Clears the softwares
         *
         * @param $computerid
         */

        public function clearSoftwares( $computerid )
        {

            $softwares = $this->computers->getComputerSoftware( $computerid );

            foreach( $softwares as $software )
            {

                if( $this->softwares->softwareExists( $software['softwareid'] ) )
                {

                    $this->softwares->deleteSoftware( $software['softwareid'] );
                }

                $this->computers->removeSoftware( $computerid, $software['softwareid'] );
            }
        }

        /**
         * Sets the computers hardware
         *
         * @param $computerid
         *
         * @param array $hardwares
         */

        public function setHardwares( $computerid, array $hardwares )
        {

            $this->computers->setHardware( $computerid, $hardwares );
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

            $hardwares = $this->computers->getComputerHardware( $computerid );

            foreach( $hardware as $item=>$value )
            {

                if( isset( $hardwares[ $item ] ) )
                {

                    continue;
                }

                $hardware[ $item ] = $value;
            }

            $this->setHardwares( $computerid, $hardwares );
        }

        public function getSoftwareClass( $uniquename )
        {

            return $this->softwares->findSoftwareByUniqueName( $uniquename );
        }

        public function log( $computerid, $message, $ipaddress )
        {

            $this->log->updateLog( $message, $computerid, $ipaddress );
        }

        public function logToIP( $ipaddress, $message )
        {

            $computer = $this->internet->getComputer( $ipaddress );

            if( $computer == null )
            {

                throw new SyscrackException();
            }

            $this->log( $computer->computerid, $message, Settings::getSetting('syscrack_log_localhost_address') );
        }

        public function getCurrentComputerAddress()
        {

            return $this->computers->getComputer( $this->computers->getCurrentUserComputer() )->ipaddress;
        }

        public function getComputerOwner( $computerid )
        {

            return $this->computers->getComputer( $computerid )->userid;
        }
    }
<?php
    namespace Framework\Syscrack\Game\Operations;

    /**
     * Lewis Lancaster 2017
     *
     * Class Uninstall
     *
     * @package Framework\Syscrack\Game\Operations
     */

    use Framework\Application\Settings;
    use Framework\Exceptions\SyscrackException;
    use Framework\Syscrack\Game\BaseClasses\Operation as BaseClass;
    use Framework\Syscrack\Game\Structures\Operation as Structure;
    use Framework\Syscrack\Game\Viruses;

    class Uninstall extends BaseClass implements Structure
    {

        /**
         * @var Viruses
         */

        protected $viruses;

        /**
         * Uninstall constructor.
         */

        public function __construct()
        {

            parent::__construct();

            $this->viruses = new Viruses();
        }

        /**
         * The configuration for this operation
         *
         * @return array
         */

        public function configuration()
        {

            return parent::configuration();
        }

        /**
         * Called when the operation is created
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

            if( $this->checkData( $data ) == false )
            {

                return false;
            }

            if( $this->softwares->softwareExists( $data['softwareid'] ) == false )
            {

                return false;
            }
            else
            {

                if( $this->computer->hasSoftware( $this->internet->getComputer( $data['ipaddress'] )->computerid, $data['softwareid'] ) == false )
                {

                    return false;
                }
                else
                {

                    if( $this->softwares->canInstall( $data['softwareid'] ) == false )
                    {

                        return false;
                    }
                    else
                    {

                        if( $this->viruses->isVirus( $data['softwareid'] ) )
                        {

                            return false;
                        }
                    }

                    return true;
                }
            }
        }

        /**
         * Called when the operation is completed
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

            if( $this->checkData( $data ) == false )
            {

                throw new SyscrackException();
            }

            $this->softwares->uninstallSoftware( $data['softwareid'] );

            $this->computer->uninstallSoftware( $this->internet->getComputer( $data['ipaddress'] )->computerid, $data['softwareid'] );

            $this->logInstall( $this->softwares->getSoftware( $data['softwareid'] )->softwarename,
                $this->internet->getComputer( $data['ipaddress'] )->computerid,$this->computer->getComputer( $this->computer->getCurrentUserComputer() )->ipaddress );

            $this->logLocal( $this->softwares->getSoftware( $data['softwareid'] )->softwarename,
                $this->computer->getCurrentUserComputer(), $data['ipaddress']);

            $this->softwares->executeSoftwareMethod( $this->softwares->getSoftwareNameFromSoftwareID( $data['softwareid'] ), 'onUninstalled', array(
                'softwareid'    => $data['softwareid'],
                'userid'        => $userid,
                'computerid'    => $this->internet->getComputer( $data['ipaddress'] )->computerid
            ));

            if( isset( $data['redirect'] ) )
            {

                $this->redirectSuccess( null , $data['redirect'] );
            }
            else
            {

                $this->redirectSuccess( $data['ipaddress'] );
            }
        }

        /**
         * Gets the completion speed of this operation
         *
         * @param $computerid
         *
         * @param $process
         *
         * @param null $softwareid
         *
         * @return int
         */

        public function getCompletionSpeed($computerid, $process, $softwareid=null)
        {

            return $this->calculateProcessingTime( $computerid, Settings::getSetting('syscrack_cpu_type'), 5.5, $softwareid );
        }

        /**
         * Logs a login action to the computers log
         *
         * @param $computerid
         *
         * @param $ipaddress
         */

        private function logInstall( $softwarename, $computerid, $ipaddress )
        {

            if( $this->computer->getCurrentUserComputer() == $computerid )
            {

                return;
            }

            $this->log('Unintalled file <' . $softwarename . '> on root', $computerid, $ipaddress );
        }

        /**
         * Logs to the local log
         *
         * @param $softwarename
         *
         * @param $ipaddress
         */

        private function logLocal( $softwarename, $computerid, $ipaddress )
        {

            $this->log('Uninstalled file <' . $softwarename . '> on ' . $ipaddress, $computerid, 'localhost' );
        }
    }
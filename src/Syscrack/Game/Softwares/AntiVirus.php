<?php
    namespace Framework\Syscrack\Game\Softwares;

    /**
     * Lewis Lancaster 2017
     *
     * Class AntiVirus
     *
     * @package Framework\Syscrack\Game\Softwares
     */

    use Framework\Syscrack\Game\BaseClasses\Software as BaseClass;
    use Framework\Syscrack\Game\Structures\Software as Structure;
    use Framework\Syscrack\Game\Utilities\TimeHelper;
    use Framework\Syscrack\Game\Viruses;

    class AntiVirus extends BaseClass implements Structure
    {

        protected $viruses;

        public function __construct()
        {

            parent::__construct(true);

            if( isset( $this->viruses ) == false )
            {

                $this->viruses = new Viruses();
            }
        }

        /**
         * The configuration of this Structure
         *
         * @return array
         */

        public function configuration()
        {

            return array(
                'uniquename'    => 'antivirus',
                'extension'     => '.av',
                'type'          => 'exe',
                'installable'   => true,
                'executable'    => true
            );
        }

        public function onExecuted( $softwareid, $userid, $computerid )
        {

            $viruses = $this->viruses->getVirusesOnComputer( $computerid );

            if( empty( $viruses ) )
            {

                $this->redirectError('No viruses were found', $this->getRedirect( $this->computer->getComputer( $computerid )->ipaddress ) );
            }

            $software = $this->softwares->getSoftware( $softwareid );

            $results = [];

            foreach( $viruses as $virus )
            {

                if( $virus->level > $software->level )
                {

                    continue;
                }

                if( $virus->installed == false )
                {

                    continue;
                }

                $results[] = array(
                    'softwareid' => $virus->softwareid
                );

                $this->softwares->deleteSoftware( $virus->softwareid );

                $this->computer->removeSoftware( $computerid, $virus->softwareid );
            }

            if( empty( $results ) )
            {

                $this->redirectError('No errors were deleted, this could be due to your anti-virus being too weak',  $this->getRedirect( $this->computer->getComputer( $computerid )->ipaddress ) );
            }

            $this->redirectSuccess( $this->getRedirect( $this->computer->getComputer( $computerid )->ipaddress ) );
        }

        public function onInstalled( $softwareid, $userid, $computerid )
        {

            return null;
        }

        public function onUninstalled($softwareid, $userid, $computerid)
        {

            return null;
        }

        public function onCollect( $softwareid, $userid, $computerid, $timeran )
        {

            return null;
        }

        public function getExecuteCompletionTime($softwareid, $computerid)
        {

            return TimeHelper::getSecondsInFuture( 1 );
        }

        /**
         * Default size of 10.0
         *
         * @return float
         */

        public function getDefaultSize()
        {

            return 10.0;
        }

        /**
         * Default level of 1.0
         *
         * @return float
         */

        public function getDefaultLevel()
        {

            return 1.0;
        }
    }
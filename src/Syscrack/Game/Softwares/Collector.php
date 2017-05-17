<?php
    namespace Framework\Syscrack\Game\Softwares;

    /**
     * Lewis Lancaster 2017
     *
     * Class Honeypot
     *
     * @package Framework\Syscrack\Game\Collector
     */

    use Framework\Syscrack\Game\BaseClasses\Software as BaseClass;
    use Framework\Syscrack\Game\Structures\Software as Structure;

    class Collector extends BaseClass implements Structure
    {

        /**
         * Collector constructor.
         */

        public function __construct()
        {

            parent::__construct();
        }

        /**
         * The configuration of this Structure
         *
         * @return array
         */

        public function configuration()
        {

            return array(
                'uniquename'        => 'collector',
                'extension'         => '.col',
                'type'              => 'collector',
                'installable'       => true,
                'executable'        => true,
                'localexecuteonly'  => true,
            );
        }

        /**
         * Collects the users viruses
         *
         * @param $softwareid
         *
         * @param $userid
         *
         * @param $computerid
         *
         * @return array|bool
         */

        public function onExecuted( $softwareid, $userid, $computerid )
        {

            $this->redirect('computer/collect');

            return true;
        }

        public function onInstalled( $softwareid, $userid, $computerid )
        {

            return;
        }

        public function onUninstalled($softwareid, $userid, $computerid)
        {
            // TODO: Implement onUninstalled() method.
        }

        public function onCollect( $softwareid, $userid, $computerid, $timeran )
        {

            return;
        }

        public function getExecuteCompletionTime($softwareid, $computerid)
        {
            return null;
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
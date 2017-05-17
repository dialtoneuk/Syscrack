<?php
    namespace Framework\Syscrack\Game\Softwares;

    /**
     * Lewis Lancaster 2017
     *
     * Class Cracker
     *
     * @package Framework\Syscrack\Game\Softwares
     */

    use Framework\Syscrack\Game\BaseClasses\Software as BaseClass;
    use Framework\Syscrack\Game\Structures\Software as Structure;

    class Cracker extends BaseClass implements Structure
    {

        /**
         * The configuration of this Structure
         *
         * @return array
         */

        public function configuration()
        {

            return array(
                'uniquename'    => 'cracker',
                'extension'     => '.crc',
                'type'          => 'cracker',
                'installable'   => true,
                'executable'    => true
            );
        }

        public function onExecuted( $softwareid, $userid, $computerid )
        {

            $this->redirect('game/internet');
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
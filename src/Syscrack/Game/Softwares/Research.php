<?php
    namespace Framework\Syscrack\Game\Softwares;

    /**
     * Lewis Lancaster 2017
     *
     * Class Research
     *
     * @package Framework\Syscrack\Game\Softwares
     */

    use Framework\Syscrack\Game\BaseClasses\Software as BaseClass;
    use Framework\Syscrack\Game\Structures\Software as Structure;

    class Research extends BaseClass implements Structure
    {

        /**
         * The configuration of this Structure
         *
         * @return array
         */

        public function configuration()
        {

            return array(
                'uniquename'        => 'research',
                'extension'         => '.rsch',
                'type'              => 'research',
                'viewable'          => false,
                'removable'         => true,
                'installable'       => true,
                'executable'        => true,
                'localexecuteonly'  => true,
                'keepdata'          => false,
                'icon'              => 'glyphicon-apple'
            );
        }

        public function onExecuted( $softwareid, $userid, $computerid )
        {

            $this->redirect('computer/actions/research');

            return true;
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
         * Default size of 16.0
         *
         * @return float
         */

        public function getDefaultSize()
        {

            return 16.0;
        }

        /**
         * Default level of 2.2
         *
         * @return float
         */

        public function getDefaultLevel()
        {

            return 2.2;
        }
    }
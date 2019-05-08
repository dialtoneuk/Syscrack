<?php
    namespace Framework\Syscrack\Game\Softwares;

    /**
     * Lewis Lancaster 2017
     *
     * Class Research
     *
     * @package Framework\Syscrack\Game\Softwares
     */

    use Framework\Syscrack\Game\BaseClasses\BaseSoftware;


    class Research extends BaseSoftware
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

            if( $computerid !== $this->currentComputer()->computerid )
                $this->redirectError("Sorry, this must be executed locally", $this->currentAddress() );
            else
                $this->redirect('computer/research');

            return true;
        }
    }
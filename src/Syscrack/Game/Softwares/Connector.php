<?php
    namespace Framework\Syscrack\Game\Softwares;

    /**
     * Lewis Lancaster 2017
     *
     * Class Connector
     *
     * @package Framework\Syscrack\Game\Softwares
     */

    use Framework\Syscrack\Game\BaseClasses\BaseSoftware;
    use Framework\Syscrack\Game\Tool;

    class Connector extends BaseSoftware
    {

        /**
         * The configuration of this Structure
         *
         * @return array
         */

        public function configuration()
        {

            return array(
                'uniquename'        => 'connector',
                'extension'         => '.con',
                'type'              => 'connector',
                'installable'       => true,
                'executable'        => true,
                'localexecuteonly'  => true,
            );
        }

        /**
         * @param null $userid
         * @param null $sofwareid
         * @param null $computerid
         * @return Tool
         */

        public function tool($userid = null, $sofwareid = null, $computerid = null): Tool
        {

            $tool = new Tool("Login", "success");
            $tool->hasSoftwareInstalled('connector');
            $tool->unhacked();
            $tool->setAction('login');
            $tool->hide();

            return( $tool );
        }
    }
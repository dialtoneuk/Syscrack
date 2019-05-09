<?php
    namespace Framework\Syscrack\Game\Softwares;

    /**
     * Lewis Lancaster 2017
     *
     * Class ForceLogin
     *
     * @package Framework\Syscrack\Game\Softwares
     */

    use Framework\Syscrack\Game\BaseClasses\BaseSoftware;
    use Framework\Syscrack\Game\Tool;

    class ForceLogin extends BaseSoftware
    {

        /**
         * The configuration of this Structure
         *
         * @return array
         */

        public function configuration()
        {

            return array(
                'uniquename'        => 'forcelogin',
                'extension'         => '.admin',
                'type'              => 'admin',
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

            $tool = new Tool("Force Login", "success");
            $tool->admin();
            $tool->setAction('forcelogin');
            $tool->icon = "info-sign";

            return( $tool );
        }
    }
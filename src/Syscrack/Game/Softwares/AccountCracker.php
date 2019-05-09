<?php
    namespace Framework\Syscrack\Game\Softwares;

    /**
     * Lewis Lancaster 2017
     *
     * Class AccountCracker
     *
     * @package Framework\Syscrack\Game\Softwares
     */

    use Framework\Syscrack\Game\BaseClasses\BaseSoftware;
    use Framework\Syscrack\Game\Tool;

    class AccountCracker extends BaseSoftware
    {

        /**
         * The configuration of this Structure
         *
         * @return array
         */

        public function configuration()
        {

            return array(
                'uniquename'        => 'accountcracker',
                'extension'         => '.acc',
                'type'              => 'accountcracker',
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

            $tool = new Tool("Hack account", "warning");
            $tool->hasSoftwareInstalled('accountcracker');
            $tool->setAction('crackaccount');
            $tool->addInput('accountnumber', 'text', "", "#0123456789");
            $tool->isComputerType('bank');
            $tool->isConnected();
            $tool->icon = "eye-open";

            return( $tool );
        }
    }
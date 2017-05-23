<?php
    namespace Framework\Syscrack\Game\Structures;

    /**
     * Lewis Lancaster 2017
     *
     * Interface Computer
     *
     * @package Framework\Syscrack\Game\Structures
     */

    interface Computer
    {

        public function configuration();

        public function onStartup( $computerid, $userid, array $softwares = [], array $hardwares = [] );

        public function onReset( $computerid );

        public function onLogin( $computerid, $ipaddress );

        public function onLogout( $computerid, $ipaddress );
    }
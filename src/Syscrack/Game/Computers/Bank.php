<?php
    namespace Framework\Syscrack\Game\Computers;

    /**
     * Lewis Lancaster 2017
     *
     * Class Bank
     *
     * @package Framework\Syscrack\Game\Computer
     */

    use Framework\Exceptions\SyscrackException;
    use Framework\Syscrack\Game\BaseClasses\Computer as BaseClass;
    use Framework\Syscrack\Game\Finance;
    use Framework\Syscrack\Game\Structures\Computer as Structure;

    class Bank extends BaseClass implements Structure
    {

        /**
         * @var Finance
         */

        protected $finance;

        /**
         * Npc constructor.
         */

        public function __construct()
        {

            if( isset( $this->finance ) == false )
                $this->finance = new Finance();

            parent::__construct( true );
        }

        /**
         * The configuration of this computer
         *
         * @return array
         */

        public function configuration()
        {

            return array(
                'installable' => false,
                'type'        => 'bank'
            );
        }

        /**
         * What to do when you login to this computer
         *
         * @param $computerid
         *
         * @param $ipaddress
         */

        public function onLogin($computerid, $ipaddress)
        {

            if( $this->internet->ipExists( $ipaddress ) == false )
                throw new SyscrackException();

            $this->internet->setCurrentConnectedAddress( $ipaddress );

            $this->log( $computerid, 'Logged in as root', $this->getCurrentComputerAddress() );
            $this->logToIP( $this->getCurrentComputerAddress(), 'Logged in as root at <' . $ipaddress . '>');
        }

        /**
         * What to do when you logout of a computer
         *
         * @param $computerid
         *
         * @param $ipaddress
         */

        public function onLogout($computerid, $ipaddress)
        {

            if( $this->internet->ipExists( $ipaddress ) == false )
                throw new SyscrackException();

            if( $this->finance->hasCurrentActiveAccount() == true )
                $this->finance->setCurrentActiveAccount( null );

            $this->internet->setCurrentConnectedAddress( null );
        }
    }
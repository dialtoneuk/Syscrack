<?php
    namespace Framework\Syscrack\Game\Computers;

    /**
     * Lewis Lancaster 2017
     *
     * Class Npc
     *
     * @package Framework\Syscrack\Game\Computers
     */

    use Framework\Application\Settings;
    use Framework\Exceptions\SyscrackException;
    use Framework\Syscrack\Game\BaseClasses\Computer as BaseClass;
    use Framework\Syscrack\Game\Schema;
    use Framework\Syscrack\Game\Structures\Computer as Structure;

    class Npc extends BaseClass implements Structure
    {

        protected $schema;

        public function __construct()
        {

            parent::__construct( true );

            if( isset( $this->schema ) == false )
            {

                $this->schema = new Schema();
            }
        }

        public function configuration()
        {

            return array(
                'installable' => false,
                'type'        => 'npc'
            );
        }

        public function onReset( $computerid )
        {

            $userid = $this->computers->getComputer( $computerid )->userid;

            if( $this->schema->hasSchema( $computerid ) == false )
            {

                $this->clearSoftwares( $computerid );

                $this->computers->resetHardware( $computerid );

                $this->addHardwares( $computerid, Settings::getSetting('syscrack_default_hardware') );
            }
            else
            {

                $schema = $this->schema->getSchema( $computerid );

                if( empty( $schema['softwares'] ) || empty( $schema['hardwares'] ) )
                {

                    throw new SyscrackException();
                }

                $this->clearSoftwares( $computerid );

                $this->computers->resetHardware( $computerid );

                $this->addSoftwares( $computerid, $userid, $schema['softwares'] );

                $this->addHardwares( $computerid, $schema['hardwares'] );
            }
        }

        public function onStartup($computerid, $userid, array $softwares = [], array $hardwares = [] )
        {

            if( $this->log->hasLog( $computerid ) == false )
            {

                $this->log->createLog( $computerid );
            }

            if( empty( $softwares ) == false )
            {

                $this->addSoftwares( $computerid, $userid, $softwares );
            }

            if( empty( $hardwares ) == false )
            {

                $this->addHardwares( $computerid, $hardwares );
            }
        }

        public function onLogin($computerid, $ipaddress)
        {
            // TODO: Implement onLogin() method.
        }

        public function onLogout($computerid, $ipaddress)
        {
            // TODO: Implement onLogout() method.
        }

    }
<?php
    namespace Framework\Syscrack\Game\Softwares;

/**
 * Lewis Lancaster 2017
 *
 * Class VSpam
 *
 * @package Framework\Syscrack\Game\Softwares
 */

use Framework\Application\Settings;
use Framework\Syscrack\Game\BaseClasses\Software as BaseClass;
use Framework\Syscrack\Game\Structures\Software as Structure;

class VSpam extends BaseClass implements Structure
{

    public function __construct()
    {

        parent::__construct( true );
    }

    /**
     * The configuration of this Structure
     *
     * @return array
     */

    public function configuration()
    {

        return array(
            'uniquename'    => 'vspam',
            'extension'     => '.vspam',
            'type'          => 'virus',
            'installable'   => true,
            'uninstallable' => true,
            'executable'    => false,
            'removable'    => false,
        );
    }

    public function onExecuted( $softwareid, $userid, $computerid )
    {


    }

    public function onInstalled( $softwareid, $userid, $computerid )
    {


    }

    public function onUninstalled($softwareid, $userid, $computerid)
    {


    }

    public function onCollect( $softwareid, $userid, $computerid, $timeran )
    {

        if( $this->hardware->hasHardwareType( $computerid, Settings::getSetting('syscrack_hardware_cpu_type') ) == false )
            return Settings::getSetting('syscrack_collector_vspam_yield') * $timeran;


        return ( Settings::getSetting('syscrack_collector_vspam_yield') * ( $this->hardware->getCPUSpeed( $computerid ) * $timeran ) ) / Settings::getSetting('syscrack_collector_global_yield');
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
<?php
    namespace Framework\Syscrack\Game\Softwares;

/**
 * Lewis Lancaster 2017
 *
 * Class VMiner
 *
 * @package Framework\Syscrack\Game\Softwares
 */

use Framework\Application\Settings;
use Framework\Syscrack\Game\BaseClasses\BaseSoftware;


class VMiner extends BaseSoftware
{

    /**
     * The configuration of this Structure
     *
     * @return array
     */

    public function configuration()
    {

        return array(
            'uniquename'    => 'vminer',
            'extension'     => '.vminer',
            'type'          => 'virus',
            'installable'   => true,
            'uninstallable' => true,
            'executable'    => false,
            'removable'    => false
        );
    }

    /**
     * @param $softwareid
     * @param $userid
     * @param $computerid
     * @param $timeran
     * @return float|int
     */

    public function onCollect( $softwareid, $userid, $computerid, $timeran )
    {

        if( parent::$hardware->hasHardwareType( $computerid, Settings::setting('syscrack_hardware_cpu_type') ) == false )
            {

                return Settings::setting('syscrack_collector_vspam_yield') * $timeran;
            }

        return ( Settings::setting('syscrack_collector_vspam_yield') * ( parent::$hardware->getCPUSpeed( $computerid ) * $timeran ) ) / Settings::setting('syscrack_collector_global_yield');
    }
}
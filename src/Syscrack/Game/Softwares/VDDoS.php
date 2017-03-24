<?php
namespace Framework\Syscrack\Game\Softwares;

/**
 * Lewis Lancaster 2017
 *
 * Class VDDoS
 *
 * @package Framework\Syscrack\Game\Softwares
 */

use Framework\Syscrack\Game\Structures\Software;

class VDDoS implements Software
{

    /**
     * The configuration of this software
     *
     * @return array
     */

    public function configuration()
    {

        return array(
            'uniquename'    => 'vddos',
            'extension'     => '.vddos',
            'type'          => 'ddos',
            'installable'   => false
        );
    }

    public function onExecuted( $softwareid, $userid, $computerid )
    {

        //What to do when a virus 'is executed'
    }

    public function onInstalled( $software, $userid, $computerid )
    {

        //What to do when the virus is installed
    }

    /**
     * Only executed if the type is a virus.
     *
     * @param $softwareid
     *
     * @param $userid
     *
     * @param $computerid
     *
     * @param $timeran
     *
     * @return mixed
     */

    public function onCollect($softwareid, $userid, $computerid, $timeran)
    {

        return $timeran;
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
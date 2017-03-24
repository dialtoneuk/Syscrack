<?php
namespace Framework\Syscrack\Game\Softwares;

/**
 * Lewis Lancaster 2017
 *
 * Class VMiner
 *
 * @package Framework\Syscrack\Game\Softwares
 */

use Framework\Syscrack\Game\Structures\Software;

class VMiner implements Software
{

    /**
     * The configuration of this software
     *
     * @return array
     */

    public function configuration()
    {

        return array(
            'uniquename'    => 'vminer',
            'extension'     => '.vminer',
            'type'          => 'virus',
            'installable'   => true
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

    public function onCollect($softwareid, $userid, $computerid, $timeran)
    {

        // TODO: Implement onCollect() method.
    }

    /**
     * Default size of 10.0
     *
     * @return float
     */

    public function getDefaultSize()
    {

        return 12.0;
    }

    /**
     * Default level of 1.0
     *
     * @return float
     */

    public function getDefaultLevel()
    {

        return 1.5;
    }
}
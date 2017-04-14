<?php
namespace Framework\Syscrack\Game\Softwares;

/**
 * Lewis Lancaster 2017
 *
 * Class Breaker
 *
 * @package Framework\Syscrack\Game\Softwares
 */

use Framework\Syscrack\Game\Structures\Software as Structure;
use Framework\Syscrack\Game\Software as BaseClass;

class Breaker extends BaseClass implements Structure
{

    /**
     * The configuration of this Structure
     *
     * @return array
     */

    public function configuration()
    {

        return array(
            'uniquename'    => 'breaker',
            'extension'     => '.brk',
            'type'          => 'breaker',
            'installable'   => false,
            'executable'    => false
        );
    }

    public function onExecuted( $softwareid, $userid, $computerid )
    {


    }

    public function onInstalled( $softwareid, $userid, $computerid )
    {


    }

    public function onCollect( $softwareid, $userid, $computerid, $timeran )
    {


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
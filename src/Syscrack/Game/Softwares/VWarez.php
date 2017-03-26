<?php
namespace Framework\Syscrack\Game\Softwares;

/**
 * Lewis Lancaster 2017
 *
 * Class VWarez
 *
 * @package Framework\Syscrack\Game\Softwares
 */

use Framework\Syscrack\Game\Structures\Software as Structure;

class VWarez implements Structure
{

    /**
     * The configuration of this Structure
     *
     * @return array
     */

    public function configuration()
    {

        return array(
            'uniquename'    => 'vwarez',
            'extension'     => '.vwarez',
            'type'          => 'virus',
            'installable'   => true
        );
    }

    public function onExecuted( $Structureid, $userid, $computerid )
    {


    }

    public function onInstalled( $Structureid, $userid, $computerid )
    {


    }

    public function onCollect( $Structureid, $userid, $computerid, $timeran )
    {


    }

    /**
     * Default size of 10.0
     *
     * @return float
     */

    public function getDefaultSize()
    {

        return 14.0;
    }

    /**
     * Default level of 1.0
     *
     * @return float
     */

    public function getDefaultLevel()
    {

        return 1.2;
    }
}
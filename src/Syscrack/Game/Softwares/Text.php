<?php
    namespace Framework\Syscrack\Game\Softwares;

/**
 * Lewis Lancaster 2017
 *
 * Class Text
 *
 * @package Framework\Syscrack\Game\Softwares
 */

use Framework\Syscrack\Game\BaseClasses\Software as BaseClass;
use Framework\Syscrack\Game\Structures\Software as Structure;

class Text extends BaseClass implements Structure
{

    /**
     * The configuration of this Structure
     *
     * @return array
     */

    public function configuration()
    {

        return array(
            'uniquename'    => 'text',
            'extension'     => '.txt',
            'type'          => 'text',
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
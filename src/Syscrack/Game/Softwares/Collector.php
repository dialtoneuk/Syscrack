<?php
namespace Framework\Syscrack\Game\Softwares;

/**
 * Lewis Lancaster 2017
 *
 * Class Honeypot
 *
 * @package Framework\Syscrack\Game\Collector
 *
 * It is very important that you do not autoload the software classes inside a software class.... this will cause a loop...
 */

use Framework\Syscrack\Game\Structures\Software as Structure;
use Framework\Syscrack\Game\Softwares;

class Collector implements Structure
{

    protected $softwares;

    public function __construct()
    {

        $this->softwares = new Softwares();
    }

    /**
     * The configuration of this Structure
     *
     * @return array
     */

    public function configuration()
    {

        return array(
            'uniquename'    => 'collector',
            'extension'     => '.col',
            'type'          => 'collector',
            'installable'   => false
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
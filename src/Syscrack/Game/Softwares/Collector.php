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

use Framework\Syscrack\Game\AddressDatabase;
use Framework\Syscrack\Game\BaseClasses\Software as BaseClass;
use Framework\Syscrack\Game\Finance;
use Framework\Syscrack\Game\Structures\Software as Structure;
use Framework\Syscrack\Game\Viruses;

class Collector extends BaseClass implements Structure
{

    /**
     * @var Viruses
     */

    protected $viruses;

    /**
     * @var AddressDatabase
     */

    protected $addressdatabase;

    /**
     * @var Finance;
     */

    protected $finance;

    /**
     * Collector constructor.
     */

    public function __construct()
    {

        parent::__construct();

        if( isset( $this->viruses ) == false )
        {

            $this->viruses = new Viruses();
        }

        if( isset( $this->addressdatabase ) == false )
        {

            $this->addressdatabase = new AddressDatabase();
        }

        if( isset( $this->finance ) == false )
        {

            $this->finance = new Finance();
        }
    }

    /**
     * The configuration of this Structure
     *
     * @return array
     */

    public function configuration()
    {

        return array(
            'uniquename'        => 'collector',
            'extension'         => '.col',
            'type'              => 'collector',
            'installable'       => true,
            'executable'        => true,
            'localexecuteonly'  => true,
        );
    }

    /**
     * Collects the users viruses
     *
     * @param $softwareid
     *
     * @param $userid
     *
     * @param $computerid
     *
     * @return array|bool
     */

    public function onExecuted( $softwareid, $userid, $computerid )
    {

        $this->redirect('computer/collect');

        return true;
    }

    public function onInstalled( $softwareid, $userid, $computerid )
    {

        return;
    }

    public function onUninstalled($softwareid, $userid, $computerid)
    {
        // TODO: Implement onUninstalled() method.
    }

    public function onCollect( $softwareid, $userid, $computerid, $timeran )
    {

        return;
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
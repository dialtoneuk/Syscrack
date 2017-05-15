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

use Framework\Application\Settings;
use Framework\Exceptions\SyscrackException;
use Framework\Syscrack\Game\AddressDatabase;
use Framework\Syscrack\Game\BaseClasses\Software as BaseClass;
use Framework\Syscrack\Game\Finance;
use Framework\Syscrack\Game\Structures\Software;
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

        if( $this->softwares->getSoftware( $softwareid )->type !== Settings::getSetting('syscrack_software_collector_type') )
        {

            return false;
        }

        $addresses = $this->addressdatabase->getDatabase( $userid );

        if( empty( $addresses ) )
        {

            return false;
        }

        $accounts = $this->finance->getUserBankAccounts( $userid );

        if( empty( $accounts ) )
        {

            $this->redirectError('You currently dont have any bank accounts, the collector for now will use the first account it finds', $this->getRedirect( $this->computer->getComputer( $computerid )->ipaddress ) );
        }

        $profits = [];

        foreach( $addresses as $address )
        {

            if( $this->viruses->hasVirusesOnComputer( $this->internet->getComputer( $address['ipaddress'] )->computerid, $userid ) == false )
            {

                continue;
            }

            $viruses = $this->viruses->getVirusesOnComputer( $this->internet->getComputer( $address['ipaddress'] )->computerid, $userid );

            foreach( $viruses as $virus )
            {

                $class = $this->softwares->getSoftwareClassFromID( $virus->softwareid );

                if( $class instanceof Software == false )
                {

                    throw new SyscrackException();
                }

                if( ( time() - $virus->lastmodified ) <= Settings::getSetting('syscrack_collector_cooldown') )
                {

                    continue;
                }

                $result = $class->onCollect( $virus->softwareid, $userid, $computerid, time() - $virus->lastmodified );

                if( $result == null )
                {

                    $profit = Settings::getSetting('syscrack_collector_amount');
                }
                else
                {

                    $profit = $result;
                }

                $profits[] = [
                    'profit'    => $profit * $this->softwares->getSoftware( $softwareid )->level,
                    'timeran'   => time() - $virus->lastmodified,
                    'ipaddress' => $address['ipaddress']
                ];

                $this->viruses->updateVirusModified( $virus->softwareid );
            }
        }

        if( empty( $profits ) )
        {

            $this->redirectError('No profits were collected, you need to wait ' . Settings::getSetting('syscrack_collector_cooldown') . ' seconds between each collect', $this->getRedirect( $this->computer->getComputer( $computerid )->ipaddress ) );
        }

        foreach( $profits as $profit )
        {

            $account = $accounts[0];

            if( $this->finance->accountNumberExists( $account->accountnumber ) == false )
            {

                return false;
            }

            $this->finance->deposit( $account->computerid, $userid, $profit['profit'] );
        }

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
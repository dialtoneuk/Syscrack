<?php
namespace Framework\Syscrack\Game\Utilities;

/**
 * Lewis Lancaster 2017
 *
 * Class Startup
 *
 * @package Framework\Syscrack\Game\Utility
 */

use Framework\Application\Settings;
use Framework\Application\Utilities\FileSystem;
use Framework\Exceptions\SyscrackException;
use Framework\Syscrack\Game\AddressDatabase;
use Framework\Syscrack\Game\BankDatabase;
use Framework\Syscrack\Game\Computer;
use Framework\Syscrack\Game\Finance;
use Framework\Syscrack\Game\Log;
use Framework\Syscrack\Game\Softwares;

class Startup
{

    /**
     * Startup constructor.
     *
     * @param $userid
     */

    public function __construct( $userid=null, $autorun=true )
    {

        if( $autorun == true || $userid !== null  )
        {

            $computerid = $this->createComputer( $userid );

            if( session_status() !== PHP_SESSION_ACTIVE )
            {

                session_start();
            }

            $_SESSION['current_computer'] = $computerid;

            $this->createAddressDatabase( $userid );

            $this->createBankDatabase( $userid );

            $this->createFinance( $userid );

            $this->createLog( $userid );
        }
    }

    /**
     * Creates a new computer
     *
     * @param null $userid
     *
     * @param string $type
     *
     * @param null $ip
     *
     * @return int
     */

    public function createComputer( $userid=null, $type='vpc', $ip=null )
    {

        $computer = new Computer();

        if( $ip == null )
        {

            if( $userid == null )
            {

                return $computer->createComputer( Settings::getSetting('syscrack_master_user'), $type, $this->getIP()  );
            }
            else
            {

                return $computer->createComputer( $userid, $type, $this->getIP()  );
            }
        }
        else
        {

            if( $userid == null )
            {

                return $computer->createComputer( Settings::getSetting('syscrack_master_user'), $type, $ip );
            }
            else
            {

                return $computer->createComputer( $userid, $type, $ip );
            }
        }
    }

    /**
     * Creates the address database
     *
     * @param $userid
     */

    public function createAddressDatabase( $userid )
    {

        $addressdatabase = new AddressDatabase();

        if( $addressdatabase->hasDatabase( $userid ) )
        {

            return;
        }

        $addressdatabase->saveDatabase( $userid );
    }

    /**
     * Creates the bank database
     *
     * @param $userid
     */

    public function createBankDatabase( $userid )
    {

        $bankdatabase = new BankDatabase();

        if( $bankdatabase->hasDatabase( $userid ) )
        {

            return;
        }

        $bankdatabase->saveDatabase( $userid, [] );
    }

    /**
     * Creates the users finance
     *
     * @param $userid
     *
     * @param null $computerid
     *
     * @return bool
     */

    public function createFinance( $userid, $computerid=null )
    {

        $finance = new Finance();

        if( $computerid == null )
        {

            $computerid = Settings::getSetting('syscrack_default_bank');
        }

        if( $finance->hasAccount( $userid ) )
        {

            return false;
        }

        $finance->createAccount( $computerid, $userid );
    }

    /**
     * Creates a new user log
     *
     * @param $userid
     */

    public function createLog( $userid )
    {

        $log = new Log();

        $computer = new Computer();

        $computerid = $computer->getUserMainComputer( $userid )->computerid;

        if( empty( $computerid ) )
        {

            throw new SyscrackException();
        }

        $log->createLog( $computerid );
    }

    /**
     * Creates the NPC
     *
     * @param $computerid
     *
     * @param array|null $data
     */

    public function createNPC( $computerid, array $data=null )
    {

        if( FileSystem::fileExists( Settings::getSetting('syscrack_npc_filepath') . $computerid . '.json' ) )
        {

            return;
        }

        if( $data == null )
        {

            FileSystem::writeJson( Settings::getSetting('syscrack_npc_filepath') . $computerid . '.json', [] );
        }
        else
        {

            FileSystem::writeJson( Settings::getSetting('syscrack_npc_filepath') . $computerid . '.json', $data );
        }
    }

    /**
     * Creates a computers software
     *
     * @param $userid
     *
     * @param $computerid
     *
     * @param array $softwares
     */

    public function createComputerSoftware( $userid, $computerid, array $softwares )
    {

        $softwares = new Softwares();

        $computer = new Computer();

        foreach( $softwares as $software )
        {

            $softwareid = $softwares->createSoftware( $softwares->findSoftwareByUniqueName( $software['uniquename'] ), $userid, $computerid, $software['softwarename'], $software['softwarelevel'] );

            if( $softwares->softwareExists( $softwareid ) == false )
            {

                throw new SyscrackException();
            }

            $computer->addSoftware( $computerid, $softwareid, $softwares->getSoftwareType( $softwares->getSoftwareNameFromSoftwareID( $softwareid ) ) );

            if( isset( $software['installed'] ) )
            {

                if( $software['installed'] == true )
                {

                    $softwares->installSoftware( $softwareid, $userid );

                    $computer->installSoftware( $computerid, $softwareid );
                }
            }
        }
    }

    /**
     * Creates the markets stock
     *
     * @param $computerid
     */

    public function createStock( $computerid )
    {

        if( FileSystem::directoryExists( Settings::getSetting('syscrack_marketstock_location') . $computerid . '/') )
        {

            return;
        }

        FileSystem::createDirectory( Settings::getSetting('syscrack_marketstock_location') . $computerid . '/');

        FileSystem::writeJson( Settings::getSetting('syscrack_marketstock_location') . $computerid . '/stock.json', [] );

        FileSystem::writeJson( Settings::getSetting('syscrack_marketstock_location') . $computerid . '/purchases.json', [] );
    }

    /**
     * Generates a new random IP address
     *
     * @return string
     */

    public function getIP()
    {

        return rand( Settings::getSetting('syscrack_lowest_iprange'),255) .  '.' . rand(192,255) . '.' . rand(192,255) . '.' . rand(192,255);
    }
}
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
use Framework\Syscrack\Game\Finance;
use Framework\Syscrack\Game\Log;
use Framework\Syscrack\Game\Computer;
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

            $this->createAddressDatabase( $userid );

            $this->createBankDatabase( $userid );

            $this->createFinance( $userid );

            $this->createComputer( $userid );

            $this->createLog( $userid );
        }
    }

    /**
     * Creates a new computer
     *
     * @param $userid
     */

    public function createComputer( $userid=null, $type='vpc', $ip=null )
    {

        $computer = new Computer();

        if( $ip == null )
        {

            if( $userid == null )
            {

                $computer->createComputer( Settings::getSetting('syscrack_master_user'), $type, $ip );
            }
            else
            {

                $computer->createComputer( $userid, $type, $ip );
            }
        }
        else
        {

            if( $userid == null )
            {

                $computer->createComputer( Settings::getSetting('syscrack_master_user'), $type, $this->getIP() );
            }
            else
            {

                $computer->createComputer( $userid, $type, $this->getIP() );
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

        $addressdatabase->saveDatabase( [] );
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
     * Creates the finance
     *
     * @param $userid
     */

    public function createFinance( $userid, $computerid=null )
    {

        $finance = new Finance();

        if( $computerid == null )
        {

            if( $finance->hasAccountAtComputer( $computerid, $userid))
            {

                return;
            }

            $finance->createAccount( $computerid, $userid );
        }
        else
        {

            if( $finance->hasAccountAtComputer( Settings::getSetting('syscrack_default_bank'), $userid))
            {

                return;
            }

            $finance->createAccount( Settings::getSetting('syscrack_default_bank'), $userid );
        }
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
     * Creates the computers various softwares
     *
     * @param $userid
     *
     * @param $computerid
     *
     * @param array $softwares
     */

    public function createComputerSoftware( $userid, $computerid, array $softwares )
    {

        $software = new Softwares();

        $computer = new Computer();

        foreach( $softwares as $value )
        {

            if( isset( $value['softwarename'] ) == false || isset( $value['softwareid'] ) == false || isset( $value['type'] ) )
            {

                throw new SyscrackException();
            }

            if( $software->softwareExists( $value['softwareid'] ) == false )
            {

                throw new SyscrackException();
            }

            $softwareid = $software->copySoftware( $software->getSoftware( $value['softwareid'] )->computerid, $computerid, $userid );

            $computer->addSoftware( $computerid, $softwareid, $value['type'], $value['softwarename'] );

            if( isset( $value['installed'] ) )
            {

                if( $value['installed'] == true )
                {

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
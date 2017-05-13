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
use Framework\Syscrack\Game\Internet;
use Framework\Syscrack\Game\Log;
use Framework\Syscrack\Game\Softwares;

class Startup
{

    /**
     * @var Computer
     */

    protected $computer;

    /**
     * @var Internet
     */

    protected $internet;

    /**
     * @var Softwares
     */

    protected $softwares;

    /**
     * @var AddressDatabase
     */

    public $addressdatabase;

    /**
     * @var BankDatabase;
     */

    public $bankdatabase;

    /**
     * @var Finance
     */

    public $finance;

    /**
     * @var Log
     */

    public $log;

    /**
     * Startup constructor.
     *
     * @param $userid
     */

    public function __construct( $userid=null, $autorun=true )
    {

        if( isset( $this->computer ) == false )
        {

            $this->computer = new Computer();
        }

        if( isset( $this->internet ) == false )
        {

            $this->internet = new Internet();
        }

        if( isset( $this->softwares ) == false )
        {

            $this->softwares = new Softwares();
        }

        if( isset( $this->addressdatabase ) == false )
        {

            $this->addressdatabase = new AddressDatabase();
        }

        if( isset( $this->bankdatabase ) == false )
        {

            $this->bankdatabase = new BankDatabase();
        }

        if( isset( $this->finance ) == false )
        {

            $this->finance = new Finance();
        }

        if( isset( $this->log ) == false )
        {

            $this->log = new Log();
        }

        if( $autorun == true || $userid !== null  )
        {

            if( session_status() !== PHP_SESSION_ACTIVE )
            {

                session_start();
            }

            $computerid = $this->createComputer( $userid, 'vpc', null, [], Settings::getSetting('syscrack_default_hardware') );

            if( $this->computer->computerExists( $computerid ) == false )
            {

                throw new SyscrackException('Computer does not exist');
            }

            $this->computer->setCurrentUserComputer( $computerid );

            if( $this->addressdatabase->hasDatabase( $userid ) == false )
            {

                $this->createAddressDatabase( $userid );
            }

            if( $this->bankdatabase->hasDatabase( $userid ) == false )
            {

                $this->createBankDatabase( $userid );
            }

            if( $this->finance->hasAccountAtComputer( Settings::getSetting('syscrack_default_bank'), $userid ) == false )
            {

                $this->createFinance( $userid );
            }

            if( $this->log->hasLog( $computerid ) == false )
            {

                $this->log->createLog( $computerid );
            }
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

    public function createComputer( $userid=null, $type='vpc', $ip=null, $softwares = [], $hardwares = [] )
    {

        if( $ip == null )
        {

            if( $userid == null )
            {

                return $this->computer->createComputer( Settings::getSetting('syscrack_master_user'), $type, $this->getIP(), $softwares, $hardwares  );
            }
            else
            {

                return $this->computer->createComputer( $userid, $type, $this->getIP(), $softwares, $hardwares  );
            }
        }
        else
        {

            if( $userid == null )
            {

                return $this->computer->createComputer( Settings::getSetting('syscrack_master_user'), $type, $ip, $softwares, $hardwares );
            }
            else
            {

                return $this->computer->createComputer( $userid, $type, $ip, $softwares, $hardwares );
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

        if( $this->addressdatabase->hasDatabase( $userid ) )
        {

            return;
        }

        $this->addressdatabase->saveDatabase( $userid );
    }

    /**
     * Creates the bank database
     *
     * @param $userid
     */

    public function createBankDatabase( $userid )
    {

        if( $this->bankdatabase->hasDatabase( $userid ) )
        {

            return;
        }

        $this->bankdatabase->saveDatabase( $userid, [] );
    }

    /**
     * Creates the users finance
     *
     * @param $userid
     *
     * @param null $computerid
     */

    public function createFinance( $userid, $computerid=null )
    {

        if( $computerid == null )
        {

            $computerid = Settings::getSetting('syscrack_default_bank');
        }

        if( $this->finance->hasAccountAtComputer( $computerid, $userid ) )
        {

            return;
        }

        $this->finance->createAccount( $computerid, $userid );
    }

    /**
     * Creates a new computer log
     *
     * @param null $computerid
     */

    public function createComputerLog( $computerid=null )
    {

        if( $computerid == null )
        {

            $computerid = $this->computer->getCurrentUserComputer();
        }

        if( $this->log->hasCurrentLog( $computerid ) == true )
        {

            return;
        }

        $this->log->createLog( $computerid );
    }

    /**
     * Creates the schema file for a computer
     *
     * @param $computerid
     *
     * @param array|null $data
     */

    public function createSchema( $computerid, array $data=null )
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

        foreach( $softwares as $software )
        {

            if( isset( $software['uniquename'] ) == false || isset( $software['softwarename'] ) == false || isset( $software['softwarelevel'] ) == false || isset( $software['softwaresize'] ) == false )
            {

                continue;
            }

            if( isset( $software['data'] ) )
            {

                $softwareid = $this->softwares->createSoftware( $this->softwares->getNameFromClass( $this->softwares->findSoftwareByUniqueName( $software['uniquename'] ) ), $userid, $computerid, $software['softwarename'], $software['softwarelevel'], $software['softwaresize'], $software['data'] );
            }
            else
            {

                $softwareid = $this->softwares->createSoftware( $this->softwares->getNameFromClass( $this->softwares->findSoftwareByUniqueName( $software['uniquename'] ) ), $userid, $computerid, $software['softwarename'], $software['softwarelevel'] );
            }

            if( $this->softwares->softwareExists( $softwareid ) == false )
            {

                throw new SyscrackException();
            }

            $this->computer->addSoftware( $computerid, $softwareid, $this->softwares->getSoftwareType( $this->softwares->getSoftwareNameFromSoftwareID( $softwareid ) ), $software['softwarename'] );

            if( isset( $software['installed'] ) )
            {

                if( $software['installed'] == true )
                {

                    $this->softwares->installSoftware( $softwareid, $userid );

                    $this->computer->installSoftware( $computerid, $softwareid );
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

        return $this->internet->getIP();
    }
}
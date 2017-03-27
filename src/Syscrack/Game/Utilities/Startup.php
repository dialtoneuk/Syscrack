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

class Startup
{

    /**
     * Startup constructor.
     *
     * @param $userid
     */

    public function __construct( $userid )
    {

        $this->createAddressDatabase( $userid );

        $this->createbankDatabase( $userid );

        $this->createFinance( $userid );

        $this->createComputer( $userid );

        $this->createLog( $userid );
    }

    /**
     * Creates a new computer
     *
     * @param $userid
     */

    private function createComputer( $userid )
    {

        $computer = new Computer();

        if( $computer->userHasComputers( $userid ) )
        {

            return;
        }

        $computer->createComputer( $userid, 'vpc', $this->getIP() );
    }

    /**
     * Creates the address database
     *
     * @param $userid
     */

    private function createAddressDatabase( $userid )
    {

        $addressdatabase = new AddressDatabase();

        if( $addressdatabase->hasDatabase( $userid ) )
        {

            return;
        }

        $addressdatabase->saveDatabase( $userid, [] );
    }

    /**
     * Creates the bank database
     *
     * @param $userid
     */

    private function createbankDatabase( $userid )
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

    private function createFinance( $userid )
    {

        $finance = new Finance();

        if( $finance->hasAccount( $userid ) )
        {

            return;
        }

        $finance->createAccount( Settings::getSetting('syscrack_default_bank'), $userid );
    }

    /**
     * Creates a new user log
     *
     * @param $userid
     */

    private function createLog( $userid )
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
     * Generates a new random IP address
     *
     * @return string
     */

    private function getIP()
    {

        return rand( Settings::getSetting('syscrack_lowest_iprange'),255) .  '.' . rand(192,255) . '.' . rand(192,255) . '.' . rand(192,255);
    }
}
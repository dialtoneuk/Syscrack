<?php
namespace Framework\Syscrack\Game\Utility;

/**
 * Lewis Lancaster 2017
 *
 * Class Startup
 *
 * @package Framework\Syscrack\Game\Utility
 */

use Framework\Application\Settings;
use Framework\Exceptions\SyscrackException;
use Framework\Syscrack\Game\AddressDatabase;
use Framework\Syscrack\Game\BankDatabase;
use Framework\Syscrack\Game\Finance;

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

            throw new SyscrackException();
        }

        $finance->createAccount( Settings::getSetting('syscrack_default_bank'), $userid );
    }
}
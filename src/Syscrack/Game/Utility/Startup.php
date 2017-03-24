<?php
namespace Framework\Syscrack\Game\Utility;

/**
 * Lewis Lancaster 2017
 *
 * Class Startup
 *
 * @package Framework\Syscrack\Game\Utility
 */

use Framework\Syscrack\Game\AddressDatabase;

class Startup
{

    public function __construct( $userid )
    {

        $this->createAddressDatabase( $userid );
    }

    private function createAddressDatabase( $userid )
    {

        $addressdatabase = new AddressDatabase();

        if( $addressdatabase->hasDatabase( $userid ) )
        {

            return;
        }

        $addressdatabase->createDatabase( $userid );
    }
}
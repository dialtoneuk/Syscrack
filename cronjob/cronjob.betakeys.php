<?php
    require_once "../vendor/autoload.php";

    use Framework\Syscrack\BetaKeys;


    if( php_sapi_name() == 'cli' )
    {

        $betakeys = new BetaKeys();

        $betakeys->generateBetaKeys( 1000 );

        //Echo out the betakeys

        print_r( $betakeys->getBetakeys() );
    }
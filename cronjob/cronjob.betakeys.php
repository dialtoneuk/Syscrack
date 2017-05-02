<?php
    require_once "../vendor/autoload.php";

    use Framework\Exceptions\SyscrackException;
    use Framework\Syscrack\BetaKeys;

    if( php_sapi_name() == 'cli' )
    {

        $betakeys = new BetaKeys();

        $keys = $betakeys->generateBetaKeys( 1000 );

        if( empty( $keys ) )
        {

            throw new SyscrackException();
        }

        $betakeys->addBetaKey( $keys );

        die( print_r( $betakeys->getBetakeys() ) );
    }
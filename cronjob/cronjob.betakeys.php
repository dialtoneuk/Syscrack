<?php
    require_once "../vendor/autoload.php";

    use Framework\Application\Settings;
    use Framework\Exceptions\SyscrackException;
    use Framework\Syscrack\BetaKeys;

    /**
     * Special thank's to Deck for pointing this out :)
     */

    if( php_sapi_name() == 'cli' )
    {

        $_SERVER['DOCUMENT_ROOT'] = realpath( getcwd() . '/..');

        Settings::preloadSettings();

        if( empty( Settings::getSettings() ) )
        {

            throw new SyscrackException('Settings have failed to load');
        }

        $betakeys = new BetaKeys();

        $keys = $betakeys->generateBetaKeys( 1000 );

        if( empty( $keys ) )
        {

            throw new SyscrackException();
        }

        print_r( json_encode( $keys, JSON_PRETTY_PRINT ) );
    }
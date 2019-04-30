<?php

require_once "vendor/autoload.php";

use Framework\Application\UtilitiesV2\Debug;
use Framework\Application\UtilitiesV2\Container;
use Framework\Application\UtilitiesV2\Scripts;
use Framework\Application\Settings;



Debug::setCMD();

if (empty( $_SERVER["DOCUMENT_ROOT"] ) )
    $root = getcwd();
else
    $root = $_SERVER["DOCUMENT_ROOT"];

if( substr( $root, -1 ) !== DIRECTORY_SEPARATOR )
    $root = $root . DIRECTORY_SEPARATOR;

if( version_compare(PHP_VERSION, '7.0.0') == -1 )
    die('Please upgrade to PHP 7.0.0+ to run this web application. Your current PHP version is ' . PHP_VERSION );

if( php_sapi_name() !== 'cli' && Debug::isCMD() == true )
{
    header("HTTP/1.0 404 Not Found");
    exit;
}

define("SYSCRACK_ROOT", $root );

Settings::preloadSettings();

include "index.php";

try
{

    Debug::echo("Script Preload \n" );

    if( count( $argv ) == 1 )
        throw new Error("Not enough arguments, please provide a script");

    $scripts = new Scripts( $argv );

    Debug::echo("\n Executing " . $scripts->script() . "\n" );

    if( $scripts->exists( $scripts->script() ) == false )
        throw new Error("Script does not exist: " . $scripts->script() . ". Please execute php -f cmd/execute.php help script=false if you are having trouble." );

    //Globalizing Scripts
    Container::add("scripts", $scripts );

    //Execute
    $scripts->execute( $scripts->script() );
}
catch ( Error $error )
{

    Debug::echo( "[Critical Error " . $error->getFile() . " " . $error->getLine() . "] : " . $error->getMessage() . "" );

    if( empty( $scripts ) )
        exit( 1 );

    Debug::echo("\n You can type php -f cmd/execute.php help script=" . $scripts->script() . " for help!");

    exit( 1 );
}


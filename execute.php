<?php

require_once "vendor/autoload.php";

use Framework\Application\UtilitiesV2\Debug;
use Framework\Application\UtilitiesV2\Container;
use Framework\Application\UtilitiesV2\Scripts;
use Framework\Application\Settings;
use Framework\Application;

if( version_compare(PHP_VERSION, '7.0.0') == -1 )
    die('Please upgrade to PHP 7.0.0+ to run this web application. Your current PHP version is ' . PHP_VERSION );

if( php_sapi_name() !== 'cli' && Debug::isCMD() == true )
{
    header("HTTP/1.0 404 Not Found");
    exit;
}

Debug::setCMD();
Debug::echo("including index.php", 2 );
include_once "index.php";
$application = new Application(false);
$application->addToGlobalContainer();

try
{

    Debug::echo("Booting Syscrack 2019" );

	Debug::echo("              _____                _____                    _____                    _____                    _____                    _____                    _____                    _____             ");
	Debug::echo("             /\    \              |\    \                  /\    \                  /\    \                  /\    \                  /\    \                  /\    \                  /\    \            ");
	Debug::echo("            /::\    \             |:\____\                /::\    \                /::\    \                /::\    \                /::\    \                /::\    \                /::\____\           ");
	Debug::echo("           /::::\    \            |::|   |               /::::\    \              /::::\    \              /::::\    \              /::::\    \              /::::\    \              /:::/    /           ");
	Debug::echo("          /::::::\    \           |::|   |              /::::::\    \            /::::::\    \            /::::::\    \            /::::::\    \            /::::::\    \            /:::/    /            ");
	Debug::echo("         /:::/\:::\    \          |::|   |             /:::/\:::\    \          /:::/\:::\    \          /:::/\:::\    \          /:::/\:::\    \          /:::/\:::\    \          /:::/    /             ");
	Debug::echo("        /:::/__\:::\    \         |::|   |            /:::/__\:::\    \        /:::/  \:::\    \        /:::/__\:::\    \        /:::/__\:::\    \        /:::/  \:::\    \        /:::/____/              ");
	Debug::echo("        \:::\   \:::\    \        |::|   |            \:::\   \:::\    \      /:::/    \:::\    \      /::::\   \:::\    \      /::::\   \:::\    \      /:::/    \:::\    \      /::::\    \              ");
	Debug::echo("      ___\:::\   \:::\    \       |::|___|______    ___\:::\   \:::\    \    /:::/    / \:::\    \    /::::::\   \:::\    \    /::::::\   \:::\    \    /:::/    / \:::\    \    /::::::\____\________     ");
	Debug::echo("     /\   \:::\   \:::\    \      /::::::::\    \  /\   \:::\   \:::\    \  /:::/    /   \:::\    \  /:::/\:::\   \:::\____\  /:::/\:::\   \:::\    \  /:::/    /   \:::\    \  /:::/\:::::::::::\    \    ");
	Debug::echo("    /::\   \:::\   \:::\____\    /::::::::::\____\/::\   \:::\   \:::\____\/:::/____/     \:::\____\/:::/  \:::\   \:::|    |/:::/  \:::\   \:::\____\/:::/____/     \:::\____\/:::/  |:::::::::::\____\   ");
	Debug::echo("    \:::\   \:::\   \::/    /   /:::/~~~~/~~      \:::\   \:::\   \::/    /\:::\    \      \::/    /\::/   |::::\  /:::|____|\::/    \:::\  /:::/    /\:::\    \      \::/    /\::/   |::|~~~|~~~~~        ");
	Debug::echo("     \:::\   \:::\   \/____/   /:::/    /          \:::\   \:::\   \/____/  \:::\    \      \/____/  \/____|:::::\/:::/    /  \/____/ \:::\/:::/    /  \:::\    \      \/____/  \/____|::|   |             ");
	Debug::echo("      \:::\   \:::\    \      /:::/    /            \:::\   \:::\    \       \:::\    \                    |:::::::::/    /            \::::::/    /    \:::\    \                    |::|   |             ");
	Debug::echo("       \:::\   \:::\____\    /:::/    /              \:::\   \:::\____\       \:::\    \                   |::|\::::/    /              \::::/    /      \:::\    \                   |::|   |             ");
	Debug::echo("        \:::\  /:::/    /    \::/    /                \:::\  /:::/    /        \:::\    \                  |::| \::/____/               /:::/    /        \:::\    \                  |::|   |             ");
	Debug::echo("         \:::\/:::/    /      \/____/                  \:::\/:::/    /          \:::\    \                 |::|  ~|                    /:::/    /          \:::\    \                 |::|   |             ");
	Debug::echo("          \::::::/    /                                 \::::::/    /            \:::\    \                |::|   |                   /:::/    /            \:::\    \                |::|   |             ");
	Debug::echo("           \::::/    /                                   \::::/    /              \:::\____\               \::|   |                  /:::/    /              \:::\____\               \::|   |             ");
	Debug::echo("            \::/    /                                     \::/    /                \::/    /                \:|   |                  \::/    /                \::/    /                \:|   |             ");
	Debug::echo("             \/____/                                       \/____/                  \/____/                  \|___|                   \/____/                  \/____/                  \|___|             ");

	Debug::session( time() );

    if( count( $argv ) == 1 )
        $argv[] = "instance";

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


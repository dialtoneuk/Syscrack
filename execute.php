<?php

	/**
	 * Check if composer has been installed
	 */

	if( file_exists( "vendor/autoload.php") )
		require_once "vendor/autoload.php";
	else
		die("Please install composer and run the following command in my root directory"
		. "\n composer install --profile");

	/**
	 * Great, now lets use these classes to initiate the instance
	 */

	use Framework\Application\UtilitiesV2\Debug;
	use Framework\Application\UtilitiesV2\Container;
	use Framework\Application\UtilitiesV2\Scripts;
	use Framework\Application\UtilitiesV2\Format;
	use Framework\Application\Settings;

	//Sets web application to CMD mode. This basically makes index.php not run the flight engine which will break in CLI.
	Debug::setCMD();


	//Helper function
	function block()
	{

		if( isset( $_SERVER["REMOTE_ADDR"] ) == false )
			$uid = getmyuid();
		else
			$uid = $_SERVER["REMOTE_ADDR"];

		if( php_sapi_name() !== 'cli' || isset( $_SERVER["REQUEST_URI"] ) )
			if( Debug::isCMD() == true )
			{

				try
				{
					//Logs this encounter
					$path = "data/intruders_" . Format::year() . ".log";
					$contents = @file_get_contents( $path );
					$message = "blocked " . addslashes( $uid ) . " at " . Format::timestamp( time(), true  ) . "\n";


					if( isset( $_SERVER["REQUEST_URI"] ) && empty( $_SERVER["REQUEST_URI"] && @$_SERVER["REQUEST_URI"] !== "/execute.php") == false )
						$message .= "user attempted to execute: " . addslashes( @$_SERVER["REQUEST_URI"] ) . "\n";

					//Writes the file but only if there isn't a dupe
					if( strpos( $contents, $message ) === false )
						@file_put_contents( $path, ( $contents .= $message ) );
				}
				catch ( Error  $error )
				{

					//Just secret exit
				}
				catch ( RuntimeException $error )
				{
					//Just secret exit
				}
				finally
				{
					header("HTTP/1.0 404 Not Found");
					exit( 0 );
				}
			}
	}

	//This is really important. This block of code essentially stops anybody accessing this file from a HTTP browser.
	block();

	//Include the original index.php. This will set all of our globals and initialize syscracks core engine.
	Debug::echo("Including index.php");
	include_once "index.php";

	if( Settings::canFindSettings() == false )
		die("Cannot find settings file! Please check the following information is correct"
			. "<br>Root: " . $root
			. "<br>CWD: " . getcwd()
			. "<br>Document Root: " . $_SERVER["DOCUMENT_ROOT"]
			. "<br>Settings file: " . $root . FileSystem::separate("data","config","settings.json" )
			. "<br>If you are still struggling with this error. Please post an issue on our official github page."
			. "<br><br>https://github.com/dialtoneuk/syscrack"
		);
	elseif( empty( Settings::settings() ) )
		Settings::setup();

	try
	{

		if( isset( $_SERVER["REQUEST_TIME"] ) )
			$time = $_SERVER["REQUEST_TIME"];
		else
			$time = time();

		//Lets the script engine and set our current session to the time of execution
	    Debug::echo("Starting script engine" );
	    Debug::session( $time );

	    //Some nice 2007 esk ascii art for your terminal
		if( 1 > 2 )
		{

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
		}
	    else
	    {

		    Debug::echo("\n   oooooooo8                                                                  oooo           ", 1);
			Debug::echo("888       oooo   oooo oooooooo8    ooooooo  oo oooooo   ooooooo    ooooooo   888  ooooo    ", 1);
			Debug::echo(" 888oooooo 888   888 888ooooooo  888     888 888    888 ooooo888 888     888 888o888       ", 1);
		    Debug::echo("        888 888 888          888 888         888      888    888 888         8888 88o      ", 1);
			Debug::echo("o88oooo888    8888   88oooooo88    88ooo888 o888o      88ooo88 8o  88ooo888 o888o o888o    ", 1);
			Debug::echo("           o8o888  Written by Lewis Lancaster (03/06/2019)                                 ", 1);
	    }

	    //If no arguments, then execute the default argument
	    if( count( $argv ) == 1 )
	        $argv[] = CLI_DEFAULT_COMMAND;

	    $scripts = new Scripts( $argv );

	    Debug::echo("\n Executing " . $scripts->script() . "\n" );

	    if( $scripts->exists( $scripts->script() ) == false )
	        throw new Error($scripts->script() . " does not exist" );

	    //Add the scripts class to the global container
	    Container::add("scripts", $scripts );

	    //Execute
	    $scripts->execute( $scripts->script() );
	}
	catch ( Error $error )
	{

	    Debug::echo( "[Critical Error " . $error->getFile() . " " . $error->getLine() . "] : " . $error->getMessage() . "" );
	    Debug::echo( "Please look in your error log for more detail.");
	}
	finally
	{

		if( Container::exist("scripts") == false )
			exit( 1 );

		Debug::echo("\n Finished execution for " . Container::get("scripts")->script()
			. " started at " . Format::timestamp( @$_SERVER["REQUEST_TIME"] )
			. " and finished at " . Format::timestamp()
		);

		exit( 1 );
	}

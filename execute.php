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
	use Framework\Application;

	//Sets web application to CMD mode. This basically makes index.php not run the flight engine which will break in CLI.
	Debug::setCMD();

	//Lets block anything that we don't want
	Application::block( true );

	Debug::echo("Including index.php");
	if( file_exists("index.php") == false )
		die("Please place me next to your syscrack compliant index.php");
	else
		include_once "index.php";

	if( isset( $application ) == false )
		if( Container::exist('application'))
			$application = Container::get('application');
		else
			throw new Error("Application class not found in global container");

	$result = $application->cli( $argv );

	if( $result == false )
		if( $application->getErrorHandler()->hasErrors() )
		{

			$errors = $application->getErrorHandler()->getErrorLog();

			Debug::echo("Script concluded with " . count( $errors ) . "errors");

			foreach( $errors as $key=>$error )
				Debug::echo( $error );
		}

	exit( @$result );


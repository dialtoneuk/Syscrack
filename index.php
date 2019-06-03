<?php

	/**
		 ____ ____ ____ ____ ____ ____ ____ ____
		||S |||y |||s |||c |||r |||a |||c |||k || Alpha 2019
		||__|||__|||__|||__|||__|||__|||__|||__|| Written by Lewis Lancaster 2019
		|/__\|/__\|/__\|/__\|/__\|/__\|/__\|/__\| Apache-2.0 License
	 * [==================================================================================]
	 * This open source project is protected by the Apache-2.0 License. For more license
	 * information as well as FAQ on what exactly you can do with this code. Please visit
	 * the github and read the license at:-
	 *
	 *              https://github.com/dialtoneuk/syscrack/blob/master/LICENSE
	 * [==================================================================================]
	 */

	if( file_exists( "vendor/autoload.php") )
		require_once "vendor/autoload.php";
	else
		die("Please install composer and run the following command in a terminal while in my root directory :-)"
			. "\n composer install --profile");

//<editor-fold defaultstate="collapsed" desc="Application Root">
    use Framework\Application\UtilitiesV2\Debug;

    if( Debug::isPHPUnitTest() == false )
    {

        if (empty( $_SERVER["DOCUMENT_ROOT"] ) )
            $root = getcwd();
        else
            $root = $_SERVER["DOCUMENT_ROOT"];

        if( substr( $root, -1 ) !== DIRECTORY_SEPARATOR )
            $root = $root . DIRECTORY_SEPARATOR;

        if( version_compare(PHP_VERSION, '7.0.0') == -1 )
            die('Please upgrade to PHP 7.0.0+ to run this web application. Your current PHP version is ' . PHP_VERSION );
    }
    else
        $root = PHPUNIT_ROOT;

	if( defined("SYSCRACK_ROOT") == false )
		define("SYSCRACK_ROOT", $root );
//</editor-fold>

//<editor-fold defaultstate="collapsed" desc="Initialization">
	if( php_sapi_name() === 'cli' && Debug::isCMD() == false )
		die('It seems you have tried to execute index.php inside a terminal. Congratulations. Please run execute.php instead.'
			. "<br>If you require documentation and tutorials on how to use our fancy terminal instance. Please read the wiki on our official github."
			. "<br><br>https://github.com/dialtoneuk/syscrack"
		);

    if( class_exists('Framework\\Application') == false )
        die("Framework\\Application not found! Please check the following conditions have been met"
            . "<br>Composer update has been ran on syscracks root directory."
            . "<br>You are running PHP 7.2+ ( you are on " . PHP_VERSION . ")."
            . "<br>Your PHP error log is clean."
            . "<br>If you are still struggling with this error. Please post an issue on our official github page."
            . "<br><br>https://github.com/dialtoneuk/syscrack"
        );

    //Okay great lets try and use these classes and assume the user knows composer
    use Framework\Application;
    use Framework\Application\Settings;
    use Framework\Application\Utilities\FileSystem;

    //Init our globals
    Application::defineGlobals();

    if( Settings::canFindSettings() == false )
        die("Cannot find settings file! Please check the following information is correct"
	        . "<br>Root: " . $root
	        . "<br>CWD: " . getcwd()
	        . "<br>Document Root: " . $_SERVER["DOCUMENT_ROOT"]
	        . "<br>Settings file: " . $root . FileSystem::separate("data","config","settings.json" )
	        . "<br>If you are still struggling with this error. Please post an issue on our official github page."
	        . "<br><br>https://github.com/dialtoneuk/syscrack"
        );

    //Initialize
    try
    {

	    $application = new Application( false );

	    if( Application::globals()->DEBUG_ENABLED )
		    Debug::initialization();

	    $application->go();
    }
    catch( RuntimeException $error )
    {

        die("Oh no! Something really bad happened and Syscrack exploded when trying to load its engine."
	        . "<br>Please post this as an issue to our github repository"
	        . "<pre>" . $error->getFile() .  " line " . $error->getLine() ."</pre>"
	        . "<pre>" . $error->getMessage() .  "</pre>"
	        . "<pre>" . $error->getTraceAsString() .  "</pre>"
	        . "<br><br>https://github.com/dialtoneuk/syscrack"
        );
    }
//</editor-fold>
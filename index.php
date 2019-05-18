<?php
    require_once "vendor/autoload.php";

    /**
         ____ ____ ____ ____ ____ ____ ____ ____
        ||S |||y |||s |||c |||r |||a |||c |||k || Alpha 2019
        ||__|||__|||__|||__|||__|||__|||__|||__|| Written by Lewis Lancaster 2019
        |/__\|/__\|/__\|/__\|/__\|/__\|/__\|/__\| Appache-2.0 License
     * [==================================================================================]
     * This open source project is protected by the Apache-2.0 License. For more license
     * information as well as FAQ on what exactly you can do with this code. Please visit
     * the github and read the license at:-
     *
     *        https://github.com/dialtoneuk/syscrack-prototype/blob/master/LICENSE
     * [==================================================================================]
     */

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
    {

        //Maybe we are PHP Unit
        $root = PHPUNIT_ROOT;
    }

//<editor-fold defaultstate="collapsed" desc="Application Settings">

    /**
     * Written by Lewis 'mkultra2018' Lancaster
     * in 2017 (March to May), 2018 (March to May), 2019 (May-Present)
     * =======================================
     */

    //Syscrack

    if( defined("SYSCRACK_ROOT") == false )
        define("SYSCRACK_ROOT", $root );

    define("SYSCRACK_URL_ROOT", "/");
    define("SYSCRACK_NAMESPACE_ROOT", "Framework\\");
    define("SYSCRACK_URL_ADDRESS", "http://localhost");
    define("SYSCRACK_VERSION_PHASE","alpha");
    define("SYSCRACK_VERSION_NUMBER","0.1.5");

    //Framework
    define("FRAMEWORK_BASECLASS", "base");  //This ties to a few of the instance builders and won't build a direct instance of any class called "Base". This is useful in the
    //Util namespace for not directly invoking my interfaces and leaving them easily modifiable. If you want to load the base, even
    //though it could potentially crash the system. Change "base" to "".
    //Website
    define("WEBSITE_TITLE", "Syscrack");
    define("WEBSITE_JQUERY", "jquery-3.3.1.min.js");
    define("WEBSITE_BOOTSTRAP4", "bootstrap.js");

    //User Accounts
    define("ACCOUNT_PREFIX", "user");
    define("ACCOUNT_DIGITS", 8);
    define("ACCOUNT_RND_MIN", 1);
    define("ACCOUNT_RND_MAX", 8);
    define("ACCOUNT_PASSWORD_MIN", 8);
    define("ACCOUNT_PASSWORD_STRICT", false );

    //Tracks
    define("TRACK_PRIVACY_PUBLIC", "public");
    define("TRACK_PRIVACY_PRIVATE", "private");
    define("TRACK_PRIVACY_PERSONAL", "personal");
    define("TRACK_PREFIX", "track");
    define("TRACK_NAME_MAXLENGTH", 64);
    define("TRACK_DIGITS", 12);
    define("TRACK_RND_MIN", 0);
    define("TRACK_RND_MAX", 9);

    //Global Upload Settings
    define("UPLOADS_TEMPORARY_DIRECTORY", "files/temp/");
    define("UPLOADS_FILEPATH","src/Framework/Uploads/");
    define("UPLOADS_NAMESPACE","Framework\\Application\\Uploads\\");
    define("UPLOADS_LOCAL", true ); //Will keep files in the temporary directory instead of uploading them ( should only be used for testing )
    define("UPLOADS_WAVEFORMS_LOCAL", true ); //Will keep wave forms in the temporary directory instead of uploading them. Use with global above to completely turn off the data handler.
    define("UPLOADS_POST_KEY", "track");
    define("UPLOADS_MAX_SIZE_GLOBAL", 500); //Used to define the max applicable size anybody can upload
    define("UPLOADS_ERROR_NOT_FOUND", 1 );
    define("UPLOADS_ERROR_FILENAME", 2 );
    define("UPLOADS_ERROR_EXTENSION", 3 );
    define("UPLOADS_ERROR_TOO_LARGE", 4 );
    define("UPLOADS_ERROR_CANCELLED", 5 );


    define("SCRIPTS_ROOT","src/Application/UtilitiesV2/Scripts/");
    define("SCRIPTS_NAMESPACE", "Framework\\Application\\UtilitiesV2\\Scripts\\");
    define("SCRIPTS_REQUIRE_CMD", true );

    //ffmeg
    define("FFMPEG_CONFIG_FILE","data/config/ffmpeg.json");

    //Verification
    define("VERIFICATIONS_NAMESPACE", "Framework\\Application\\Verifications\\");
    define("VERIFICATIONS_ROOT", "src/Framework/Verifications/");
    define("VERIFICATIONS_TYPE_EMAIL", "email");
    define("VERIFICATIONS_TYPE_MOBILE", "mobile");

    //Amazon
    define("AMAZON_BUCKET_URL", "https://s3.eu-west-2.amazonaws.com/colourspace/");
    define("AMAZON_CREDENTIALS_FILE", "data/config/storage/amazon.json");
    define("AMAZON_S3_BUCKET", "colourspace");
    define("AMAZON_LOCATION_US_WEST", "us-west-1");
    define("AMAZON_LOCATION_US_WEST_2", "us-west-2");
    define("AMAZON_LOCATION_US_EAST", "us-east-1");
    define("AMAZON_LOCATION_US_EAST_2", "us-east-2");
    define("AMAZON_LOCATION_CA_CENTRAL", "ca-central-1");
    define("AMAZON_LOCATION_EU_WEST", "eu-west-1");
    define("AMAZON_LOCATION_EU_WEST_2", "eu-west-2");
    define("AMAZON_LOCATION_EU_CENTRAL", "eu-central-1");

    //Google recaptcha
    define("GOOGLE_RECAPTCHA_ENABLED", false );
    define("GOOGLE_RECAPTCHA_CREDENTIALS", "data/config/google_recaptcha.json" );

    //Google Cloud Storage
    define("GOOGLE_CLOUD_CREDENTIALS",  "data/config/storage/google.json");

    //Cloud Storage
    define("STORAGE_CONFIG_ROOT","cdata/config/storage/");
    define("STORAGE_SETTINGS_FILE","settings.json");

    //Syscrack
    define('SYSCRACK_TIME_START', microtime( true ) );

    //Flight
    define("FLIGHT_JQUERY_FILE", "jquery-3.3.1.min.js");
    define("FLIGHT_CONTENT_OBJECT", true ); //Instead, convert $model into an object ( which is cleaner )
    define("FLIGHT_MODEL_DEFINITION", "model" );
    define("FLIGHT_PAGE_DEFINITION", "page" );
    define("FLIGHT_SET_GLOBALS", true );
    define("FLIGHT_VIEW_FOLDER", "themes" );

    ///Twig
    define("TWIG_VIEW_FOLDER", "themes");

    //Setups
    define("SETUP_ROOT", "src/Application/UtilitiesV2/Setups/");
    define("SETUP_NAMESPACE", "Framework\\Application\\UtilityV2\\Setups\\");

    //MVC
    define("MVC_NAMESPACE", "Framework\\Application\\MVC\\");
    define("MVC_NAMESPACE_MODELS", "Models");
    define("MVC_NAMESPACE_VIEWS", "Views");
    define("MVC_NAMESPACE_CONTROLLERS", "Controllers");
    define("MVC_TYPE_MODEL", "model");
    define("MVC_TYPE_VIEW", "view");
    define("MVC_TYPE_CONTROLLER", "controller");
    define("MVC_REQUEST_POST", "POST");
    define("MVC_REQUEST_GET", "GET");
    define("MVC_REQUEST_PUT", "PUT");
    define("MVC_REQUEST_DELETE", "DELETE");
    define('MVC_ROUTE_FILE', 'config/routes.json');
    define("MVC_ROOT", "src/Views/MVC/");

    //Makers
    define("MAKER_FILEPATH", "src/Application/UtilitiesV2/Makers/");
    define("MAKER_NAMESPACE", "Framework\\Application\\UtilitiesV2\\Makers\\");

    //Pages
    define("PAGE_SIZE", 6 ); //Default page size: 6 objects wide

    //Form
    define("FORM_ERROR_GENERAL", "general_error");
    define("FORM_ERROR_INCORRECT", "incorrect_information");
    define("FORM_ERROR_MISSING", "missing_information");
    define("FORM_MESSAGE_SUCCESS", "success_message");
    define("FORM_MESSAGE_INFO", "info_message");
    define("FORM_DATA", "data");

    //Resource combiner

    define("RESOURCE_COMBINER_ROOT", "data/config/");
    define("RESOURCE_COMBINER_CHMOD", true );
    define("RESOURCE_COMBINER_CHMOD_PERM", 0755 );
    define("RESOURCE_COMBINER_PRETTY", true );
    define("RESOURCE_COMBINER_FILEPATH", "data/resources.bundle" );

    //Fields
    define("FIELD_TYPE_INCREMENTS","increments");
    define("FIELD_TYPE_STRING","string");
    define("FIELD_TYPE_INT","integer");
    define("FIELD_TYPE_PRIMARY","primary");
    define("FIELD_TYPE_TIMESTAMP","timestamp");
    define("FIELD_TYPE_DECIMAL","decimal");
    define("FIELD_TYPE_JSON","json");
    define("FIELD_TYPE_IPADDRESS","ipAddress");

    //Columns
    define("COLUMN_USERID", "userid");
    define("COLUMN_SESSIONID", "sessionid");
    define("COLUMN_CREATION", "creation");
    define("COLUMN_METAINFO", "metainfo");
    define("COLUMN_TRACKID", "trackid");

    //Tables
    define("TABLES_NAMESPACE", "Framework\\Database\\Tables\\");
    define("TABLES_ROOT", "src/Database/Tables/");

    //Tests
    define("TESTS_NAMESPACE", "Framework\\Application\\UtilityV2\\Tests\\");
    define("TESTS_ROOT", "src/Application/UtilitiesV2/Tests/");

    //Audit (Moderation)
    define("AUDIT_TYPE_BAN","ban");
    define("AUDIT_TYPE_WARNING","warning");
    define("AUDIT_TYPE_GROUPCHANGE","groupchange");

    //Log
    define("LOG_ROOT","data/config/");
    define("LOG_TYPE_GENERAL", "general");
    define("LOG_TYPE_WARNING", "warning");
    define("LOG_TYPE_DEFAULT", "default");

    //Auto Execute
    define("AUTOEXEC_ROOT", "src/Application/UtilitiesV2/AutoExecs/");
    define("AUTOEXEC_NAMESPACE", "Framework\\Application\\UtilityV2\\AutoExecs\\");
    define("AUTOEXEC_SCRIPTS_ROOT","data/config/autoexec/");
    define("AUTOEXEC_LOG_REFRESH", 12 ); //In hours
    define("AUTOEXEC_LOG_LOCATION","data/config/log/");

    //Database Settings
    define("DATABASE_ENCRYPTION", false);
    define("DATABSAE_ENCRYPTION_KEY", null ); //Replace null with a string of a key to not use a rand gen key.
    define("DATABASE_CREDENTIALS", "data/config/database/connection.json");
    define("DATABASE_MAP", "data/config/database/databaseschema.json");

    //Groups
    define("GROUPS_ROOT", "data/config/groups/");
    define("GROUPS_DEFAULT", "default");
    define("GROUPS_FLAG_MAXLENGTH", "uploadmaxlength");
    define("GROUPS_FLAG_MAXSIZE", "uploadmaxsize");
    define("GROUPS_FLAG_LOSSLESS", "lossless");
    define("GROUPS_FLAG_ADMIN", "admin");
    define("GROUPS_FLAG_DEVELOPER", "developer");

    //User Permissions
    define("USER_PERMISSIONS_ROOT", "data/config/user/");


    //Featured
    define("FEATURED_ROOT", "data/featured/");
    define("FEATURED_ARTISTS", "artists");
    define("FEATURED_TRACKS", "tracks");

    //Stream audio codec types
    define("STREAMS_MP3", "mp3");
    define("STREAMS_FLAC", "flac");
    define("STREAMS_OGG", "ogg");
    define("STREAMS_WAV", "wav");

    //Debugging Options
    define("DEBUG_ENABLED", true ); //Will write debug messages and echo them inside the terminal instance
    define("DEBUG_WRITE_FILE", true );
    define("DEBUG_MESSAGES_FILE", 'data/config/debug/messages.json');
    define("DEBUG_TIMERS_FILE", 'data/config/debug/timers.json');

    //Mailer
    define("MAILER_CONFIGURATION_FILE", "data/config/templates.json");
    define("MAILER_TEMPLATES_ROOT", "resources/email/");
    define("MAILER_IS_HTML", true );
    define("MAILER_IS_SMTP", true );
    define("MAILER_FROM_ADDRESS", "user00000001@Syscrack.io" );
    define("MAILER_FROM_USER", "user00000001" );
    define("MAILER_CONTACT_ADDRESS", "support@Syscrack.io" );
    define("MAILER_VERIFY_TEMPLATE", "email" );
    define("MAILER_BANNED_TEMPLATE", "banned" );
    define("MAILER_REMOVED_TEMPLATE", "removed" );
    define("MAILER_POSTED_TEMPLATE", "posted" );
    define("MAILER_COMMENTS_TEMPLATE", "comments" );

    //Javascript Builder
    define("SCRIPT_BUILDER_ENABLED", true ); //It isnt recommended you turn this on unless your compiled.js for some reason is missing or you are developing.
    define("SCRIPT_BUILDER_ROOT", "resources/scripts/");
    define("SCRIPT_BUILDER_FREQUENCY", 60 * 60 * 2); //Change the last digit for hours. Remove a "* 60" for minutes.
    define("SCRIPT_BUILDER_COMPILED", "compiled.js");
    define("SCRIPT_BUILDER_FORCED", false ) ;//Compiles a fresh build each request regardless of frequency setting.

    //Misc
    define("COLLECTOR_DEFAULT_NAMESPACE", "Framework\\Application\\");

    //Colours
    define("COLOURS_OUTPUT_HEX", 1);
    define("COLOURS_OUTPUT_RGB", 2);

    //Shop
    define("SHOP_ROOT","src/Framework/Items/");
    define("SHOP_NAMESPACE","Framework\\Application\\Items\\");
    define("SHOP_INVENTORY","data/config/shop/items.json");

    //Balance
    define("BALANCE_DEFAULT_AMOUNT", 100 );

    //Transactions
    define("TRANSACTION_TYPE_WITHDRAW", "withdraw" );
    define("TRANSACTION_TYPE_DEPOSIT", "deposit" );

    //Migrator Util
    define("MIGRATOR_ROOT", "src/Application/UtilitiesV2/Migrators/");
    define("MIGRATOR_NAMESPACE","Framework\\Application\\UtilityV2\\Migrators\\");

    define("PHPUNIT_FINISHED", true );

    //</editor-fold>

    if( php_sapi_name() === 'cli' && Debug::isCMD() == false )
        die('Please run this web application through your web browser. It wont work via the console!');

//<editor-fold defaultstate="collapsed" desc="Syscrack Initialization">

    /**
     * Check if the framework application class exists
     */

    if( class_exists('Framework\\Application') == false )
    {

        ob_clean();

        ?>

            <html>
                <head>
                    <meta charset="utf-8">
                    <meta http-equiv="X-UA-Compatible" content="IE=edge">
                    <meta name="viewport" content="width=device-width, initial-scale=1">

                    <title>Framework Error</title>

                    <!-- Stylesheets -->
                    <link href="/assets/css/bootstrap.dark.css" rel="stylesheet">
                    <link href="/assets/css/bootstrap-combobox.css" rel="stylesheet">

                    <!--[if lt IE 9]>
                    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
                    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
                    <![endif]-->
                </head>
                <body>
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-12">
                                <h5 style="color: #ababab" class="text-uppercase text-center">
                                    Critical Error
                                </h5>
                                <div class="panel panel-danger">
                                    <div class="panel-heading">
                                        Major error
                                    </div>
                                    <div class="panel-body text-center">
                                        The framework was unable to find the Application class, this could be due to a few reasons, please check out the <a href="https://github.com/dialtoneuk/Syscrack2017/">github for solutions.</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </body>
            </html>
        <?php

        exit;
    }

    use Framework\Application;
    use Framework\Application\Settings;

    if( Settings::canFindSettings() == false )
    {

        ob_clean();

        ?>

            <html>
                <head>
                    <meta charset="utf-8">
                    <meta http-equiv="X-UA-Compatible" content="IE=edge">
                    <meta name="viewport" content="width=device-width, initial-scale=1">

                    <title>Framework Error</title>

                    <!-- Stylesheets -->
                    <link href="/assets/css/bootstrap.dark.css" rel="stylesheet">
                    <link href="/assets/css/bootstrap-combobox.css" rel="stylesheet">

                    <!--[if lt IE 9]>
                    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
                    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
                    <![endif]-->
                </head>
                <body>
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-12">
                                <h5 style="color: #ababab" class="text-uppercase text-center">
                                    Critical Error
                                </h5>
                                <div class="panel panel-danger">
                                    <div class="panel-heading">
                                        Major error
                                    </div>
                                    <div class="panel-body text-center">
                                        The framework was unable to find your settings file located at <?=Settings::fileLocation()?>, this could be because of a few reasons. We suggest you check out <a href="https://github.com/dialtoneuk/Syscrack2017/">the github.</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
                </body>
            </html>
        <?php

        exit;
    }

    /**
    * Starts the application
    */

    try
    {


	    if( DEBUG_ENABLED )
	    {
		    //This will automatically allow all the debug methods in the application to function
		    Debug::initialization();
		    Debug::message("Request initiated");
		    Debug::setStartTime('application');
	    }

        $application = new Application( false );

        if( Debug::isCMD() )
            Application\UtilitiesV2\Container::add("application", $application );

        /**
         * Set the view path for flight
         */

        Flight::set('flight.views.path', Settings::setting("syscrack_view_location"));

        /**
         * Handles an error with the render engine
         */

        Flight::map('error', function(Error $error) use ( $application ){

            if( Settings::setting('error_logging') )
            {

                $application->getErrorHandler()->handleFlightError( $error );

                if( Settings::setting('error_display_page') )
                {

                    if( $_SERVER['REQUEST_URI'] == '/' )
                        Flight::redirect('/error?redirect=/index');
                    else
                        Flight::redirect('/error?redirect=' . $_SERVER['REQUEST_URI'] );
                }
                else
                {

                    Flight::redirect('/');
                }
            }
            else
            {

                Flight::notFound();
            }
        });

        /**
         * Maps the 'not found' page
         */

        Flight::map('notFound', function(){

            Flight::redirect('/framework/error/notfound/');
        });

        /**
         * Map our time end
         */

        Flight::before('start', function ()
        {

	        if ( DEBUG_ENABLED )
		        Debug::setStartTime('flight_route');

            define('SYSCRACK_TIME_END', microtime( true ) );
        });

	    /**
	     * After start
	     */

	    Flight::after('start', function()
	    {
		    if( DEBUG_ENABLED  )
		    {
			    Debug::message("Request Complete");
			    Debug::setEndTime('flight_route' );
			    if( DEBUG_WRITE_FILE )
			    {
				    Debug::stashMessages();
				    Debug::stashTimers();
			    }
		    }
	    });

	    Flight::before('redirect', function()
	    {

	        Debug::message('Redirected to: ' . Application\Render::$last_redirect );
	    });

        /**
         * Starts the applications controllers
         */

        try
        {

            if( Debug::isCMD() == false )
            {


                $application->runController();

                /**
                 * Set the application to be global
                 */

                $application->addToGlobalContainer();

                /**
                 * Set the application to be global
                 */

                $application->runFlight();
            }
            else
                Debug::echo("Syscrack has loaded but did not start the engine due to being in command line interface mode");
        }
        catch( Exception $error )
        {

            if( Settings::setting('error_logging') )
            {

                $application->getErrorHandler()->handleError( $error );

                if( Settings::setting('error_display_page') )
                {

                    if( $_SERVER['REQUEST_URI'] == '/' )
                        Flight::redirect('/error?redirect=/index');
                    else
                        Flight::redirect('/error?redirect=' . $_SERVER['REQUEST_URI'] );
                }
                else
                {

                    Flight::redirect('/');
                }
            }
            else
            {

                Flight::notFound();
            }
        }
    }
    catch( Exception $error )
    {

        ob_clean();

        ?>

            <html>
                <head>
                    <meta charset="utf-8">
                    <meta http-equiv="X-UA-Compatible" content="IE=edge">
                    <meta name="viewport" content="width=device-width, initial-scale=1">

                    <title>Critical Error</title>

                    <!-- Stylesheets -->
                    <link href="/themes/alpha/css/bootstrap.dark.css" rel="stylesheet">
                    <link href="/themes/alpha/css/bootstrap-combobox.css" rel="stylesheet">

                    <!--[if lt IE 9]>
                    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
                    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
                    <![endif]-->
                </head>
                <body>
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-12">
                                <h5 style="color: #ababab" class="text-uppercase text-center">
                                    Critical Error
                                </h5>
                                <div class="panel panel-danger">
                                    <div class="panel-heading">
                                        <?=$error->getMessage()?> @ <?=$error->getFile()?> line <?=$error->getLine()?>
                                    </div>
                                    <div class="panel-body text-center">
                                        <p>
                                            An error occurred outside of the framework, this is usually due to a permission error, a rewrite error, or something completely different, check out the error stack below.
                                        </p>

                                        <div class="well">
<pre>
    <?=$error->getTraceAsString()?>
</pre>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </body>
            </html>
        <?php
    }
//</editor-fold>

//And that's all folks
Debug::setEndTime('application');
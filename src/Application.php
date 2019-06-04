<?php

	namespace Framework;

	/**
	 * Lewis Lancaster 2016
	 *
	 * Class Application
	 *
	 * @package Framework
	 */

	use Flight;
	use Framework\Application\UtilitiesV2\Container;
	use Framework\Application\ErrorHandler;
	use Framework\Application\Loader;
	use Framework\Application\UtilitiesV2\Debug;
	use Framework\Application\UtilitiesV2\Globals;
	use Framework\Application\UtilitiesV2\Scripts;
	use Framework\Exceptions\ApplicationException;
	use Framework\Application\Settings;
	use Framework\Views\Controller;
	use Framework\Application\UtilitiesV2\Format;
	use Framework\Application\Utilities\FileSystem;

	class Application
	{

		/**
		 * @var Loader
		 */

		protected static $loader;

		/**
		 * @var Controller
		 */

		protected static $controller;

		/**
		 * @var Scripts
		 */

		protected static $scripts;

		/**
		 * @var Globals
		 */

		protected static $globals;

		/**
		 * Application constructor.
		 *
		 * @param bool $autostart
		 */

		public function __construct($autostart = true)
		{

			if ($autostart == true)
				$this->go();
		}


		/**
		 * Gets a new error handler
		 *
		 * @return ErrorHandler
		 */

		public function getErrorHandler()
		{

			return new ErrorHandler();
		}


		/**
		 * @return Scripts
		 */

		public function getScripts()
		{

			return self::$scripts;
		}

		/**
		 * Gets the loader
		 *
		 * @return Loader
		 */

		public function getLoader()
		{

			return self::$loader;
		}

		/**
		 * Gets the controller
		 *
		 * @return Controller
		 */

		public function getController()
		{

			return self::$controller;
		}

		/**
		 * Setup
		 */

		public static function setup()
		{

			Flight::set('flight.views.path', SYSCRACK_ROOT );
			Flight::map('error', function( \Error $error)
			{

				if( Container::exist("application") == false )
					die("Unable to disclose error correctly due to lack of application global instance");
				else
					$application = Container::get("application");

				if( Settings::setting('error_logging') )
				{

					$application->getErrorHandler()->handleFlightError( $error );

					if( Settings::setting('error_display_page') )
						if( $_SERVER['REQUEST_URI'] == '/' )
							Flight::redirect('/error?redirect=/index');
						else
							Flight::redirect('/error?redirect=' . $_SERVER['REQUEST_URI'] );
					else
						Flight::redirect('/');

				}
				else
					Flight::notFound();

			});
			Flight::map('notFound', function(){

				Flight::redirect('/framework/error/notfound/');
			});
			Flight::after('start', function()
			{

				if( Application::globals()->DEBUG_ENABLED  )
				{
					Debug::message("Request Complete");

					if( Application::globals()->DEBUG_WRITE_FILE )
						Debug::stashMessages();
				}
			});
		}

		/**
		 * Adds the application to the global container
		 */

		public function addToGlobalContainer()
		{

			if (Container::exist('application'))
				throw new ApplicationException();


			Container::add('application', $this);
		}

		/**
		 * Runs the controller and flight engine
		 */

		public function go()
		{

			$this->executeLoader();

			try
			{
				$this->addToGlobalContainer();

				if( Debug::isCMD() == false )
				{

					$this->runController();

					define('SYSCRACK_TIME_END', microtime( true ) );
					$this->runFlight();
				}
				else
					Debug::echo("Flight engine halted due to CMD mode being active. Syscrack has loaded successfully!");
			}
			catch( \Error $error )
			{

				if( Settings::setting('error_logging') )
				{

					$this->getErrorHandler()->handleError( $error );

					if( Settings::setting('error_display_page') )
						if( $_SERVER['REQUEST_URI'] == '/' )
							Flight::redirect('/error?redirect=/index');
						else
							Flight::redirect('/error?redirect=' . $_SERVER['REQUEST_URI'] );
					else
						Flight::redirect('/');

				}
				else
					Flight::notFound();
			}
			catch ( \RuntimeException $error )
			{

				if( Settings::setting('error_logging') )
				{

					$this->getErrorHandler()->handleError( $error );

					if( Settings::setting('error_display_page') )
						if( $_SERVER['REQUEST_URI'] == '/' )
							Flight::redirect('/error?redirect=/index');
						else
							Flight::redirect('/error?redirect=' . $_SERVER['REQUEST_URI'] );
					else
						Flight::redirect('/');

				}
				else
					Flight::notFound();
			}
			finally
			{

				Debug::message("Finished application loading");
			}
		}

		/**
		 * @param array $argv
		 * @param null $session
		 * @param bool $splash
		 *
		 * @return bool
		 */

		public function cli( array $argv = [], $session=null, $splash=true )
		{

			try
			{

				if( Settings::canFindSettings() == false )
					die("Cannot find settings file! Please check the following information is correct"
						. "<br>Root: " . SYSCRACK_ROOT
						. "<br>CWD: " . getcwd()
						. "<br>Document Root: " . $_SERVER["DOCUMENT_ROOT"]
						. "<br>Settings file: " . SYSCRACK_ROOT . FileSystem::separate("data","config","settings.json" )
						. "<br>If you are still struggling with this error. Please post an issue on our official github page."
						. "<br><br>https://github.com/dialtoneuk/syscrack"
					);
				elseif( empty( Settings::settings() ) )
					Settings::setup();

				if( $splash )
					$this->splash();

				if( empty( $argv ) )
					$argv[] = self::$globals->CLI_DEFAULT_COMMAND;
				elseif( count( $argv ) == 1 )
					$argv[] = self::$globals->CLI_DEFAULT_COMMAND;

				if( isset( self::$scripts ) == false )
					self::$scripts = new Scripts( $argv );

				$script = self::$scripts->script();

				if( $session == null )
					if( isset( $_SERVER["REQUEST_TIME"] ) )
						Debug::session( $_SERVER["REQUEST_TIME"] );
					else
						Debug::session( time() );

				if( self::$scripts->exists( $script ) == false )
					throw new \Error("script " . $script  . " does not exist");

				$result = self::$scripts->execute( $script );

				Debug::echo("\n Finished execution for " . $script
					. " started at " . Format::timestamp( @$_SERVER["REQUEST_TIME"] )
					. " and finished at " . Format::timestamp()
					. " with exit code " . (int)@$result
				);

				return( $result );
			}
			catch ( \Error $error )
			{

				@$this->getErrorHandler()->handleError( $error );
			}
			finally
			{

				Debug::stashOutput();
				Debug::stashMessages();
				Debug::stashTimers();
			}

			return( false );
		}

		/**
		 * prepares
		 */

		public function prepare()
		{

			if( empty( $argv ) )
				$argv[] = self::$globals->CLI_DEFAULT_COMMAND;
			elseif( count( $argv ) == 1 )
				$argv[] = self::$globals->CLI_DEFAULT_COMMAND;

			self::$scripts = new Scripts( $argv );
		}

		/**
		 * @param callable|null $callback
		 */

		public function executeLoader( callable $callback = null )
		{

			if( isset( self::$loader ) == false )
				self::$loader = $this->createLoader();

			try
			{
				self::$loader->loadPaypload();
			}
			catch (\Error $error)
			{

				if (Debug::isCMD())
					Debug::echo("[ ERROR IN LOADER ] " . $error->getMessage());
				else
					throw $error;
			}
			finally
			{

				if( empty( $callback ) == false )
					$callback();
			}
		}


		/**
		 * Runs flight engine
		 */

		public function runFlight()
		{

			Flight::start();
		}

		/**
		 * Runs the controller
		 */

		public function runController()
		{

			if( isset( self::$controller ) == false )
				self::$controller = $this->createController();

			self::$controller->run();
		}

		/**
		 * @param bool $log
		 */

		public static function block( $log=true )
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

						if( $log )
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
					}
					catch ( \Error  $error )
					{

						//Just secret exit
					}
					catch ( \RuntimeException $error )
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

		/**
		 * Application globals
		 */

		public static function defineGlobals()
		{

			$globals = [
					["SYSCRACK_URL_ROOT", "/"],
					["SYSCRACK_NAMESPACE_ROOT", "Framework\\"],
					["SYSCRACK_URL_ADDRESS", "http://localhost"],
					["SYSCRACK_VERSION_PHASE","alpha"],
					["SYSCRACK_VERSION_NUMBER","0.1.5"],
					//Framework
					["FRAMEWORK_BASECLASS", "base"],  //This ties to a few of the instance builders and won't build a direct instance of any class called "Base". This is useful in the
					//Util namespace for not directly invoking my interfaces and leaving them easily modifiable. If you want to load the base, even
					//though it could potentially crash the system. Change "base" to "".
					//Website
					["WEBSITE_TITLE", "Syscrack"],
					["WEBSITE_JQUERY", "jquery-3.3.1.min.js"],
					["WEBSITE_BOOTSTRAP4", "bootstrap.js"],
					//User Accounts
					["ACCOUNT_PREFIX", "user"],
					["ACCOUNT_DIGITS", 8],
					["ACCOUNT_RND_MIN", 1],
					["ACCOUNT_RND_MAX", 8],
					["ACCOUNT_PASSWORD_MIN", 8],
					["ACCOUNT_PASSWORD_STRICT", false ],
					//Tracks
					["TRACK_PRIVACY_PUBLIC", "public"],
					["TRACK_PRIVACY_PRIVATE", "private"],
					["TRACK_PRIVACY_PERSONAL", "personal"],
					["TRACK_PREFIX", "track"],
					["TRACK_NAME_MAXLENGTH", 64],
					["TRACK_DIGITS", 12],
					["TRACK_RND_MIN", 0],
					["TRACK_RND_MAX", 9],
					//Global Upload Settings
					["UPLOADS_TEMPORARY_DIRECTORY", "files/temp/"],
					["UPLOADS_FILEPATH","src/Framework/Uploads/"],
					["UPLOADS_NAMESPACE","Framework\\Application\\Uploads\\"],
					["UPLOADS_LOCAL", true ], //Will keep files in the temporary directory instead of uploading them ( should only be used for testing )
					["UPLOADS_WAVEFORMS_LOCAL", true ], //Will keep wave forms in the temporary directory instead of uploading them. Use with global above to completely turn off the data handler.
					["UPLOADS_POST_KEY", "track"],
					["UPLOADS_MAX_SIZE_GLOBAL", 500], //Used to define the max applicable size anybody can upload
					["UPLOADS_ERROR_NOT_FOUND", 1 ],
					["UPLOADS_ERROR_FILENAME", 2 ],
					["UPLOADS_ERROR_EXTENSION", 3 ],
					["UPLOADS_ERROR_TOO_LARGE", 4 ],
					["UPLOADS_ERROR_CANCELLED", 5 ],
					//Scripts
					["SCRIPTS_ROOT","src/Application/UtilitiesV2/Scripts/"],
					["SCRIPTS_NAMESPACE", "Framework\\Application\\UtilitiesV2\\Scripts\\"],
					["SCRIPTS_REQUIRE_CMD", true ],
					//FFMPEG
					["FFMPEG_CONFIG_FILE","data/config/ffmpeg.json"],
					//Verification
					["VERIFICATIONS_NAMESPACE", "Framework\\Application\\Verifications\\"],
					["VERIFICATIONS_ROOT", "src/Framework/Verifications/"],
					["VERIFICATIONS_TYPE_EMAIL", "email"],
					["VERIFICATIONS_TYPE_MOBILE", "mobile"],
					//Amazon
					["AMAZON_BUCKET_URL", "https://s3.eu-west-2.amazonaws.com/colourspace/"],
					["AMAZON_CREDENTIALS_FILE", "data/config/storage/amazon.json"],
					["AMAZON_S3_BUCKET", "colourspace"],
					["AMAZON_LOCATION_US_WEST", "us-west-1"],
					["AMAZON_LOCATION_US_WEST_2", "us-west-2"],
					["AMAZON_LOCATION_US_EAST", "us-east-1"],
					["AMAZON_LOCATION_US_EAST_2", "us-east-2"],
					["AMAZON_LOCATION_CA_CENTRAL", "ca-central-1"],
					["AMAZON_LOCATION_EU_WEST", "eu-west-1"],
					["AMAZON_LOCATION_EU_WEST_2", "eu-west-2"],
					["AMAZON_LOCATION_EU_CENTRAL", "eu-central-1"],
					//Google
					["GOOGLE_RECAPTCHA_ENABLED", false ],
					["GOOGLE_RECAPTCHA_CREDENTIALS", "data/config/google_recaptcha.json" ],
					["GOOGLE_CLOUD_CREDENTIALS",  "data/config/storage/google.json"],
					//Cloud Storage
					["STORAGE_CONFIG_ROOT","cdata/config/storage/"],
					["STORAGE_SETTINGS_FILE","settings.json"],
					//Syscrack
					['SYSCRACK_TIME_START', microtime( true ) ],
					//Flight
					["FLIGHT_JQUERY_FILE", "jquery-3.3.1.min.js"],
					["FLIGHT_CONTENT_OBJECT", true ], //Instead, convert $model into an object ( which is cleaner )
					["FLIGHT_MODEL_DEFINITION", "model" ],
					["FLIGHT_PAGE_DEFINITION", "page" ],
					["FLIGHT_SET_GLOBALS", true ],
					["FLIGHT_VIEW_FOLDER", "themes" ],
					///Twig
					["TWIG_VIEW_FOLDER", "themes"],
					//Setups
					["SETUP_ROOT", "src/Application/UtilitiesV2/Setups/"],
					["SETUP_NAMESPACE", "Framework\\Application\\UtilitiesV2\\Setups\\"],
					//MVC
					["MVC_NAMESPACE", "Framework\\Application\\MVC\\"],
					["MVC_NAMESPACE_MODELS", "Models"],
					["MVC_NAMESPACE_VIEWS", "Views"],
					["MVC_NAMESPACE_CONTROLLERS", "Controllers"],
					["MVC_TYPE_MODEL", "model"],
					["MVC_TYPE_VIEW", "view"],
					["MVC_TYPE_CONTROLLER", "controller"],
					["MVC_REQUEST_POST", "POST"],
					["MVC_REQUEST_GET", "GET"],
					["MVC_REQUEST_PUT", "PUT"],
					["MVC_REQUEST_DELETE", "DELETE"],
					['MVC_ROUTE_FILE', 'config/routes.json'],
					["MVC_ROOT", "src/Views/MVC/"],
					//Makers
					["MAKER_FILEPATH", "src/Application/UtilitiesV2/Makers/"],
					["MAKER_NAMESPACE", "Framework\\Application\\UtilitiesV2\\Makers\\"],
					//Pages
					["PAGE_SIZE", 6 ], //Default page size: 6 objects wide
					//Form
					["FORM_ERROR_GENERAL", "general_error"],
					["FORM_ERROR_INCORRECT", "incorrect_information"],
					["FORM_ERROR_MISSING", "missing_information"],
					["FORM_MESSAGE_SUCCESS", "success_message"],
					["FORM_MESSAGE_INFO", "info_message"],
					["FORM_DATA", "data"],
					//Resource combiner
					["RESOURCE_COMBINER_ROOT", "data/config/"],
					["RESOURCE_COMBINER_CHMOD", true ],
					["RESOURCE_COMBINER_CHMOD_PERM", 0755 ],
					["RESOURCE_COMBINER_PRETTY", true ],
					["RESOURCE_COMBINER_FILEPATH", "data/resources.bundle" ],
					//Fields
					["FIELD_TYPE_INCREMENTS","increments"],
					["FIELD_TYPE_STRING","string"],
					["FIELD_TYPE_INT","integer"],
					["FIELD_TYPE_PRIMARY","primary"],
					["FIELD_TYPE_TIMESTAMP","timestamp"],
					["FIELD_TYPE_DECIMAL","decimal"],
					["FIELD_TYPE_JSON","json"],
					["FIELD_TYPE_IPADDRESS","ipAddress"],
					//Columns
					["COLUMN_USERID", "userid"],
					["COLUMN_SESSIONID", "sessionid"],
					["COLUMN_CREATION", "creation"],
					["COLUMN_METAINFO", "metainfo"],
					["COLUMN_TRACKID", "trackid"],
					//Tables
					["TABLES_NAMESPACE", "Framework\\Database\\Tables\\"],
					["TABLES_ROOT", "src/Database/Tables/"],
					//Tests
					["TESTS_NAMESPACE", "Framework\\Application\\UtilitiesV2\\Tests\\"],
					["TESTS_ROOT", "src/Application/UtilitiesV2/Tests/"],
					//Audit (Moderation)
					["AUDIT_TYPE_BAN","ban"],
					["AUDIT_TYPE_WARNING","warning"],
					["AUDIT_TYPE_GROUPCHANGE","groupchange"],
					//Log
					["LOG_ROOT","data/config/"],
					["LOG_TYPE_GENERAL", "general"],
					["LOG_TYPE_WARNING", "warning"],
					["LOG_TYPE_DEFAULT", "default"],
					//Auto Execute
					["AUTOEXEC_ROOT", "src/Application/UtilitiesV2/AutoExecs/"],
					["AUTOEXEC_NAMESPACE", "Framework\\Application\\UtilitiesV2\\AutoExecs\\"],
					["AUTOEXEC_SCRIPTS_ROOT","resources/scripts/"],
					["AUTOEXEC_LOG_REFRESH", 12 ], //In hours
					["AUTOEXEC_LOG_LOCATION","data/config/log/"],
					//Database Settings
					["DATABASE_ENCRYPTION", false],
					["DATABSAE_ENCRYPTION_KEY", null ], //Replace null with a string of a key to not use a rand gen key.
					["DATABASE_CREDENTIALS", "data/config/database/connection.json"],
					["DATABASE_MAP", "data/config/database/databaseschema.json"],
					//Groups
					["GROUPS_ROOT", "data/config/groups/"],
					["GROUPS_DEFAULT", "default"],
					["GROUPS_FLAG_MAXLENGTH", "uploadmaxlength"],
					["GROUPS_FLAG_MAXSIZE", "uploadmaxsize"],
					["GROUPS_FLAG_LOSSLESS", "lossless"],
					["GROUPS_FLAG_ADMIN", "admin"],
					["GROUPS_FLAG_DEVELOPER", "developer"],
					//User Permissions
					["USER_PERMISSIONS_ROOT", "data/config/user/"],
					//Featured
					["FEATURED_ROOT", "data/featured/"],
					["FEATURED_ARTISTS", "artists"],
					["FEATURED_TRACKS", "tracks"],
					//Stream audio codec types
					["STREAMS_MP3", "mp3"],
					["STREAMS_FLAC", "flac"],
					["STREAMS_OGG", "ogg"],
					["STREAMS_WAV", "wav"],
					//Debugging Options
					["DEBUG_ENABLED", true ], //Will write debug messages and echo them inside the terminal instance
					["DEBUG_WRITE_FILE", true ],
					["DEBUG_MESSAGES_FILE", 'data/cli/messages.json'],
					["DEBUG_TIMERS_FILE", 'data/cli/timers.json'],
					//Mailer
					["MAILER_CONFIGURATION_FILE", "data/config/templates.json"],
					["MAILER_TEMPLATES_ROOT", "resources/email/"],
					["MAILER_IS_HTML", true ],
					["MAILER_IS_SMTP", true ],
					["MAILER_FROM_ADDRESS", "user00000001@Syscrack.io" ],
					["MAILER_FROM_USER", "user00000001" ],
					["MAILER_CONTACT_ADDRESS", "support@Syscrack.io" ],
					["MAILER_VERIFY_TEMPLATE", "email" ],
					["MAILER_BANNED_TEMPLATE", "banned" ],
					["MAILER_REMOVED_TEMPLATE", "removed" ],
					["MAILER_POSTED_TEMPLATE", "posted" ],
					["MAILER_COMMENTS_TEMPLATE", "comments" ],
					//Javascript Builder
					["SCRIPT_BUILDER_ENABLED", true ], //It isnt recommended you turn this on unless your compiled.js for some reason is missing or you are developing.
					["SCRIPT_BUILDER_ROOT", "resources/scripts/"],
					["SCRIPT_BUILDER_FREQUENCY", 60 * 60 * 2], //Change the last digit for hours. Remove a "* 60" for minutes.
					["SCRIPT_BUILDER_COMPILED", "compiled.js"],
					["SCRIPT_BUILDER_FORCED", false ],//Compiles a fresh build each request regardless of frequency setting.
					//Misc
					["COLLECTOR_DEFAULT_NAMESPACE", "Framework\\Application\\"],
					//Colours
					["COLOURS_OUTPUT_HEX", 1],
					["COLOURS_OUTPUT_RGB", 2],
					//Shop
					["SHOP_ROOT","src/Framework/Items/"],
					["SHOP_NAMESPACE","Framework\\Application\\Items\\"],
					["SHOP_INVENTORY","data/config/shop/items.json"],
					//Balance
					["BALANCE_DEFAULT_AMOUNT", 100 ],
					//Transactions
					["TRANSACTION_TYPE_WITHDRAW", "withdraw" ],
					["TRANSACTION_TYPE_DEPOSIT", "deposit" ],
					["MIGRATOR_ROOT", "src/Application/UtilitiesV2/Migrators/"],
					["MIGRATOR_NAMESPACE","Framework\\Application\\UtilitiesV2\\Migrators\\"],
					//PHP Unit
					["PHPUNIT_FINISHED", true ],
					//CLI
					["CLI_DEFAULT_COMMAND","instance"]
				];

			foreach( $globals as $value )
				define( $value[0], $value[1] );

			if( isset( self::$globals ) == false )
				self::$globals = new Globals( $globals );
		}

		/**
		 * @return Globals
		 */

		public static function globals(): Globals
		{

			returN( self::$globals );
		}

		/**
		 * @return Loader
		 */

		private function createLoader()
		{

			return new Loader();
		}

		/**
		 * Creates the controller
		 *
		 * @return Controller
		 */

		private function createController()
		{

			return new Controller();
		}

		/**
		 * Splashscreen
		 */

		private function splash()
		{

			Debug::echo("\n   oooooooo8                                                                  oooo           ", 1);
			Debug::echo("888       oooo   oooo oooooooo8    ooooooo  oo oooooo   ooooooo    ooooooo   888  ooooo    ", 1);
			Debug::echo(" 888oooooo 888   888 888ooooooo  888     888 888    888 ooooo888 888     888 888o888       ", 1);
			Debug::echo("        888 888 888          888 888         888      888    888 888         8888 88o      ", 1);
			Debug::echo("o88oooo888    8888   88oooooo88    88ooo888 o888o      88ooo88 8o  88ooo888 o888o o888o    ", 1);
			Debug::echo("           o8o888  Written by Lewis Lancaster (03/06/2019)                                 ", 1);
		}
	}
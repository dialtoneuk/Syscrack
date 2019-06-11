<?php
	declare(strict_types=1);

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
	use Framework\Application\Settings;
	use Framework\Views\Controller;
	use Framework\Application\UtilitiesV2\Format;
	use Framework\Application\Utilities\FileSystem;

	/**
	 * Class Application
	 * @package Framework
	 */
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
			Flight::map('error', function( $error )
			{

				if( Container::exist("application") == false )
					echo("Unable to disclose error correctly due to lack of application global instance" . "<br>" . @print_r( $error ) );
				else
				{

					$application = Container::get("application");

					if( Settings::setting('error_logging') )
					{

						$application->getErrorHandler()->handleFlightError( $error );

						if( Settings::setting('error_display_page') )
						{

							if( $_SERVER['REQUEST_URI'] == '/' )
								Flight::redirect('/error?redirect=/index');
							else
								if( isset( $_GET["redirect"] ) == false )
									Flight::redirect('/error?redirect=' . htmlspecialchars( $_SERVER['REQUEST_URI'] ) );
								else
									Flight::redirect('/error?redirect=' . htmlspecialchars( $_GET["redirect"] ) );
						}
						else
							Flight::redirect('/');

					}
					else
						Flight::notFound();
				}
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
			{

				if( Debug::isPHPUnitTest() == false )
					throw new \Error();
			}
			else
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


							if( isset( $_SERVER["REQUEST_URI"] ) && empty( $_SERVER["REQUEST_URI"] && @$_SERVER["REQUEST_URI"] !== "/execute.php") == false && strlen( @$_SERVER["REQUEST_URI"] ) < 128 )
								$message .= "user attempted to execute: " . addslashes( @$_SERVER["REQUEST_URI"] ) . "\n";

							//Writes the file but only if there isn't a dupe
							if( strpos( $contents, $message ) === false )
								/** @noinspection PhpUnusedLocalVariableInspection */
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

			$path = FileSystem::separate("resources","includes","globals.module");

			if( FileSystem::exists($path) == false )
				throw new \Error("Globals not found in: " .  FileSystem::getFilePath( $path ) );
			else
				$globals = include( FileSystem::getFilePath( $path ) );

			foreach( $globals as $value )
				if( defined( $value[0] ) == false )
				{

					if( isset( self::$globals ) )
						Debug::message("defining " . $value[0] . " with value of " . $value[1] );

					define( $value[0], $value[1] );
				}
				elseif( isset( self::$globals ) )
				{

					$current_globals = Application::globals()->all();

					if( $current_globals[ $value[0] ] !== $value[1] )
						unset( $GLOBALS[ $value[0] ] );

					@define( $value[0], $value[1] );
				}

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
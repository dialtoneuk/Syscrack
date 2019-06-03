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
	use Framework\Application\Container;
	use Framework\Application\ErrorHandler;
	use Framework\Application\Loader;
	use Framework\Application\UtilitiesV2\Debug;
	use Framework\Exceptions\ApplicationException;
	use Framework\Application\Settings;
	use Framework\Views\Controller;

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

				if( Settings::setting('error_logging') )
				{

					$this->getErrorHandler()->handleFlightError( $error );

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

				if( DEBUG_ENABLED  )
				{
					Debug::message("Request Complete");

					if( DEBUG_WRITE_FILE )
						Debug::stashMessages();
				}
			});
		}

		/**
		 * Adds the application to the global container
		 */

		public function addToGlobalContainer()
		{

			if (Container::hasObject('application'))
				throw new ApplicationException();


			Container::setObject('application', $this);
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
	}
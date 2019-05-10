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
	use Framework\Views\Controller;

	class Application
	{

		/**
		 * @var Loader
		 */

		protected $loader;

		/**
		 * @var Controller
		 */

		protected $controller;

		/**
		 * Application constructor.
		 */

		public function __construct($autostart = true)
		{

			$this->initialize();

			if ($autostart == true)
			{

				$this->runController();

				$this->runFlight();
			}
		}

		/**
		 * Initializes the Application
		 */

		public function initialize()
		{

			$this->loader = $this->createLoader();

			$this->controller = $this->createController();
		}

		/**
		 * Adds the application to the global container
		 */

		public function addToGlobalContainer()
		{

			if (Container::hasObject('application'))
			{

				throw new ApplicationException();
			}

			Container::setObject('application', $this);
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

			$this->controller->run();
		}

		/**
		 * Gets the loader
		 *
		 * @return Loader
		 */

		public function getLoader()
		{

			return $this->loader;
		}

		/**
		 * Gets the controller
		 *
		 * @return Controller
		 */

		public function getController()
		{

			return $this->controller;
		}

		/**
		 * Creates the loader
		 *
		 * @param null $callback
		 *
		 * @return Loader
		 */

		private function createLoader($callback = null)
		{

			$loader = new Loader();

			try
			{

				$loader->loadPaypload();
			} catch (\RuntimeException $error)
			{

				if (Debug::isCMD())
					Debug::echo("[ ERROR IN LOADER ] " . $error->getMessage());
				else
					throw $error;
			}

			return $loader;
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
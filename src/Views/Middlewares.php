<?php

	namespace Framework\Views;

	/**
	 * Lewis Lancaster 2016
	 *
	 * Class Middlewares
	 *
	 * @package Framework\Views
	 */

	use Error;
	use Framework\Application\Settings;
	use Framework\Application\Utilities\Factory;
	use Framework\Application\Utilities\FileSystem;
	use Framework\Exceptions\ApplicationException;
	use Framework\Views\Structures\Middleware;

	class Middlewares
	{

		/**
		 * @var array
		 */

		protected $middlewares = [];

		/**
		 * @var array
		 */

		protected static $results = [];

		/**
		 * @var Factory
		 */

		protected $factory;

		/**
		 * Middlewares constructor.
		 *
		 * @param string $namespace
		 *
		 * @param bool $auto
		 */

		public function __construct($namespace = null, $auto = true)
		{

			if ($namespace == null)
			{

				$namespace = Settings::setting('middlewares_namespace');
			}

			$this->factory = new Factory($namespace);

			if ($auto)
			{

				$this->loadMiddleware();
			}
		}

		/**
		 * Loads the middlewares
		 *
		 * @return bool
		 */

		public function loadMiddleware()
		{

			try
			{

				$this->getMiddlewares();
			} catch (\Error $error)
			{

				return false;
			}

			return true;
		}

		/**
		 * Processes the middlewares
		 */

		public function processMiddlewares()
		{

			foreach ($this->middlewares as $middleware)
			{

				try
				{

					$class = $this->factory->createClass($middleware);

					if ($class instanceof Middleware == false)
					{

						throw new ApplicationException();
					}

					if ($class->onRequest())
					{

						self::addToResults($middleware, true);

						$class->onSuccess();
					}
					else
					{

						self::addToResults($middleware, false);

						$class->onFailure();
					}
				} catch (Error $error)
				{

					self::addToResults($middleware, 'error');

					continue;
				}
			}
		}

		/**
		 * Returns if the middlewares have loaded
		 *
		 * @return bool
		 */

		public function isLoaded()
		{

			if (empty($this->middlewares))
			{

				return false;
			}

			return true;
		}

		/**
		 * Returns true if there are any middlewares
		 *
		 * @return bool
		 */

		public function hasMiddlewares()
		{

			if (FileSystem::getFilesInDirectory(Settings::setting('middlewares_location')) == null)
			{

				return false;
			}

			return true;
		}

		/**
		 * Gets all the results from the middlewares result array
		 *
		 * @return array
		 */

		public static function getResults()
		{

			return self::$results;
		}

		/**
		 * Gets a result
		 *
		 * @param $middleware
		 *
		 * @return mixed
		 */

		public static function getResult($middleware)
		{

			if (self::$results[strtolower($middleware)] == null)
			{

				return false;
			}

			return self::$results[strtolower($middleware)];
		}

		/**
		 * Adds a middleware to the results array
		 *
		 * @param $middleware
		 *
		 * @param bool $result
		 */

		public static function addToResults($middleware, $result)
		{

			self::$results[strtolower($middleware)] = $result;
		}

		/**
		 * Returns true if this middleware has an error
		 *
		 * @param $middleware
		 *
		 * @return bool
		 */

		public static function hasError($middleware)
		{

			if (self::$results[strtolower($middleware)] == 'error')
			{

				return true;
			}

			return false;
		}

		/**
		 * Gets the middlewares and sets the internal class array
		 */

		private function getMiddlewares()
		{

			$middlewares = FileSystem::getFilesInDirectory(Settings::setting('middlewares_location'));

			if (empty($middlewares))
			{

				throw new ApplicationException();
			}

			$middlewares = $this->format($middlewares);

			if (empty($middlewares))
			{

				throw new ApplicationException();
			}

			$this->middlewares = $middlewares;
		}

		/**
		 * Correctly formates the files ( by removing the extension ) to be passed to the factory
		 *
		 * @param $files
		 *
		 * @return array
		 */

		private function format($files)
		{

			$array = array();

			foreach ($files as $file)
			{

				$array[] = FileSystem::getFileName($file);

			}

			return $array;
		}
	}
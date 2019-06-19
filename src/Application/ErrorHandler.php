<?php
	declare(strict_types=1);

	namespace Framework\Application;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class ErrorHandler
	 *
	 * @package Framework\Application
	 */

	use Error;
	use Framework\Application\Utilities\FileSystem;
	use Framework\Application\Utilities\IPAddress;
	use Framework\Application\UtilitiesV2\Debug;


	/**
	 * Class ErrorHandler
	 * @package Framework\Application
	 */
	class ErrorHandler
	{

		/**
		 * Holds the active log file
		 *
		 * @var array|mixed
		 */

		protected $error_log = [];

		/**
		 * ErrorHandler constructor.
		 *
		 * @param bool $autoload
		 */

		public function __construct($autoload = true)
		{

			if ($autoload == true)
				if ($this->hasLogFile())
					if( Debug::isCMD() == false )
						$this->error_log = $this->getLogFile();

		}

		/**
		 * Handles the errors of the application
		 *
		 * @param $error
		 */

		public function handleError( $error, string $type="application_error" )
		{

			try
			{

				if( $error instanceof Error
					|| $error instanceof \RuntimeException
					|| $error instanceof \ErrorException
					|| $error instanceof \Exception )
				{

					$array = [
						'message' => $error->getMessage(),
						'type' => @get_class( $error ),
						'details' => [
							'url' => $_SERVER['REQUEST_URI'],
							'line' => $error->getLine(),
							'file' => $error->getFile(),
							'trace' => $error->getTraceAsString()
						]
					];

					Debug::message("Caught error: " . $array["message"] );
					Debug::message( $array["type"]  );
					Debug::message( print_r( $array["details"] )  );
				}
				else
				{

					Debug::message("Error is not instance of \\Errror or \\RuntimeException");

					$array = [
						'type' => $type,
						'message' => @get_class( $error ),
						'details' => [
							'url' => $_SERVER['REQUEST_URI'],
						]
					];
				}

				$this->addToLog($array);

				if (Settings::setting('error_logging') && Debug::isCMD() == false )
					$this->saveErrors();
			}
			catch ( \Error $error  )
			{

				die( print_r( $error ) );
			}
			catch ( \RuntimeException $error )
			{

				die( print_r( $error ) );
			}
			catch ( \Exception $error )
			{

				die( print_r( $error) );
			}
			catch ( \ErrorException $error )
			{

				die( print_r( $error ) );
			}
		}

		/**
		 * @param $error
		 * @param string $type
		 */

		public function handleFlightError( $error, string $type="flight_error")
		{

			try
			{

				$array = [
					'message' => $error->getMessage(),
					'type' => @get_class( $error ),
					'details' => [
						'url' => $_SERVER['REQUEST_URI'],
						'line' => $error->getLine(),
						'file' => $error->getFile(),
						'trace' => $error->getTraceAsString()
					]
				];

				Debug::message("Caught error: " . $array["message"] );
				Debug::message( $array["type"]  );
				Debug::message( $array["details"] );

				$this->addToLog($array);

				if (Settings::setting('error_logging') && Debug::isCMD() == false )
					$this->saveErrors();
			}
			catch ( \Error $error  )
			{

				die("<pre>" . print_r( $error ) . "</pre>" );
			}
			catch ( \RuntimeException $error )
			{

				die("<pre>" . print_r( $error ) . "</pre>" );
			}
			catch ( \Exception $error )
			{

				die("<pre>" . print_r( $error ) . "</pre>" );
			}
			catch ( \ErrorException $error )
			{

				die("<pre>" . print_r( $error ) . "</pre>" );
			}
		}

		/**
		 * Gets the error log
		 *
		 * @return array|mixed
		 */

		public function getErrorLog()
		{

			return $this->error_log;
		}

		/**
		 * Reads the error log from file
		 *
		 * @return array
		 */

		public function readErrorLog()
		{

			if( Debug::isCMD() )
				return [];

			if( FileSystem::exists( $this->getFileLocation()) == false )
				return [];

			$file = FileSystem::read($this->getFileLocation());

			if (empty($file))
			{

				throw new \Error();
			}

			return json_decode($file, true);
		}

		/**
		 * Deletes the error log
		 */

		public function deleteErrorLog()
		{

			FileSystem::delete($this->getFileLocation());
		}

		/**
		 * Saves our errors to file
		 */

		public function saveErrors()
		{

			if ($this->hasLogFile() == false)
				FileSystem::write($this->getFileLocation(), json_encode($this->getErrorLog(), JSON_PRETTY_PRINT));
			else
				FileSystem::append($this->getFileLocation(), json_encode($this->getErrorLog(), JSON_PRETTY_PRINT));
		}

		/**
		 * Handles the log file
		 *
		 * @return bool
		 */

		public function hasLogFile()
		{

			if (FileSystem::exists($this->getFileLocation()))
				return true;

			return false;
		}

		/**
		 * Gets the last error ( used by the error page )
		 *
		 * @return mixed
		 */

		public function getLastError()
		{

			$log = null;

			if (empty($this->error_log))
				$log = $this->readErrorLog();
			else
				$log = $this->error_log;

			return end($log);
		}

		/**
		 * Returns true if we have errors
		 *
		 * @return bool
		 */

		public function hasErrors()
		{

			$log = null;

			if (empty($this->error_log))
				$log = $this->readErrorLog();
			else
				$log = $this->error_log;

			if (empty($log))
			{

				return false;
			}

			return true;
		}

		/**
		 * Adds to the log
		 *
		 * @param array $array
		 */

		private function addToLog(array $array)
		{

			$data = [
				'timestamp' => time(),
				'ip' => $this->getIP()
			];

			$this->error_log[] = array_merge($array, $data);
		}

		/**
		 * Gets the log file
		 *
		 * @return mixed
		 */

		private function getLogFile()
		{

			$file = FileSystem::read(self::getFileLocation());

			if (empty($file))
			{

				throw new \Error();
			}

			return json_decode($file, true);
		}

		/**
		 * Gets the error creators remote address
		 *
		 * @return string
		 */

		private function getIP()
		{

			return IPAddress::getAddress();
		}

		/**
		 * Gets the file location
		 *
		 * @return mixed
		 */

		private function getFileLocation()
		{

			return Settings::setting('error_log_location');
		}
	}
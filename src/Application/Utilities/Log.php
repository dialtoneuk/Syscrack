<?php
	declare(strict_types=1);

	namespace Framework\Application\Utilities;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class Log
	 *
	 * @package Framework\Application
	 */

	use Framework\Application\Settings;


	/**
	 * Class Log
	 * @package Framework\Application\Utilities
	 */
	class Log
	{

		/**
		 * Holds the current active log
		 *
		 * @var array
		 */

		protected static $active_log = [];

		/**
		 * @var bool
		 */

		public static $disabled = false;

		/**
		 * Logs the message
		 *
		 * @param $message
		 *
		 * @param string $type
		 */

		public static function log($message, $type = 'notice')
		{

			if (is_string($message) == false)
			{

				throw new \Error();
			}

			self::addToActiveLog(['message' => $message, 'type' => $type]);
		}

		/**
		 * Saves the log to file
		 */

		public static function saveLogToFile()
		{

			FileSystem::writeJson(Settings::setting('active_log_location'), self::getActiveLog());
		}

		/**
		 * Reads the active log file
		 *
		 * @return mixed
		 */

		public static function readActiveLog()
		{

			return FileSystem::readJson(Settings::setting('active_log_location'));
		}

		/**
		 * Gets the active log
		 *
		 * @return array
		 */

		public static function getActiveLog()
		{

			return self::$active_log;
		}

		/***
		 * Returns true if the active log isn't empty
		 *
		 * @return bool
		 */

		public static function hasActiveLog()
		{

			if (empty(self::$active_log))
			{

				return false;
			}

			return true;
		}

		/**
		 * Gets the active log to j-ay-son
		 *
		 * @return string
		 */

		public static function toJson()
		{

			return json_encode(self::getActiveLog(), JSON_PRETTY_PRINT);
		}

		/**
		 * Adds to the active log
		 *
		 * @param array $array
		 */

		private static function addToActiveLog(array $array)
		{

			$data = [
				'time' => time(),
				'date' => date('d-m-y'),
				'microtime' => microtime(true)
			];

			self::$active_log[] = array_merge($array, $data);
		}
	}
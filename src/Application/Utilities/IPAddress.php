<?php

	namespace Framework\Application\Utilities;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class IPAddress
	 *
	 * @package Framework\Application\Utilities
	 */
	class IPAddress
	{

		/**
		 * Gets the users IP Address
		 *
		 * @return string
		 */

		public static function getAddress()
		{

			if ($_SERVER['REMOTE_ADDR'] == "::1" || $_SERVER['REMOTE_ADDR'] == "localhost")
			{

				return gethostbyname(gethostname());
			}

			return $_SERVER['REMOTE_ADDR'];
		}
	}
<?php
	declare(strict_types=1);

	namespace Framework\Application\Api;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class Manager
	 *
	 * @package Framework\Application\Api
	 */

	use Framework\Database\Tables\Api;

	/**
	 * Class Manager
	 * @package Framework\Application\Api
	 */
	class Manager
	{

		protected $database;

		/**
		 * Manager constructor.
		 */

		public function __construct()
		{

			$this->database = new Api();
		}

		/**
		 * Returns true if the apikey is valid
		 *
		 * @param $apikey
		 *
		 * @return bool
		 */

		public function hasApiKey($apikey)
		{

			if ($this->database->getApiByKey($apikey) == null)
			{

				return false;
			}

			return true;
		}

		/**
		 * Returns true if the user has an API
		 *
		 * @param $userid
		 *
		 * @return bool
		 */

		public function userHasApi($userid)
		{

			if ($this->database->getApiByUser($userid) == null)
			{

				return false;
			}

			return true;
		}

		/**
		 * Gets an API via its key
		 *
		 * @param $apikey
		 *
		 * @return mixed
		 */

		public function getApi($apikey)
		{

			return $this->database->getApiByKey($apikey);
		}

		/**
		 * Gets the API by the user
		 *
		 * @param $userid
		 *
		 * @return \Illuminate\Support\Collection|null
		 */

		public function getUserApi($userid)
		{

			return $this->database->getApiByUser($userid);
		}
	}
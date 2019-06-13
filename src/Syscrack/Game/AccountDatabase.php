<?php
	declare(strict_types=1);

	namespace Framework\Syscrack\Game;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class AccountDatabase
	 *
	 * @package Framework\Syscrack\Game
	 */

	use Framework\Application\Settings;
	use Framework\Application\Utilities\FileSystem;

	/**
	 * Class AccountDatabase
	 * @package Framework\Syscrack\Game
	 */
	class AccountDatabase
	{

		/**
		 * @var mixed
		 */

		protected $database = [];

		/**
		 * @var null
		 */

		public $userid;

		/**
		 * AccountDatabase constructor.
		 *
		 * @param null $userid
		 *
		 * @param bool $autoload
		 */

		public function __construct($userid = null, $autoload = true)
		{

			if ($userid != null)
			{

				if ($autoload)
				{

					$this->database = $this->getDatabase($userid);

					$this->userid = $userid;
				}
			}
		}

		/**
		 * Gets the banks IP address
		 *
		 * @param $accountnumber
		 *
		 * @return null
		 */

		public function getBankIPAddress($accountnumber)
		{

			foreach ($this->database as $key => $value)
			{

				if ($value['accountnumber'] == $accountnumber)
				{

					return $value['ipaddress'];
				}
			}

			return null;
		}

		/**
		 * Adds an account number
		 *
		 * @param $accountnumber
		 *
		 * @param $ipaddress
		 *
		 * @param bool $save
		 */

		public function addAccountNumber($accountnumber, $ipaddress, $save = true)
		{

			$this->database[] = [
				'accountnumber' => $accountnumber,
				'ipaddress' => $ipaddress
			];

			if ($save == true)
			{

				$this->saveDatabase($this->userid, $this->database);
			}
		}

		/**
		 * Removes an account number from this list
		 *
		 * @param $accountnumber
		 *
		 * @param bool $save
		 */

		public function removeAccountNumber($accountnumber, $save = true)
		{

			foreach ($this->database as $key => $value)
			{

				if ($value['accountnumber'] == $accountnumber)
				{

					unset($this->database[$key]);
				}
			}

			if ($save == true)
			{

				$this->saveDatabase($this->userid, $this->database);
			}
		}

		/**
		 * Checks if this account number is in the database
		 *
		 * @param $accountnumber
		 *
		 * @return bool
		 */

		public function hasAccountNumber($accountnumber)
		{


			foreach ($this->database as $key => $value)
			{

				if ($value['accountnumber'] == $accountnumber)
				{

					return true;
				}
			}

			return false;
		}

		/**
		 * Returns true if the users database file exists
		 *
		 * @param $userid
		 *
		 * @return bool
		 */

		public function hasDatabase($userid)
		{

			if (FileSystem::exists($this->getFile($userid)) == false)
			{

				return false;
			}

			return true;
		}

		/**
		 * Loads the database
		 *
		 * @param $userid
		 */

		public function loadDatabase($userid)
		{

			$this->database = $this->getDatabase($userid);
		}

		/**
		 * Gets the bank database
		 *
		 * @param $userid
		 *
		 * @return mixed
		 */

		public function getDatabase($userid)
		{

			return FileSystem::readJson($this->getFile($userid));
		}

		/**
		 * Saves the database to file
		 *
		 * @param $userid
		 *
		 * @param array $data
		 */

		public function saveDatabase($userid = null, $data = [])
		{

			if ($userid == null)
			{

				$userid = $this->userid;
			}

			if (empty($data))
			{

				$data = $this->database;
			}

			FileSystem::writeJson($this->getFile($userid), $data);
		}

		/**
		 * Gets the file path
		 *
		 * @param $userid
		 *
		 * @return string
		 */

		public function getFile($userid)
		{

			return Settings::setting('accounts_location') . $userid .
				Settings::setting('json_extension');
		}
	}
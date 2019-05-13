<?php

	namespace Framework\Syscrack\Game\Bases;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class Operation
	 *
	 * @package Framework\Syscrack\Game
	 */

	use Framework\Application\Container;
	use Framework\Application\Render;
	use Framework\Application\Settings;
	use Framework\Application\Utilities\ArrayHelper;
	use Framework\Exceptions\SyscrackException;
	use Framework\Syscrack\Game\Computer;
	use Framework\Syscrack\Game\Finance;
	use Framework\Syscrack\Game\Hardware;
	use Framework\Syscrack\Game\Internet;
	use Framework\Syscrack\Game\Log;
	use Framework\Syscrack\Game\Software;
	use Framework\Syscrack\Game\Statistics;
	use Framework\Syscrack\Game\Interfaces\Operation;
	use Framework\Syscrack\Game\Utilities\TimeHelper;
	use Framework\Syscrack\User;
	use Illuminate\Support\Collection;

	class BaseOperation implements Operation
	{

		/**
		 * @var Log
		 */

		protected static $log;

		/**
		 * @var BaseSoftware
		 */

		protected static $software;

		/**
		 * @var BaseComputer
		 */

		protected static $computer;

		/**
		 * @var Internet
		 */

		protected static $internet;

		/**
		 * @var Hardware
		 */

		protected static $hardware;

		/**
		 * @var Statistics
		 */

		protected static $statistics;

		/**
		 * @var Finance;
		 */

		protected static $finance;

		/**
		 * @var User
		 */

		protected static $user;

		/**
		 * Operation constructor.
		 *
		 * @param bool $createclasses
		 */

		public function __construct($createclasses = true)
		{

			if ($createclasses)
			{

				if (isset(self::$log) == false)
					self::$log = new Log();

				if (isset(self::$software) == false)
					self::$software = new Software();

				if (isset(self::$computer) == false)
					self::$computer = new Computer();

				if (isset(self::$internet) == false)
					self::$internet = new Internet();

				if (isset(self::$hardware) == false)
					self::$hardware = new Hardware();

				if (isset(self::$statistics) == false)
					self::$statistics = new Statistics();

				if (isset(self::$finance) == false)
					self::$finance = new Finance();

				if (isset(self::$user) == false)
					self::$user = new User();
			}
		}

		/**
		 * @param $timecompleted
		 * @param $computerid
		 * @param $userid
		 * @param $process
		 * @param array $data
		 *
		 * @return bool
		 */

		public function onCreation($timecompleted, $computerid, $userid, $process, array $data)
		{

			return true;
		}

		/**
		 * @param $timecompleted
		 * @param $timestarted
		 * @param $computerid
		 * @param $userid
		 * @param $process
		 * @param array $data
		 *
		 * @return bool
		 */

		public function onCompletion($timecompleted, $timestarted, $computerid, $userid, $process, array $data)
		{

			return true;
		}

		/**
		 * @param $computerid
		 * @param $ipaddress
		 * @param null $softwareid
		 *
		 * @return int
		 */

		public function getCompletionSpeed($computerid, $ipaddress, $softwareid = null)
		{

			return 0;
		}

		/**
		 * @param $ipaddress
		 * @param $userid
		 *
		 * @return array
		 */

		public function getCustomData($ipaddress, $userid)
		{

			return ([]);
		}

		/**
		 * @param $data
		 * @param $ipaddress
		 * @param $userid
		 *
		 * @return bool
		 */

		public function onPost($data, $ipaddress, $userid)
		{

			return true;
		}

		/**
		 * @param null $ipaddress
		 *
		 * @return string
		 */

		public function url($ipaddress = null)
		{

			return ('game/internet/' . $ipaddress);
		}

		/**
		 * Gets the configuration of this operation
		 */

		public function configuration()
		{

			return array(
				'allowsoftware' => true,
				'allowlocal' => true,
				'allowanonymous' => false,
				'requiresoftware' => true,
				'requireloggedin' => true,
				'allowpost' => false,
				'allowcustomdata' => false,
				'jsonoutput' => false,
				'elevated' => false,
				'postrequirements' => []
			);
		}

		/**
		 * Checks if the computer has this software by its name
		 *
		 * @param $softwarename
		 *
		 * @param $computerid
		 *
		 * @param $installed
		 *
		 * @return bool
		 */

		public function hasSoftware($softwarename, $computerid, $installed = true)
		{

			$software = self::$computer->getComputerSoftware($computerid);

			foreach ($software as $key => $value)
			{
				if (self::$software->softwareExists($value['softwareid']) == false)
					continue;

				$software = self::$software->getSoftware($value['softwareid']);

				if ($software->softwarename == $softwarename)
					if ($installed)
						if ($software->installed == true)
							return true;
						else
							return true;
			}

			return false;
		}

		/**
		 * Returns true if to return all outputs in Json format for those post operations
		 *
		 * @return bool
		 */

		public function isJsonOutput()
		{

			if (isset($this->configuration()['jsonoutput']))
				if ($this->configuration()['jsonoutput'] == true)
					return true;
				else
					return false;
		}

		/**
		 * @return bool
		 */

		public function isElevated()
		{

			if (isset($this->configuration()['elevated']))
				if ($this->configuration()['elevated'] == true)
					return true;
				else
					return false;
		}

		/**
		 * Gets the highest level of software on the users computer
		 *
		 * @param $computerid
		 *
		 * @param null $type
		 *
		 * @return array|null
		 */

		public function getHighestLevelSoftware($computerid, $type = null)
		{

			if ($type == null)
				$type = Settings::setting('syscrack_software_cracker_type');

			$software = self::$computer->getComputerSoftware($computerid);

			if (empty($software))
				return null;


			$results = [];

			foreach ($software as $key => $value)
				if ($value['type'] == $type)
					if ($value['installed'] == true)
						$results[] = self::$software->getSoftware($value['softwareid']);

			if (empty($results))
				return null;

			$results = ArrayHelper::sortArray($results, 'level');

			if (is_array($results) == false)
				return (array)$results;

			return (array)$results[0];
		}

		/**
		 * Checks the data given to the operation and returns false is a requirement isn't set
		 *
		 * @param array $data
		 *
		 * @param array $requirements
		 *
		 * @return bool
		 */

		public function checkData(array $data, array $requirements = ['softwareid', 'ipaddress'])
		{

			foreach ($requirements as $requirement)
			{

				if (isset($data[$requirement]) == false)
					return false;

				if ($data[$requirement] == null || empty($data[$requirement]))
					return false;
			}

			return true;
		}

		/**
		 * Adds a log message to a computer
		 *
		 * @param $message
		 *
		 * @param $computerid
		 *
		 * @param $ipaddress
		 */

		public function logToComputer($message, $computerid, $ipaddress)
		{

			self::$log->updateLog($message, $computerid, $ipaddress);
		}

		/**
		 * Logs the actions on the personal players computer and the corresponding ip addresses computer
		 *
		 * @param $message
		 *
		 * @param $computerid
		 *
		 * @param $ipaddress
		 */

		public function logActions($message, $computerid, $ipaddress)
		{

			if (self::$computer->computerExists($computerid) == false)
				throw new SyscrackException();

			$victimid = self::$internet->getComputer($ipaddress);

			if (self::$log->hasLog($victimid->computerid) == false || self::$log->hasLog($computerid) == false)
				throw new SyscrackException();

			$this->logToComputer($message, $victimid->computerid, self::$computer->getComputer($computerid)->ipaddress);
			$this->logToComputer($message, $computerid, Settings::setting('syscrack_log_localhost_name'));
		}

		/**
		 * @var Collection
		 */

		protected static $cache;

		/**
		 * @return Collection
		 */

		public function currentComputer()
		{

			if (isset(self::$cache) == false)
				self::$cache = self::$computer->getComputer(self::$computer->computerid());

			return (self::$cache);
		}

		/**
		 * @param $file
		 * @param array|null $array
		 * @param bool $default_sets
		 * @param bool $cleanob
		 */

		public function render($file, array $array = [], $default_sets = false, $cleanob = true)
		{


			if ($default_sets)
			{

				$userid = Container::getObject('session')->userid();
				$computerid = self::$computer->computerid();

				if (isset($array["localsoftwares"]) == false)
					$array["localsoftwares"] = self::$software->getSoftwareOnComputer($computerid);

				if (isset($array["user"]) == false)
					$array["user"] = self::$user->getUser($userid);

				if (isset($array["computer"]) == false)
					$array["computer"] = self::$computer->getComputer($computerid);

				if (isset($array["accounts"]) == false)
					$array["accounts"] = self::$finance->getUserBankAccounts($userid);

				if (isset($array["ipaddress"]) == false)
					$array["ipaddress"] = self::$computer->getComputer($computerid)->ipaddress;
			}

			if ($cleanob)
				ob_clean();

			Render::view('syscrack/' . $file, $array);
		}

		/**
		 * Calculates the processing time for an action using the algorithm
		 *
		 * @param $computerid
		 *
		 * @param string $hardwaretype
		 *
		 * @param float $speedness
		 *
		 * @param null $softwareid
		 *
		 * @return int
		 */

		public function calculateProcessingTime($computerid, $hardwaretype = "cpu", $speedness = 5.5, $softwareid = null)
		{

			if (self::$hardware->hasHardwareType($computerid, $hardwaretype) == false)
				return TimeHelper::getSecondsInFuture(Settings::setting('syscrack_operations_default_processingtime'));


			if ($softwareid !== null)
			{

				if (self::$software->softwareExists($softwareid) == false)
					throw new SyscrackException();

				$hardware = self::$hardware->getHardwareType($computerid, $hardwaretype);
				$software = self::$software->getSoftware($softwareid);
				return TimeHelper::getSecondsInFuture(floor((sqrt($software->level / $hardware['value']) * $speedness) * (Settings::setting('syscrack_operations_global_speed'))));
			}

			$hardware = self::$hardware->getHardwareType($computerid, $hardwaretype);
			return TimeHelper::getSecondsInFuture(floor(sqrt($speedness / $hardware['value']) * (Settings::setting('syscrack_operations_global_speed'))));
		}

		/**
		 * @param $accounts
		 *
		 * @return array
		 */

		public function getAddresses($accounts)
		{

			$ipaddresses = [];

			foreach ($accounts as $account)
				$ipaddresses[] = ["accountnumber" => $account->accountnumber, "ipaddress" => self::$computer->getComputer($account->computerid)->ipaddress];

			return ($ipaddresses);
		}


		/**
		 * @param $path
		 * @param bool $exit
		 */

		public function redirect($path, $exit = true)
		{

			Render::redirect(Settings::setting('controller_index_root') . $path);
		}

		/**
		 * Checks the custom data
		 *
		 * @param $data
		 *
		 * @param array|null $requirements
		 *
		 * @return bool
		 */

		public function checkCustomData($data, array $requirements = null)
		{

			if (isset($data['custom']) == false)
				return false;


			if (empty($data['custom']) || $data['custom'] == null)
				return false;


			if ($requirements !== null)
				foreach ($requirements as $requirement)
					if (isset($data['custom'][$requirement]) == false)
						return false;

			return true;
		}

		/**
		 * Checks if the computer has space
		 *
		 * @param $computerid
		 *
		 * @param float $needed
		 *
		 * @return bool
		 */

		public function hasSpace($computerid, $needed)
		{

			$hdd = self::$hardware->getHardwareType($computerid, 'harddrive')['value'];
			$softwares = self::$software->getSoftwareOnComputer($computerid);

			if (empty($softwares))
				return true;

			$usedspace = 0.0 + $needed;

			foreach ($softwares as $software)
				$usedspace += @$software->size;

			return ($usedspace < $hdd) ? true : false;
		}

		/**
		 * Gets the page the operation should redirect too
		 *
		 * @param null $ipaddress
		 *
		 * @param bool $local
		 *
		 * @return string
		 */

		public function getRedirect($ipaddress = null, $local = false)
		{

			if ($ipaddress == self::$computer->getComputer(self::$computer->computerid())->ipaddress)
				return Settings::setting('syscrack_computers_page');

			if ($local)
				return Settings::setting('syscrack_computers_page');

			if ($ipaddress)
				return Settings::setting('syscrack_game_page') . '/' . Settings::setting('syscrack_internet_page') . '/' . $ipaddress;

			return Settings::setting('syscrack_game_page');
		}

		/**
		 * Unsets session variables on logout
		 */

		public function safeUnset()
		{

			$unset = Settings::setting('syscrack_operations_safeunset_values');

			foreach ($unset as $value)
				if (isset($_SESSION[$value]))
					unset($_SESSION[$value]);


		}

		/**
		 * Gets the computer id from an ipaddress
		 *
		 * @param $ipaddress
		 *
		 * @return mixed
		 */

		public function getComputerId($ipaddress)
		{

			return self::$internet->getComputer($ipaddress)->computerid;
		}

		/**
		 * Gets the current computers ip address
		 *
		 * @return mixed
		 */

		public function getCurrentComputerAddress()
		{

			return self::$computer->getComputer(self::$computer->computerid())->ipaddress;
		}

		/**
		 * Gets the software name of a software
		 *
		 * @param $softwareid
		 *
		 * @return mixed
		 */

		public function getSoftwareName($softwareid)
		{

			if (self::$software->softwareExists($softwareid) == false)

				throw new SyscrackException();

			return self::$software->getSoftware($softwareid)->softwarename;
		}

		/**
		 * Gets the current page
		 *
		 * @return string
		 */

		private function getCurrentPage()
		{

			$page = array_values(array_filter(explode('/', strip_tags($_SERVER['REQUEST_URI']))));

			if (empty($page))
			{

				return Settings::setting('controller_index_page');
			}

			return $page[0];
		}
	}
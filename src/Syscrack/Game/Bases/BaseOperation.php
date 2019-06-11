<?php
	declare(strict_types=1);

	namespace Framework\Syscrack\Game\Bases;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class Operation
	 *
	 * @package Framework\Syscrack\Game
	 */

	use Framework\Application\UtilitiesV2\Container;
	use Framework\Application\Render;
	use Framework\Application\Settings;
	use Framework\Application\Utilities\ArrayHelper;
	use Framework\Application\UtilitiesV2\Controller\FormMessage;
	use Framework\Application\UtilitiesV2\Format;

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
	use Framework\Application\FormContainer;
	use Framework\Syscrack\Game\Preferences;
	use Framework\Application;

	/**
	 * Class BaseOperation
	 * @package Framework\Syscrack\Game\Bases
	 */
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
		 * @var Preferences
		 */

		protected static $preferences;

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

				if( isset( self::$preferences ) == false )
					self::$preferences = new Preferences();
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
		 * @return bool|null
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

			return [
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
			];
		}

		/**
		 * @param string $message
		 */

		public function formError($message = '')
		{

			FormContainer::add( new FormMessage( Application::globals()->FORM_ERROR_GENERAL, $message, false ) );
		}

		/**
		 * @param string $optional_message
		 */

		public function formSuccess($optional_message = '')
		{

			FormContainer::add( new FormMessage( Application::globals()->FORM_MESSAGE_SUCCESS, $optional_message, true ) );
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

			return false;
		}

		/**
		 * Gets a type of software to be used in an operation. Takes the uniquename of a software and will either
		 * return the highest level software of the users preference if they have one
		 *
		 * @param int $computerid
		 * @param string $type
		 *
		 * @return array|mixed|null
		 */

		public function software( int $computerid, string $type="cracker" )
		{

			$computer = self::$computer->getComputer( $computerid );

			if( empty( $computer ) )
				return null;

			if( self::$preferences->has( $computer->userid ) == false )
				return( $this->getHighestLevelSoftware( $computerid, $type, true) );
			elseif( self::$preferences->hasSoftwarePreference( $computer->userid, $computerid, $type ) == false )
				return( $this->getHighestLevelSoftware( $computerid, $type, true) );
			else
			{

				$preference = self::$preferences->getSoftwarePreference( $computer->userid, $computerid, $type );

				if( self::$computer->hasSoftware( $computerid, $preference ) == false )
					return( $this->getHighestLevelSoftware( $computerid, $type, true) );
				elseif( self::$software->softwareExists( $preference ) == false )
					return( $this->getHighestLevelSoftware( $computerid, $type, true ) );
				else
					return( self::$software->getSoftware( $preference ) );
			}
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
				throw new \Error();

			$victimid = self::$internet->computer($ipaddress);

			if (self::$log->hasLog($victimid->computerid) == false || self::$log->hasLog($computerid) == false)
				throw new \Error();

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
		 * @param array $array
		 * @param bool $default_sets
		 * @param bool $cleanob
		 * @param null $model
		 */

		public function render($file, array $array = [], $default_sets = false, $cleanob = true, $model=null )
		{


			if ($default_sets)
			{

				$userid = Container::get('session')->userid();
				$computerid = self::$computer->computerid();

				if (isset($array["localsoftwares"]) == false)
					$array["localsoftwares"] = self::$software->getSoftwareOnComputer($computerid);

				if (isset($array["user"]) == false)
					$array["user"] = self::$user->getUser($userid);

				if (isset($array["currentcomputer"]) == false)
					$array["currentcomputer"] = self::$computer->getComputer($computerid);

				if (isset($array["accounts"]) == false)
					$array["accounts"] = self::$finance->getUserBankAccounts($userid);

				if (isset($array["ipaddress"]) == false)
					$array["ipaddress"] = self::$computer->getComputer($computerid)->ipaddress;

				if( isset( $array["cash"] ) == false )
					$array["cash"] = self::$finance->getTotalUserCash( $userid );

				if( isset( $array["connection"] ) == false )
					$array["connection"] = self::$internet->getCurrentConnectedAddress();
			}

			if ($cleanob)
				ob_clean();

			Render::view('syscrack/' . $file, $array, $model );
		}

		/**
		 * @param $computerid
		 * @param null $userid
		 * @param array $data
		 *
		 * @return int
		 */

		public function addSoftware( $computerid, $userid, array $data )
		{

			if( $userid === null )
				if( Container::exist('session') )
					$userid = Container::get('session')->userid();
				else
					throw new \Error("Session does not exist in container when trying to add software");

			if( is_numeric( $userid ) == false )
				throw new \Error("Userid must be numeric");

			$userid = (int)$userid;

			if( isset( $data["uniquename"] ) == false )
				throw new \Error("Cannot add software with out unique name");

			$class = self::$software->findSoftwareByUniqueName( $data["uniquename"] );
			$softwareid = self::$software->createSoftware( $class,
				$userid, $computerid, @$data["name"],@$data["level"],
				@$data["size"], @$data["data"] );

			$software = self::$software->getSoftware( $softwareid );
			self::$computer->addSoftware( $software->computerid, $software->softwareid, $software->type );

			return( $softwareid );
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
					throw new \Error();

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

			if( $exit )
				Application\UtilitiesV2\Debug::message("attempted exit to: " . $path );

			Render::redirect(Application::globals()->CONTROLLER_INDEX_ROOT . $path);
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

		public function computerAtAddress($ipaddress)
		{

			return self::$internet->computer($ipaddress)->computerid;
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

				throw new \Error();

			return self::$software->getSoftware($softwareid)->softwarename;
		}

		/**
		 * @param $computerid
		 * @param null $type
		 * @param bool $object
		 *
		 * @return array|mixed|null
		 */

		private function getHighestLevelSoftware($computerid, $type = null, $object=false)
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
						$results[] = array_merge( $value, [
							'level' => @self::$software->getSoftware( $value["softwareid"] )->level
						]);

			$results = ArrayHelper::sortArray($results, 'level');

			if (is_array($results) == false)
				return (array)$results;

			if( isset( $results[0]) == false )
				if( $object )
					return( Format::toObject( $results ) );
				else
					return $results;

			if( $object )
				return( Format::toObject( $results[0] ) );
			else
				return $results[0];
		}
	}
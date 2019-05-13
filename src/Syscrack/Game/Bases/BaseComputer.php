<?php

	namespace Framework\Syscrack\Game\Bases;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class BaseComputer
	 *
	 * @package Framework\Syscrack\Game\Bases
	 */

	use Framework\Application\Settings;
	use Framework\Application\UtilitiesV2\Conventions\ComputerData;
	use Framework\Exceptions\SyscrackException;
	use Framework\Syscrack\Game\Computer as ComputerController;
	use Framework\Syscrack\Game\Internet;
	use Framework\Syscrack\Game\Log;
	use Framework\Syscrack\Game\Metadata;
	use Framework\Syscrack\Game\Software;
	use Framework\Syscrack\Game\Interfaces\Computer as Structure;

	class BaseComputer implements Structure
	{

		/**
		 * @var ComputerController
		 */

		protected static $computer;

		/**
		 * @var BaseSoftware
		 */

		protected static $software;

		/**
		 * @var Internet
		 */

		protected static $internet;

		/**
		 * @var Log
		 */

		protected static $log;

		/**
		 * @var Metadata
		 */

		protected static $metadata;

		/**
		 * BaseComputer constructor.
		 *
		 * @param bool $createclasses
		 */

		public function __construct($createclasses = true)
		{

			if (isset(self::$metadata) == false)
				self::$metadata = new Metadata();

			if ($createclasses == true)
			{

				if ($createclasses && isset(self::$computer) == false)
					self::$computer = new ComputerController();
				if ($createclasses && isset(self::$software) == false)
					self::$software = new Software();
				if ($createclasses && isset(self::$internet) == false)
					self::$internet = new Internet();
				if ($createclasses && isset(self::$log) == false)
					self::$log = new Log();
			}
		}

		/**
		 * @return bool
		 */

		public function configuration()
		{

			return (true);
		}

		/**
		 * @param $computerid
		 * @param $userid
		 *
		 * @return array
		 */

		public function data($computerid, $userid)
		{

			return ([]);
		}

		/**
		 * @param $computerid
		 * @param $ipaddress
		 *
		 * @return bool
		 */

		public function onLogin($computerid, $ipaddress)
		{

			return (true);
		}

		/**
		 * @param $computerid
		 * @param $ipaddress
		 *
		 * @return bool
		 */

		public function onLogout($computerid, $ipaddress)
		{

			return (true);
		}

		/**
		 * @return Metadata
		 */

		public function metadata(): Metadata
		{

			return (self::$metadata);
		}

		/**
		 * @param $computerid
		 * @param ComputerData $metadata
		 */

		public function reload($computerid, ComputerData $metadata)
		{

			$this->addHardwares($computerid, $metadata->hardware);
			$this->addSoftware($computerid, null, $metadata->software);

			$array = $metadata->info;
			$array["reset"] = microtime(true);
			$this->metadata()->update($computerid, array("info" => $array));
		}

		/**
		 * @return mixed
		 */

		public function canGetData()
		{

			return (@$this->configuration()["data"]);
		}


		/**
		 * Adds the software
		 *
		 * @param $computerid
		 *
		 * @param int $userid
		 *
		 * @param array $software
		 */

		public function addSoftware($computerid, $userid = null, array $software = [])
		{

			if ($userid == null)
				$userid = Settings::setting("syscrack_master_user");

			foreach ($software as $softwares)
			{

				if (isset($software['uniquename']) == false)
					continue;

				$class = self::$software->findSoftwareByUniqueName($software['uniquename']);

				if ($class == null)
					continue;

				$name = self::$software->getNameFromClass($class);

				if (isset($software['data']) == false)
					$software['data'] = [];

				$softwareid = self::$software->createSoftware(
					$name,
					$userid,
					$computerid,
					$software['name'],
					$software['level'],
					$software['size'],
					$software['data']);

				self::$computer->addSoftware(
					$computerid,
					$softwareid,
					self::$software->getSoftwareType($name)
				);


				if (isset($software['installed']) && $software['installed'])
				{

					self::$computer->installSoftware($computerid, $softwareid);
					self::$software->installSoftware($softwareid, $userid);
				}
			}
		}

		/**
		 * @param $computerid
		 */

		public function onReset($computerid)
		{

			$this->clearSoftware($computerid);
			self::$computer->resetHardware($computerid);

			if (self::$log->hasLog($computerid))
				self::$log->saveLog($computerid, []);

			if ($this->metadata()->exists($computerid))
				$this->reload($computerid, $this->metadata()->get($computerid));
			else
				$this->addHardwares($computerid, Settings::setting('syscrack_default_hardware'));
		}

		/**
		 * @param $computerid
		 * @param $userid
		 * @param array $software
		 * @param array $hardware
		 * @param array $custom
		 */

		public function onStartup($computerid, $userid, array $software = [], array $hardware = [], array $custom = [])
		{

			if (self::$log->hasLog($computerid) == false)
				self::$log->createLog($computerid);

			$this->addSoftware($computerid, $userid, $software);
			$this->addHardwares($computerid, $hardware);

			$this->metadata()->create($computerid, Metadata::generateData("Computer_" . $computerid, $this->configuration()["type"], $software, $hardware, $custom));
		}

		/**
		 * Clears the software
		 *
		 * @param $computerid
		 */

		public function clearSoftware($computerid)
		{

			$software = self::$computer->getComputerSoftware($computerid);

			foreach ($software as $softwares)
			{
				if (self::$software->softwareExists($software['softwareid']))
					self::$software->deleteSoftware($software['softwareid']);

				self::$computer->removeSoftware($computerid, $software['softwareid']);
			}
		}

		/**
		 * Sets the computer hardware
		 *
		 * @param $computerid
		 *
		 * @param array $hardware
		 */

		public function setHardwares($computerid, array $hardware)
		{

			self::$computer->setHardware($computerid, $hardware);
		}

		/**
		 * Adds a hardware to the computer
		 *
		 * @param $computerid
		 *
		 * @param array $hardware
		 */

		public function addHardwares($computerid, array $hardware)
		{

			$hardware = self::$computer->getComputerHardware($computerid);

			foreach ($hardware as $item => $value)
			{

				if (isset($hardware[$item]))
					continue;

				$hardware[$item] = $value;
			}

			$this->setHardwares($computerid, $hardware);
		}

		/**
		 * @param $uniquename
		 *
		 * @return \Framework\Syscrack\Game\Interfaces\Software
		 */

		public function getSoftwareClass($uniquename)
		{

			return self::$software->findSoftwareByUniqueName($uniquename);
		}

		/**
		 * @param $computerid
		 * @param $message
		 * @param $ipaddress
		 */

		public function log($computerid, $message, $ipaddress)
		{

			self::$log->updateLog($message, $computerid, $ipaddress);
		}

		/**
		 * @param $ipaddress
		 * @param $message
		 */

		public function logToIP($ipaddress, $message)
		{

			$computer = self::$internet->getComputer($ipaddress);

			if ($computer == null)
				throw new SyscrackException();

			$this->log($computer->computerid, $message, Settings::setting('syscrack_log_localhost_address'));
		}

		/**
		 * @return mixed
		 */

		public function getCurrentComputerAddress()
		{

			return self::$computer->getComputer(self::$computer->computerid())->ipaddress;
		}

		/**
		 * @param $computerid
		 *
		 * @return mixed
		 */

		public function getComputerOwner($computerid)
		{

			return self::$computer->getComputer($computerid)->userid;
		}
	}
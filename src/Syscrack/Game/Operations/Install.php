<?php
	declare(strict_types=1);

	namespace Framework\Syscrack\Game\Operations;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class Install
	 *
	 * @package Framework\Syscrack\Game\Operations
	 */

	use Framework\Application\Settings;
	use Framework\Syscrack\Game\AddressDatabase;
	use Framework\Syscrack\Game\Bases\BaseOperation;
	use Framework\Syscrack\Game\Viruses;

	/**
	 * Class Install
	 * @package Framework\Syscrack\Game\Operations
	 */
	class Install extends BaseOperation
	{

		/**
		 * @var Viruses
		 */

		protected static $viruses;

		/**
		 * @var AddressDatabase
		 */

		protected static $addressdatabase;

		/**
		 * Install constructor.
		 */

		public function __construct()
		{

			if (isset(self::$viruses) == false)
				self::$viruses = new Viruses();

			if (isset(self::$addressdatabase) == false)
				self::$addressdatabase = new AddressDatabase();

			parent::__construct(true);
		}

		/**
		 * Returns the configuration
		 *
		 * @return array
		 */

		public function configuration()
		{

			return [
				'allowsoftware' => true,
				'allowlocal' => true,
				'requiresoftware' => true,
				'requireloggedin' => true
			];
		}

		/**
		 * Called when a process with the corresponding operation is created
		 *
		 * @param $timecompleted
		 *
		 * @param $computerid
		 *
		 * @param $userid
		 *
		 * @param $process
		 *
		 * @param array $data
		 *
		 *
		 * @return bool
		 */

		public function onCreation($timecompleted, $computerid, $userid, $process, array $data)
		{

			if ($this->checkData($data) == false)
				return false;

			if (self::$software->canInstall($data['softwareid']) == false)
				return false;

			if (self::$software->isInstalled($data['softwareid'], $this->getComputerId($data['ipaddress'])))
				return false;

			if (self::$viruses->isVirus($data['softwareid']))
			{

				$software = self::$software->getSoftware($data['softwareid']);

				if ($this->getComputerId($data['ipaddress']) == $computerid)
					return false;

				if (self::$viruses->virusAlreadyInstalled($software->uniquename, $this->getComputerId($data['ipaddress']), $userid))
					return false;
			}

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
		 * @return bool|null|string
		 */

		public function onCompletion($timecompleted, $timestarted, $computerid, $userid, $process, array $data)
		{

			if ($this->checkData($data) == false)
				return false;

			if (self::$internet->ipExists($data['ipaddress']) == false)
				return false;

			if (self::$software->softwareExists($data['softwareid']) == false)
				return false;

			if (self::$software->isInstalled($data['softwareid'], $this->getComputerId($data['ipaddress'])))
				return false;

			self::$software->installSoftware($data['softwareid'], $userid);
			self::$computer->installSoftware($this->getComputerId($data['ipaddress']), $data['softwareid']);

			$this->logInstall($this->getSoftwareName($data['softwareid']),
				$this->getComputerId($data['ipaddress']), $this->getCurrentComputerAddress());
			$this->logLocal($this->getSoftwareName($data['softwareid']),
				self::$computer->computerid(), $data['ipaddress']);

			self::$software->executeSoftwareMethod(self::$software->getSoftwareNameFromSoftwareID($data['softwareid']), 'onInstalled', [
				'softwareid' => $data['softwareid'],
				'userid' => $userid,
				'computerid' => $this->getComputerId($data['ipaddress'])
			]);

			if (self::$viruses->isVirus($data['softwareid']) == true)
			{

				if (Settings::setting('syscrack_statistics_enabled') == true)
					self::$statistics->addStatistic('virusinstalls');

				self::$addressdatabase->addVirus($data['ipaddress'], $data['softwareid'], $userid);
			}

			if( parent::onCompletion(
					$timecompleted,
					$timestarted,
					$computerid,
					$userid,
					$process,
					$data) == false )
				return false;
			else if (isset($data['redirect']) == false)
				return true;
			else
				return ($data['redirect']);
		}

		/**
		 * Gets the completion speed
		 *
		 * @param $computerid
		 *
		 * @param $ipaddress
		 *
		 * @param null $softwareid
		 *
		 * @return int
		 */

		public function getCompletionSpeed($computerid, $ipaddress, $softwareid = null)
		{

			return $this->calculateProcessingTime($computerid, Settings::setting('syscrack_hardware_cpu_type'), 1, $softwareid);
		}

		/**
		 * @param $softwarename
		 * @param $computerid
		 * @param $ipaddress
		 */

		private function logInstall($softwarename, $computerid, $ipaddress)
		{

			if (self::$computer->computerid() == $computerid)
			{

				return;
			}

			$this->logToComputer('Installed file (' . $softwarename . ') on root', $computerid, $ipaddress);
		}

		/**
		 * @param $softwarename
		 * @param $computerid
		 * @param $ipaddress
		 */

		private function logLocal($softwarename, $computerid, $ipaddress)
		{

			$this->logToComputer('Installed file (' . $softwarename . ') on <' . $ipaddress . '>', $computerid, 'localhost');
		}
	}
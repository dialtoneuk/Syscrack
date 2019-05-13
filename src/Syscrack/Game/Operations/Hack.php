<?php

	namespace Framework\Syscrack\Game\Operations;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class Hack
	 *
	 * @package Framework\Syscrack\Game\Operations
	 */

	use Framework\Application\Settings;
	use Framework\Exceptions\SyscrackException;
	use Framework\Syscrack\Game\AddressDatabase;
	use Framework\Syscrack\Game\Bases\BaseOperation;

	class Hack extends BaseOperation
	{

		/**
		 * @var AddressDatabase;
		 */

		protected static $addressdatabase;

		/**
		 * Hack constructor.
		 */

		public function __construct()
		{

			if (isset(self::$addressdatabase) == false)
				self::$addressdatabase = new AddressDatabase();

			parent::__construct();
		}

		/**
		 * The configuration of this operation
		 */

		public function configuration()
		{

			return array(
				'allowsoftware' => false,
				'allowlocal' => false,
				'requiresoftware' => false,
				'requireloggedin' => false,
				'elevated' => true,
			);
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
		 * Called when this process request is created
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
		 * @return mixed
		 */

		public function onCreation($timecompleted, $computerid, $userid, $process, array $data)
		{

			if ($this->checkData($data, ['ipaddress']) == false)
				return false;

			if (self::$computer->getComputer($computerid)->ipaddress == $data['ipaddress'])
				return false;


			if (self::$addressdatabase->hasAddress($data['ipaddress'], $userid))
				return false;

			if (self::$computer->hasType($computerid, Settings::setting('syscrack_software_cracker_type'), true) == false)
				return false;

			$victimid = $this->getComputerId($data['ipaddress']);

			if (self::$computer->hasType($victimid, Settings::setting('syscrack_software_hasher_type'), true) == true)
				if ($this->getHighestLevelSoftware($victimid, Settings::setting('syscrack_software_hasher_type'))['level'] > $this->getHighestLevelSoftware($computerid, Settings::setting('syscrack_software_cracker_type'))['level'])
					return false;

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
		 * @return bool|mixed
		 */

		public function onCompletion($timecompleted, $timestarted, $computerid, $userid, $process, array $data)
		{

			if ($this->checkData($data, ['ipaddress']) == false)
				throw new SyscrackException();


			if (self::$internet->ipExists($data['ipaddress']) == false)
				return false;

			self::$addressdatabase->addAddress($data['ipaddress'], $userid);

			if (Settings::setting('syscrack_statistics_enabled') == true)
				self::$statistics->addStatistic('hacks');

			if (isset($data['redirect']) == false)
				return true;
			else
				return ($data['redirect']);
		}

		/**
		 * Gets the completion speed of this action
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

			return $this->calculateProcessingTime($computerid, Settings::setting('syscrack_hardware_cpu_type'), Settings::setting('syscrack_operations_hack_speed'), $softwareid);
		}
	}
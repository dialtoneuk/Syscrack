<?php

	namespace Framework\Syscrack\Game\Operations;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class Clear
	 *
	 * @package Framework\Syscrack\Game\Operations
	 */

	use Framework\Application\Settings;
	use Framework\Exceptions\SyscrackException;
	use Framework\Syscrack\Game\BaseClasses\BaseOperation;


	class Clear extends BaseOperation
	{

		/**
		 * The configuration of this operation
		 */

		public function configuration()
		{

			return array(
				'allowsoftware' => false,
				'allowlocal' => true,
				'requireloggedin' => true
			);
		}

		/**
		 * Called when the process is created
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
		 * @return bool
		 */

		public function onCreation($timecompleted, $computerid, $userid, $process, array $data)
		{

			if ($this->checkData($data, ['ipaddress']) == false)
				return false;

			$computer = self::$internet->getComputer($data['ipaddress']);

			if (self::$log->hasLog($computer->computerid) == false)
				return false;

			if (empty(self::$log->getCurrentLog($computer->computerid)))
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
		 * @return bool
		 */

		public function onCompletion($timecompleted, $timestarted, $computerid, $userid, $process, array $data)
		{

			if ($this->checkData($data, ['ipaddress']) == false)
				throw new SyscrackException();

			if (self::$internet->ipExists($data['ipaddress']) == false)
				return false;

			self::$log->saveLog($this->getComputerId($data['ipaddress']), []);

			if (isset($data['redirect']) == false)
				return true;
			else
				return ($data['redirect']);
		}

		/**
		 * gets the time in seconds it takes to complete an action
		 *
		 * @param $computerid
		 *
		 * @param $softwareid
		 *
		 * @param $ipaddress
		 *
		 * @return null
		 */

		public function getCompletionSpeed($computerid, $ipaddress, $softwareid = null)
		{

			return $this->calculateProcessingTime($computerid, Settings::setting('syscrack_hardware_cpu_type'), Settings::setting('syscrack_operations_clear_speed'), $softwareid);
		}
	}
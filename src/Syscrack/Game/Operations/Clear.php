<?php
	declare(strict_types=1);

	namespace Framework\Syscrack\Game\Operations;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class Clear
	 *
	 * @package Framework\Syscrack\Game\Operations
	 */

	use Framework\Application\Settings;

	use Framework\Syscrack\Game\Bases\BaseOperation;


	/**
	 * Class Clear
	 * @package Framework\Syscrack\Game\Operations
	 */
	class Clear extends BaseOperation
	{

		/**
		 * The configuration of this operation
		 */

		public function configuration()
		{

			return [
				'allowsoftware' => false,
				'allowlocal' => true,
				'requireloggedin' => true
			];
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

			$computer = self::$internet->computer($data['ipaddress']);

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
		 * @return bool|string|null
		 */

		public function onCompletion($timecompleted, $timestarted, $computerid, $userid, $process, array $data)
		{

			if ($this->checkData($data, ['ipaddress']) == false)
				throw new \Error();

			if (self::$internet->ipExists($data['ipaddress']) == false)
				return false;

			self::$log->saveLog($this->computerAtAddress($data['ipaddress']), []);

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

			return $this->calculateProcessingTime($computerid, Settings::setting('hardware_type_cpu'), Settings::setting('operations_clear_speed'), $softwareid);
		}
	}
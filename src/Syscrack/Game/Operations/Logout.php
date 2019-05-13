<?php

	namespace Framework\Syscrack\Game\Operations;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class Logout
	 *
	 * @package Framework\Syscrack\Game\Operations
	 */

	use Framework\Exceptions\SyscrackException;
	use Framework\Syscrack\Game\Bases\BaseOperation;


	class Logout extends BaseOperation
	{

		/**
		 * The configuration of this operation
		 */

		public function configuration()
		{

			return array(
				'allowsoftware' => false,
				'allowlocal' => false,
				'requireloggedin' => true
			);
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

			if (self::$internet->hasCurrentConnection() == false)
				return false;


			if (self::$internet->getCurrentConnectedAddress() !== $data['ipaddress'])
				return false;

			if (isset($data['redirect']) == false)
				return true;
			else
				return ($data['redirect']);
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

			$computer = self::$internet->getComputer($data['ipaddress']);

			if (self::$computer->hasComputerClass($computer->type) == false)
				return false;

			self::$computer->getComputerClass($computer->type)->onLogout($computer->computerid, $data['ipaddress']);
			return true;
		}
	}
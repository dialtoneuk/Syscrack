<?php

	namespace Framework\Syscrack\Game\Operations;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class Execute
	 *
	 * @package Framework\Syscrack\Game\Operations
	 */

	use Framework\Exceptions\SyscrackException;
	use Framework\Syscrack\Game\Bases\BaseOperation;
	use Framework\Syscrack\Game\Interfaces\Software;

	class Execute extends BaseOperation
	{

		/**
		 * Called when the software is executed
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

			if ($this->checkData($data, ['ipaddress', 'softwareid']) == false)
				return false;

			if (self::$software->canExecute($data['softwareid']) == false)
				return false;

			if (self::$software->isInstalled($data['softwareid'], $this->getComputerId($data['ipaddress'])) == false)
				return false;

			if (self::$software->localExecuteOnly($data['softwareid']) && self::$computer->getComputer($computerid)->ipaddress !== $data['ipaddress'])
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

			if ($this->checkData($data, ['ipaddress', 'softwareid']) == false)
				return false;

			if (self::$internet->ipExists($data['ipaddress']) == false)
				return false;

			if (self::$software->softwareExists($data['softwareid']) == false)
				return false;

			$class = self::$software->getSoftwareClassFromID($data['softwareid']);

			if ($class instanceof Software == false)
				return false;

			return @$class->onExecuted($data['softwareid'], $userid, $this->getComputerId($data['ipaddress']));
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
		 * @return mixed|null
		 */

		public function getCompletionSpeed($computerid, $ipaddress, $softwareid = null)
		{

			if ($softwareid == null)
				throw new SyscrackException();

			if (self::$software->softwareExists($softwareid) == false)
				throw new SyscrackException();

			$class = self::$software->getSoftwareClassFromID($softwareid);

			if ($class instanceof Software == false)
				throw new SyscrackException();

			return $class->getExecuteCompletionTime($softwareid, $computerid);
		}
	}
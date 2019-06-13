<?php
	declare(strict_types=1);

	namespace Framework\Syscrack\Game\Operations;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class Logout
	 *
	 * @package Framework\Syscrack\Game\Operations
	 */

	use Framework\Application\Settings;
	use Framework\Syscrack\Game\Bases\BaseOperation;
	use Framework\Syscrack\Game\Viruses;

	/**
	 * Class Delete
	 * @package Framework\Syscrack\Game\Operations
	 */
	class Delete extends BaseOperation
	{

		/**
		 * @var Viruses
		 */

		protected static $viruses;

		/**
		 * Delete constructor.
		 */

		public function __construct()
		{

			if (isset(self::$viruses) == false)
				self::$viruses = new Viruses();


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

			if ($this->checkData($data) == false)
				return false;

			if (self::$computer->hasSoftware($this->computerAtAddress($data['ipaddress']), $data['softwareid']) == false)
				return false;

			$software = self::$software->getSoftware($data['softwareid']);

			if( $software->installed == false )
				if (self::$viruses->isVirus($software->softwareid) && $software->userid !== $userid)
					return false;
				elseif( self::$viruses->isVirus($software->softwareid) && $software->userid == $userid )
					return true;

			if(self::$software->canRemove($software->softwareid) == false )
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

			if ($this->checkData($data) == false)
				return false;

			if (self::$software->softwareExists($data['softwareid']) == false)
				return false;

			$software = self::$software->getSoftware($data['softwareid']);

			if (self::$internet->ipExists($data['ipaddress']) == false)
				return false;

			self::$software->deleteSoftware($software->softwareid);
			self::$computer->removeSoftware($this->computerAtAddress($data['ipaddress']), $software->softwareid);
			$this->logDelete($software->softwarename, $this->computerAtAddress($data['ipaddress']), self::$computer->getComputer($computerid)->ipaddress);
			$this->logLocal($software->softwarename, $data['ipaddress']);

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
		 * Returns the completion time for this action
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

			return $this->calculateProcessingTime($computerid, Settings::setting('hardware_type_cpu'), 5.5, $softwareid);
		}

		/**
		 * @param $softwarename
		 * @param $computerid
		 * @param $ipaddress
		 */

		private function logDelete($softwarename, $computerid, $ipaddress)
		{

			if (self::$computer->computerid() == $computerid)
			{

				return;
			}

			$this->logToComputer('Deleted file <' . $softwarename . '> on root', $computerid, $ipaddress);
		}

		/**
		 * Logs to the local log
		 *
		 * @param $softwarename
		 *
		 * @param $ipaddress
		 */

		private function logLocal($softwarename, $ipaddress)
		{

			$this->logToComputer('Deleted file <' . $softwarename . '> on ' . $ipaddress, self::$computer->getComputer(self::$computer->computerid())->computerid, 'localhost');
		}
	}
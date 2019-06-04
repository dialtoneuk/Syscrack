<?php

	namespace Framework\Syscrack\Game\Operations;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class AnonDownload
	 *
	 * @package Framework\Syscrack\Game\Operations
	 */

	use Framework\Application\Settings;

	use Framework\Syscrack\Game\Bases\BaseOperation;

	class AnonDownload extends BaseOperation
	{

		/**
		 * Allows for anonymous downloads
		 *
		 * @return array
		 */

		public function configuration()
		{

			return array(
				'allowlocal' => false,
				'allowsoftware' => true,
				'allowanonymous' => true,
				'requiresoftware' => true,
				'requireloggedin' => false,
				'elevated' => true,
			);
		}

		/**
		 * Called when the operation is created
		 *
		 * @param self::$financeimecompleted
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

			if ($this->checkData($data) == false)
				return false;

			$computer = self::$internet->computer($data['ipaddress']);
			$software = self::$software->getSoftware($data['softwareid']);

			if ($computer->type !== Settings::setting('syscrack_computers_download_type'))
				return false;
			else if ($this->hasSpace(self::$computer->computerid(), $software->size) == false)
				return false;
			else if (self::$software->isAnonDownloadSoftware($software->softwareid) == false)
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
		 * @return bool|string
		 */

		public function onCompletion($timecompleted, $timestarted, $computerid, $userid, $process, array $data)
		{


			if ($this->checkData($data) == false)
				return false;

			$computer = self::$internet->computer($data['ipaddress']);
			$software = self::$software->getSoftware($data['softwareid']);

			if (self::$internet->ipExists($data['ipaddress']) == false)
				return false;
			else if (self::$software->softwareExists($data['softwareid']) == false)
				return false;

			$new_software = self::$software->getSoftware(self::$software->copySoftware($data['softwareid'], self::$computer->computerid(), $userid));
			self::$computer->addSoftware(self::$computer->computerid(), $new_software->softwareid, $new_software->type);

			if (isset($data['redirect']) == false)
				return true;
			else
				return ($data['redirect']);
		}

		/**
		 * Gets the completion speed of this operation
		 *
		 * @param $computerid
		 *
		 * @param $ipaddress
		 *
		 * @param $softwareid
		 *
		 * @return int
		 */

		public function getCompletionSpeed($computerid, $ipaddress, $softwareid = null)
		{

			if (self::$software->softwareExists($softwareid) == false)
				throw new \Error();

			return $this->calculateProcessingTime($computerid, Settings::setting('syscrack_hardware_download_type'), self::$software->getSoftware($softwareid)->size / 5, $softwareid);
		}
	}
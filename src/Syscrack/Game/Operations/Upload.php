<?php

	namespace Framework\Syscrack\Game\Operations;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class Upload
	 *
	 * @package Framework\Syscrack\Game\Operations
	 */

	use Framework\Application\Settings;
	use Framework\Application\Utilities\PostHelper;
	use Framework\Syscrack\Game\Bases\BaseOperation;


	class Upload extends BaseOperation
	{

		/**
		 * @return array
		 */

		public function configuration()
		{

			return array(
				'allowsoftware' => false,
				'allowlocal' => false,
				'requiresoftware' => false,
				'requireloggedin' => true,
				'allowpost' => false,
				'allowcustomdata' => true,
			);
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


			if ($this->checkData($data, ['ipaddress']) == false)
				return false;

			if ($this->checkCustomData($data, ['softwareid']) == false)
				return false;

			if (self::$software->softwareExists($data['custom']['softwareid']) == false)
				return false;

			$software = self::$software->getSoftware($data['custom']['softwareid']);

			if ($this->hasSpace($this->getComputerId($data['ipaddress']), $software->size) == false)
				return false;

			if (self::$computer->hasSoftware($computerid, $software->softwareid) == false)
				return false;

			if (self::$computer->isInstalled($computerid, $software->softwareid) == true)
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
				return false;

			if (self::$internet->ipExists($data['ipaddress']) == false)
				return false;

			if ($this->checkCustomData($data, ['softwareid']) == false)
				return false;

			if (self::$software->softwareExists($data['custom']['softwareid']) == false)
				return false;

			$software = self::$software->getSoftware($data['custom']['softwareid']);

			if (self::$software->hasData($software->softwareid) == true && self::$software->keepData($software->softwareid))
			{

				$softwaredata = self::$software->getSoftwareData($software->softwareid);

				if (self::$software->checkSoftwareData($software->softwareid, ['allowanondownloads']) == true)
					return false;

				if (self::$software->checkSoftwareData($software->softwareid, ['editable']) == true)
					return false;

				$new_softwareid = self::$software->copySoftware($software->softwareid, $this->getComputerId($data['ipaddress']), $userid, false, $softwaredata);
			}
			else
				$new_softwareid = self::$software->copySoftware($software->softwareid, $this->getComputerId($data['ipaddress']), $userid);


			self::$computer->addSoftware($this->getComputerId($data['ipaddress']), $new_softwareid, $software->type);

			if (self::$computer->hasSoftware($this->getComputerId($data['ipaddress']), $new_softwareid) == false)
				return false;

			$this->logUpload($software, $this->getComputerId($data['ipaddress']), self::$computer->getComputer($computerid)->ipaddress);
			$this->logLocal($software, $data['ipaddress']);

			if (isset($data['redirect']) == false)
				return true;
			else
				return ($data['redirect']);
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

			return $this->calculateProcessingTime($computerid, Settings::setting('syscrack_hardware_upload_type'), 20, $softwareid);
		}

		/**
		 * @param $ipaddress
		 * @param $userid
		 *
		 * @return array|null
		 */

		public function getCustomData($ipaddress, $userid)
		{

			if (PostHelper::hasPostData() == false)
			{

				return null;
			}

			if (PostHelper::checkForRequirements(['softwareid']) == false)
			{

				return null;
			}

			return array(
				'softwareid' => PostHelper::getPostData('softwareid')
			);
		}

		/**
		 * @param $software
		 * @param $computerid
		 * @param $ipaddress
		 */

		private function logUpload($software, $computerid, $ipaddress)
		{

			$this->logToComputer('Uploaded file ' . $software->softwarename . ' (' . $software->level . ') on root', $computerid, $ipaddress);
		}

		/**
		 * Logs to the local log
		 *
		 * @param $software
		 * @param $ipaddress
		 */

		private function logLocal($software, $ipaddress)
		{

			$this->logToComputer('Uploaded file ' . $software->softwarename . ' (' . $software->level . ') on <' . $ipaddress . '>', self::$computer->getComputer(self::$computer->computerid())->computerid, 'localhost');
		}
	}
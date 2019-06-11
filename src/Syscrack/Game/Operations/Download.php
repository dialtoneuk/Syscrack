<?php
	declare(strict_types=1);

	namespace Framework\Syscrack\Game\Operations;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class Download
	 *
	 * @package Framework\Syscrack\Game\Operations
	 */

	use Framework\Application\Settings;

	use Framework\Syscrack\Game\Bases\BaseOperation;
	use Framework\Syscrack\Game\Viruses;

	/**
	 * Class Download
	 * @package Framework\Syscrack\Game\Operations
	 */
	class Download extends BaseOperation
	{

		/**
		 * @var Viruses
		 */

		protected static $viruses;

		/**
		 * Download constructor.
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
				'allowlocal' => false,
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

			if (self::$computer->hasSoftware(self::$internet->computer($data['ipaddress'])->computerid, $data['softwareid']) == false)
				return false;

			$software = self::$software->getSoftware($data['softwareid']);

			if ($this->hasSpace(self::$computer->computerid(), $software->size) == false)
				return false;

			if (self::$viruses->isVirus($software->softwareid) && self::$software->isInstalled($software->softwareid, $this->computerAtAddress($data['ipaddress'])))
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

			if (self::$internet->ipExists($data['ipaddress']) == false)
				return false;

			if (self::$software->softwareExists($data['softwareid']) == false)
				return false;

			$software = self::$software->getSoftware($data['softwareid']);

			if (self::$software->hasData($software->softwareid) == true && self::$software->keepData($software->softwareid))
			{

				$softwaredata = self::$software->getSoftwareData($software->softwareid);

				if (self::$software->checkSoftwareData($software->softwareid, ['allowanondownloads']) == true)
					unset($softwaredata['allowanondownloads']);

				if (self::$software->checkSoftwareData($software->softwareid, ['editable']) == true)
					unset($softwaredata['editable']);

				$new_softwareid = self::$software->copySoftware($software->softwareid, $computerid, $userid, false, $softwaredata);
			}
			else
				$new_softwareid = self::$software->copySoftware($software->softwareid, $computerid, $userid);

			self::$computer->addSoftware($computerid, $new_softwareid, $software->type);

			if (self::$computer->hasSoftware($computerid, $new_softwareid) == false)
				throw new \Error();

			$this->logDownload($software->softwarename, $this->computerAtAddress($data['ipaddress']), self::$computer->getComputer($computerid)->ipaddress);
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

			if (self::$software->softwareExists($softwareid) == false)
			{

				throw new \Error();
			}

			return $this->calculateProcessingTime($computerid, Settings::setting('syscrack_hardware_download_type'), self::$software->getSoftware($softwareid)->size / 5, $softwareid);
		}

		/**
		 * @param $softwarename
		 * @param $computerid
		 * @param $ipaddress
		 */

		private function logDownload($softwarename, $computerid, $ipaddress)
		{

			$this->logToComputer('Downloaded file (' . $softwarename . ') on root', $computerid, $ipaddress);
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

			$this->logToComputer('Downloaded file (' . $softwarename . ') on <' . $ipaddress . '>', self::$computer->getComputer(self::$computer->computerid())->computerid, 'localhost');
		}
	}
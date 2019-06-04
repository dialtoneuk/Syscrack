<?php

	namespace Framework\Syscrack\Game\Operations;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class Uninstall
	 *
	 * @package Framework\Syscrack\Game\Operations
	 */

	use Framework\Application\Settings;

	use Framework\Syscrack\Game\Bases\BaseOperation;
	use Framework\Syscrack\Game\Viruses;

	class Uninstall extends BaseOperation
	{

		/**
		 * @var Viruses
		 */

		protected static $viruses;

		/**
		 * Uninstall constructor.
		 */

		public function __construct()
		{

			if (isset(self::$viruses) == false)
				self::$viruses = new Viruses();

			parent::__construct();
		}

		/**
		 * The configuration for this operation
		 *
		 * @return array
		 */

		public function configuration()
		{

			return array(
				'allowsoftware' => true,
				'allowlocal' => true,
				'requiresoftware' => true,
				'requireloggedin' => true
			);
		}

		/**
		 * Called when the operation is created
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

			if ($this->checkData($data) == false)
			{

				return false;
			}

			if (self::$software->softwareExists($data['softwareid']) == false)
			{

				return false;
			}
			else
			{

				if (self::$computer->hasSoftware($this->getComputerId($data['ipaddress']), $data['softwareid']) == false)
				{

					return false;
				}
			}

			if (self::$software->canUninstall($data['softwareid']) == false)
			{

				$this->redirectError('You cannot uninstall this software', $this->getRedirect($data['ipaddress']));
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
		 * @return bool|mixed
		 */

		public function onCompletion($timecompleted, $timestarted, $computerid, $userid, $process, array $data)
		{

			if ($this->checkData($data) == false)
			{

				throw new \Error();
			}

			if (self::$internet->ipExists($data['ipaddress']) == false)
			{

				$this->redirect($this->getRedirect());
				return false;
			}

			if (self::$software->softwareExists($data['softwareid']) == false)
			{

				$this->redirect($this->getRedirect());
				return false;
			}

			if (self::$software->isInstalled($data['softwareid'], $this->getComputerId($data['ipaddress'])) == false)
			{

				$this->redirect($this->getRedirect());
				return false;
			}

			self::$software->uninstallSoftware($data['softwareid']);
			self::$computer->uninstallSoftware($this->getComputerId($data['ipaddress']), $data['softwareid']);

			$this->logUninstall($this->getSoftwareName($data['softwareid']),
				$this->getComputerId($data['ipaddress']), $this->getCurrentComputerAddress());

			$this->logLocal($this->getSoftwareName($data['softwareid']),
				self::$computer->computerid(), $data['ipaddress']);

			self::$software->executeSoftwareMethod(self::$software->getSoftwareNameFromSoftwareID($data['softwareid']), 'onUninstalled', array(
				'softwareid' => $data['softwareid'],
				'userid' => $userid,
				'computerid' => $this->getComputerId($data['ipaddress'])
			));

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
		 * @param null $softwareid
		 *
		 * @return int
		 */

		public function getCompletionSpeed($computerid, $ipaddress, $softwareid = null)
		{

			return $this->calculateProcessingTime($computerid, Settings::setting('syscrack_hardware_cpu_type'), 20, $softwareid);
		}

		/**
		 * Gets the custom data for this operation
		 *
		 * @param $ipaddress
		 *
		 * @param $userid
		 *
		 * @return array
		 */

		public function getCustomData($ipaddress, $userid)
		{

			return array();
		}

		/**
		 * Called upon a post request to this operation
		 *
		 * @param $data
		 *
		 * @param $ipaddress
		 *
		 * @param $userid
		 *
		 * @return bool
		 */

		public function onPost($data, $ipaddress, $userid)
		{

			return true;
		}

		/**
		 * @param $softwarename
		 * @param $computerid
		 * @param $ipaddress
		 */

		private function logUninstall($softwarename, $computerid, $ipaddress)
		{

			if (self::$computer->computerid() == $computerid)
			{

				return;
			}

			$this->logToComputer('Uninstalled file (' . $softwarename . ') on root', $computerid, $ipaddress);
		}

		/**
		 * @param $softwarename
		 * @param $computerid
		 * @param $ipaddress
		 */

		private function logLocal($softwarename, $computerid, $ipaddress)
		{

			$this->logToComputer('Uninstalled file (' . $softwarename . ') on <' . $ipaddress . '>', $computerid, 'localhost');
		}
	}
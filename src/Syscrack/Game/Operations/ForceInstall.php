<?php
	declare(strict_types=1);

	namespace Framework\Syscrack\Game\Operations;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class ForceInstall
	 *
	 * @package Framework\Syscrack\Game\Operations
	 */

	use Framework\Application\Settings;
	use Framework\Application\Utilities\PostHelper;
	use Framework\Syscrack\Game\AddressDatabase;
	use Framework\Syscrack\Game\Bases\BaseOperation;
	use Framework\Syscrack\Game\Viruses;

	/**
	 * Class ForceInstall
	 * @package Framework\Syscrack\Game\Operations
	 */
	class ForceInstall extends BaseOperation
	{

		/**
		 * @var Viruses
		 */

		protected static $viruses;

		/**
		 * @var AddressDatabase
		 */

		protected static $addressdatabase;

		/**
		 * Install constructor.
		 */

		public function __construct()
		{

			if (isset(self::$viruses) == false)
				self::$viruses = new Viruses();

			if (isset(self::$addressdatabase) == false)
				self::$addressdatabase = new AddressDatabase();

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
				'requiresoftware' => false,
				'requireloggedin' => true,
				'allowpost' => false,
				'allowcustomdata' => true,
			];
		}

		/**
		 * @param null $ipaddress
		 *
		 * @return string
		 */

		public function url($ipaddress = null)
		{

			return ("admin/computer/edit/" . @$this->computerAtAddress($ipaddress));
		}

		/**
		 * Called when a process with the corresponding operation is created
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
		 *
		 * @return bool
		 */

		public function onCreation($timecompleted, $computerid, $userid, $process, array $data)
		{

			if ($this->checkData($data, ['ipaddress']) == false)
				return false;

			if (self::$internet->ipExists($data['ipaddress']) == false)
				return false;

			if ($this->checkCustomData($data, ['softwareid']) == false)
				return false;

			if (self::$software->softwareExists($data['custom']['softwareid']) == false)
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
		 * @return bool|null|string
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

			self::$software->installSoftware($data['custom']['softwareid'], $userid);
			self::$computer->installSoftware($this->computerAtAddress($data['ipaddress']), $data['custom']['softwareid']);
			self::$software->executeSoftwareMethod(self::$software->getSoftwareNameFromSoftwareID($data['custom']['softwareid']), 'onInstalled', [
				'softwareid' => $data['custom']['softwareid'],
				'userid' => $userid,
				'computerid' => $this->computerAtAddress($data['ipaddress'])
			]);

			if (self::$viruses->isVirus($data['custom']['softwareid']) == true)
			{

				if (Settings::setting('statistics_enabled') == true)
					self::$statistics->addStatistic('virusinstalls');

				self::$addressdatabase->addVirus($data['ipaddress'], $data['custom']['softwareid'], $userid);
			}

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
		 * @param $ipaddress
		 * @param $userid
		 *
		 * @return array|null
		 */

		public function getCustomData($ipaddress, $userid)
		{

			if (PostHelper::hasPostData() == false)
				return null;


			if (PostHelper::checkForRequirements(['softwareid']) == false)
				return null;


			return ['softwareid' => PostHelper::getPostData('softwareid')];
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

			return null;
		}
	}
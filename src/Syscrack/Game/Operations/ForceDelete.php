<?php
	declare(strict_types=1);

	namespace Framework\Syscrack\Game\Operations;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class ForceDelete
	 *
	 * @package Framework\Syscrack\Game\Operations
	 */

	use Framework\Application\Utilities\PostHelper;
	use Framework\Syscrack\Game\Bases\BaseOperation;
	use Framework\Syscrack\Game\Viruses;

	/**
	 * Class ForceDelete
	 * @package Framework\Syscrack\Game\Operations
	 */
	class ForceDelete extends BaseOperation
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
				'requiresoftware' => false,
				'requireloggedin' => true,
				'allowpost' => false,
				'allowcustomdata' => true
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

			if ($this->checkCustomData($data, ['softwareid']) == false)
				return false;

			if (self::$user->isAdmin($userid) == false)
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

			if ($this->checkCustomData($data, ['softwareid']) == false)
				return false;

			if (self::$user->isAdmin($userid) == false)
				return false;

			$software = self::$software->getSoftware($data['custom']['softwareid']);
			self::$software->deleteSoftware($software->softwareid);
			self::$computer->removeSoftware($this->computerAtAddress($data['ipaddress']), $software->softwareid);

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

			return null;
		}
	}
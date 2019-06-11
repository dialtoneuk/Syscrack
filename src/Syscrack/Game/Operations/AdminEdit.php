<?php
	declare(strict_types=1);

	namespace Framework\Syscrack\Game\Operations;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class AdminEdit
	 *
	 * @package Framework\Syscrack\Game\Operations
	 */

	use Framework\Syscrack\Game\Bases\BaseOperation;

	/**
	 * Class AdminEdit
	 * @package Framework\Syscrack\Game\Operations
	 */
	class AdminEdit extends BaseOperation
	{

		/**
		 * AdminEdit constructor.
		 */

		public function __construct()
		{

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
				'elevated' => true
			];
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
		 * @return bool|string|null
		 */

		public function onCompletion($timecompleted, $timestarted, $computerid, $userid, $process, array $data)
		{

			if (self::$user->isAdmin($userid) == false)
				return false;

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
				return 'admin/computer/edit/' . $this->getComputerId($data["ipaddress"]);
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
<?php

	namespace Framework\Syscrack\Game\Operations;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class View
	 *
	 * @package Framework\Syscrack\Game\Operations
	 */

	use Framework\Syscrack\Game\Bases\BaseOperation;
	use Framework\Syscrack\User;

	class View extends BaseOperation
	{

		protected static $user;

		/**
		 * View constructor.
		 */

		public function __construct()
		{

			if (isset(self::$user) == false)
				self::$user = new User();

			parent::__construct(true);
		}

		/**
		 * Returns the configuration
		 *
		 * @return array
		 */

		public function configuration()
		{

			return array(
				'allowsoftware' => true,
				'allowlocal' => true,
				'requiresoftware' => true,
				'elevated' => true
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

			if ($this->checkData($data) == false)
				return false;


			if (self::$software->hasData($data['softwareid']) == false)
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
		 * @return bool
		 */

		public function onCompletion($timecompleted, $timestarted, $computerid, $userid, $process, array $data)
		{

			if ($this->checkData($data) == false)
				return false;

			if (self::$internet->ipExists($data['ipaddress']) == false)
				return false;
			if (self::$software->softwareExists($data['softwareid']) == false)
				return false;

			if (self::$software->hasData($data['softwareid']) == false)
				return false;

			$software = self::$software->getSoftware($data['softwareid']);

			$this->render('operations/operations.view', array(
				'software' => $software,
				'ipaddress' => self::$internet->getCurrentConnectedAddress(),
				'data' => json_decode($software->data),
				'softwaredata' => self::$software->getSoftwareData($data['softwareid'])
			), true);

			return( true );
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
		 * Gets the completion time
		 *
		 * @param $computerid
		 *
		 * @param $ipaddress
		 *
		 * @param null $softwareid
		 *
		 * @return null
		 */

		public function getCompletionSpeed($computerid, $ipaddress, $softwareid = null)
		{

			return null;
		}
	}
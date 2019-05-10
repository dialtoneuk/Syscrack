<?php

	namespace Framework\Syscrack\Game\Operations;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class Bank
	 *
	 * @package Framework\Syscrack\Game\Operations
	 */

	use Framework\Application\Settings;
	use Framework\Exceptions\SyscrackException;
	use Framework\Syscrack\Game\BaseClasses\BaseOperation;
	use Framework\Syscrack\Game\Market as Database;
	use Framework\Syscrack\Game\Metadata;

	class Market extends BaseOperation
	{

		/**
		 * @var Database
		 */

		protected static $market;

		/**
		 * @var Metadata
		 */

		protected static $metadata;

		/**
		 * Market constructor.
		 *
		 * @param bool $createclasses
		 */

		public function __construct(bool $createclasses = true)
		{

			if (isset(self::$market) == false)
				self::$market = new Database();

			if (isset(self::$metadata) == false)
				self::$metadata = new Metadata();

			parent::__construct($createclasses);
		}

		/**
		 * Returns the configuration
		 *
		 * @return array
		 */

		public function configuration()
		{

			return array(
				'allowsoftware' => false,
				'allowlocal' => false,
				'requiresoftware' => false,
				'requireloggedin' => false,
				'allowpost' => false
			);
		}

		/**
		 * @param null $ipaddress
		 *
		 * @return string
		 */

		public function url($ipaddress = null)
		{

			return ('game/internet/' . $ipaddress . "/market");
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

			$computer = self::$internet->getComputer($data['ipaddress']);

			if ($computer->type != Settings::setting('syscrack_computers_market_type'))
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
		 * @return bool|null
		 */

		public function onCompletion($timecompleted, $timestarted, $computerid, $userid, $process, array $data)
		{

			if ($this->checkData($data, ['ipaddress']) == false)
				throw new SyscrackException();

			if (self::$internet->ipExists($data['ipaddress']) == false)
				return false;

			$computer = self::$internet->getComputer($data["ipaddress"]);

			$this->render('operations/operations.market', array('ipaddress' => $data['ipaddress'],
				'metadata' => self::$metadata->get($computer->computerid),
				'items' => self::$market->getStock($computer->computerid),
				'purchases' => self::$market->getPurchases($computer->computerid)), true);

			return null;
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
		 * @param $data
		 * @param $ipaddress
		 * @param $userid
		 *
		 * @return bool
		 */

		public function onPost($data, $ipaddress, $userid)
		{

			return true;
		}
	}
<?php
	declare(strict_types=1);

	namespace Framework\Syscrack\Game\Operations;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class Buy
	 *
	 * @package Framework\Syscrack\Game\Operations
	 */

	use Framework\Application\Utilities\PostHelper;

	use Framework\Syscrack\Game\Bases\BaseOperation;
	use Framework\Syscrack\Game\Finance;
	use Framework\Syscrack\Game\Market;
	use Framework\Syscrack\Game\Utilities\TimeHelper;

	/**
	 * Class Buy
	 * @package Framework\Syscrack\Game\Operations
	 */
	class Buy extends BaseOperation
	{

		/**
		 * @var Market
		 */

		protected static $market;


		/**
		 * @var Finance
		 */

		protected static $finance;

		/**
		 * View constructor.
		 */

		public function __construct()
		{

			if (isset(self::$market) == false)
				self::$market = new Market();

			if (isset(self::$finance) == false)
				self::$finance = new Finance();

			parent::__construct();
		}

		/**
		 * Returns the configuration
		 *
		 * @return array
		 */

		public function configuration()
		{

			return [
				'allowsoftware' => false,
				'allowlocal' => false,
				'requiresoftware' => false,
				'requireloggedin' => false,
				'allowpost' => false,
				"allowcustomdata" => true,
			];
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
		 * Called on creation
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


			if ($this->checkData($data, ['ipaddress']) == false)
				return false;

			if ($this->checkCustomData($data, ['itemid', 'accountnumber']) == false)
				return false;

			if (self::$finance->accountNumberExists($data['custom']['accountnumber']) == false)
				return false;

			$computer = self::$internet->computer($data['ipaddress']);

			if (self::$computer->isMarket($computer->computerid) == false)
				return false;

			if (self::$market->hasStockItem($computer->computerid, $data['custom']['itemid']) == false)
				return false;

			$item = self::$market->getStockItem($computer->computerid, $data['custom']['itemid']);
			$account = self::$finance->getByAccountNumber($data['custom']['accountnumber']);

			if (self::$market->check($computer->computerid) == false)
				return false;

			if (self::$finance->canAfford($account->computerid, $userid, $item['price']) == false)
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

			if ($this->checkData($data, ['ipaddress']) == false)
				return false;

			if (self::$internet->ipExists($data['ipaddress']) == false)
				return false;

			if ($this->checkCustomData($data, ['itemid', 'accountnumber']) == false)
				return false;

			if (self::$finance->accountNumberExists($data['custom']['accountnumber']) == false)
				return false;

			$account = self::$finance->getByAccountNumber($data['custom']['accountnumber']);
			$item = self::$market->getStockItem($this->getComputerId($data['ipaddress']), $data['custom']['itemid']);

			if (self::$finance->canAfford($account->computerid, $account->userid, $item['price']) == false)
				return false;

			if (isset($item['hardware']) == false)
				throw new \Error();

			if (self::$hardware->hasHardwareType($computerid, $item['hardware']))
				self::$hardware->updateHardware($computerid, $item['hardware'], $item['value']);
			else
				self::$hardware->addHardware($computerid, $item['hardware'], $item['value']);

			self::$finance->withdraw($account->computerid, $userid, $item['price']);
			self::$market->addPurchase($this->getComputerId($data['ipaddress']), $computerid, $data['custom']['itemid']);
			$this->logPayment($computerid, $data['custom']['accountnumber'], $data['ipaddress']);

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
		 * @return null
		 */

		public function getCompletionSpeed($computerid, $ipaddress, $softwareid = null)
		{

			return TimeHelper::getSecondsInFuture(3);
		}

		/**
		 * Gets the custom data for this operation
		 *
		 * @param $ipaddress
		 *
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

			if (PostHelper::checkForRequirements(['accountnumber', 'itemid']) == false)
			{

				return null;
			}

			return [
				'accountnumber' => PostHelper::getPostData('accountnumber'),
				'itemid' => PostHelper::getPostData('itemid')
			];
		}

		/**
		 * @param $computerid
		 * @param $accountnumber
		 * @param $ipaddress
		 */

		private function logPayment($computerid, $accountnumber, $ipaddress)
		{

			$this->logToComputer('Successfully initiated online payment using account (' . $accountnumber . ') to server <' . $ipaddress . '>', $computerid, 'localhost');
		}
	}
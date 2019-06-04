<?php

	namespace Framework\Syscrack\Game\Operations;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class ResetAddress
	 *
	 * @package Framework\Syscrack\Game\Operations
	 */

	use Framework\Application\Settings;
	use Framework\Application\Utilities\PostHelper;

	use Framework\Syscrack\Game\Bases\BaseOperation;
	use Framework\Syscrack\Game\Finance;
	use Framework\Syscrack\Game\Utilities\TimeHelper;

	class ResetAddress extends BaseOperation
	{

		/**
		 * @var Finance
		 */

		protected static $finance;

		/**
		 * ResetAddress constructor.
		 */

		public function __construct()
		{

			if (isset(self::$finance) == false)
				self::$finance = new Finance();

			parent::__construct(true);
		}

		/**
		 * @return array
		 */

		public function configuration()
		{

			return array(
				'allowlocal' => false,
				'allowsoftware' => false,
				'requiresoftware' => false,
				'requireloggedin' => false,
				'allowcustomdata' => true
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
			{

				return false;
			}

			if ($this->checkCustomData($data, ['accountnumber']) == false)
			{

				return false;
			}

			if (self::$internet->computer($data['ipaddress'])->type != Settings::setting('syscrack_computers_isp_type'))
			{

				return false;
			}

			if (self::$finance->accountNumberExists($data['custom']['accountnumber']) == false)
				$this->redirect( $this->getRedirect($data['ipaddress'] ) );
			else
			{

				$account = self::$finance->getByAccountNumber($data['custom']['accountnumber']);

				if (self::$finance->canAfford($account->computerid, $account->userid, Settings::setting('syscrack_operations_resetaddress_price')) == false)
					$this->redirect( $this->getRedirect($data['ipaddress'] ) );;
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

			if ($this->checkData($data, ['ipaddress']) == false)
			{

				throw new \Error();
			}

			if ($this->checkCustomData($data, ['accountnumber']) == false)
			{

				throw new \Error();
			}

			if (self::$finance->accountNumberExists($data['custom']['accountnumber']) == false)
			{

				$this->redirect($this->getRedirect($data['ipaddress']));
				return false;
			}

			$account = self::$finance->getByAccountNumber($data['custom']['accountnumber']);

			if (self::$finance->canAfford($account->computerid, $account->userid, Settings::setting('syscrack_operations_resetaddress_price')) == false)
			{

				$this->redirect($this->getRedirect($data['ipaddress']));
				return false;
			}

			self::$finance->withdraw($account->computerid, $account->userid, Settings::setting('syscrack_operations_resetaddress_price'));

			self::$internet->changeAddress($computerid);

			self::$log->updateLog('Changed ip address for ' . Settings::setting('syscrack_currency') . number_format(Settings::setting('syscrack_operations_resetaddress_price')) . ' using account ' . $account->accountnumber,
				self::$computer->computerid(),
				'localhost');

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

			return TimeHelper::getSecondsInFuture(Settings::setting('syscrack_operations_resetaddress_time'));
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

			if (PostHelper::checkForRequirements(['accountnumber']) == false)
			{

				return null;
			}

			return array(
				'accountnumber' => PostHelper::getPostData('accountnumber')
			);
		}

		/**
		 * @param $data
		 * @param $ipaddress
		 * @param $userid
		 */

		public function onPost($data, $ipaddress, $userid)
		{
			// TODO: Implement onPost() method.
		}
	}
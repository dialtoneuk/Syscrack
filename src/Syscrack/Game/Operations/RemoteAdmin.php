<?php

	namespace Framework\Syscrack\Game\Operations;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class RemoteAdmin
	 *
	 * @package Framework\Syscrack\Game\Operations
	 */

	use Framework\Application\Settings;
	use Framework\Application\Utilities\PostHelper;
	use Framework\Exceptions\SyscrackException;
	use Framework\Syscrack\Game\Bases\BaseOperation;
	use Framework\Syscrack\Game\Finance;


	class RemoteAdmin extends BaseOperation
	{

		/**
		 * @var Finance
		 */

		protected static $finance;

		/**
		 * View constructor.
		 */

		public function __construct()
		{

			if (isset(self::$finance) == false)
				self::$finance = new Finance();

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
				'allowsoftware' => false,
				'allowlocal' => false,
				'requiresoftware' => false,
				'requireloggedin' => false,
				'allowpost' => true
			);
		}

		/**
		 * @param null $ipaddress
		 *
		 * @return string
		 */

		public function url($ipaddress = null)
		{

			if ($ipaddress == null)
				return (parent::url($ipaddress));

			return ('game/internet/' . @$ipaddress . '/remoteadmin');
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

			if ($computer->type != Settings::setting('syscrack_computers_bank_type'))
				return false;

			if (self::$finance->hasCurrentActiveAccount() == false)
				return false;

			else
			{

				if (self::$finance->accountNumberExists(self::$finance->getCurrentActiveAccount()) == false)
				{

					if (Settings::setting('syscrack_operations_bank_clearonfail'))
						self::$finance->setCurrentActiveAccount(null);

					return false;
				}

				if (self::$finance->getByAccountNumber(self::$finance->getCurrentActiveAccount())->computerid !== $computer->computerid)
					return false;

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
		 * @return bool
		 */

		public function onCompletion($timecompleted, $timestarted, $computerid, $userid, $process, array $data)
		{

			if ($this->checkData($data, ['ipaddress']) == false)
				throw new SyscrackException();

			if (self::$finance->accountNumberExists(self::$finance->getCurrentActiveAccount()) == false)
				return false;
			else
				$this->render('operations/operations.bank.adminaccount',
					array(
						'ipaddress' => $data['ipaddress'],
						'userid' => $userid,
						'account' => self::$finance->getByAccountNumber(self::$finance->getCurrentActiveAccount()),
						'accounts' => self::$finance->getUserBankAccounts($userid),
						'accounts_location' => $this->getAddresses(self::$finance->getUserBankAccounts($userid)),
						'computer' => self::$internet->getComputer($data['ipaddress'])
					), true);

			return null;
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

			if (PostHelper::hasPostData() == false)
			{

				$this->redirect($this->getRedirect($ipaddress) . '/remoteadmin');
			}

			if (PostHelper::checkForRequirements(['action']) == false)
				return false;
			else
			{

				if (self::$finance->hasCurrentActiveAccount() == false)
					return false;

				if (self::$finance->accountNumberExists(self::$finance->getCurrentActiveAccount()) == false)
				{

					if (Settings::setting('syscrack_operations_bank_clearonfail'))
					{

						self::$finance->setCurrentActiveAccount(null);
					}

					return false;
				}

				$action = PostHelper::getPostData('action');

				if ($action == 'transfer')
				{

					if (PostHelper::checkForRequirements(['accountnumber', 'ipaddress', 'amount']))
					{

						$accountnumber = PostHelper::getPostData('accountnumber');
						$targetipaddress = PostHelper::getPostData('ipaddress');
						$amount = PostHelper::getPostData('amount');

						if (is_numeric($amount) == false)
						{

							return false;
						}

						$amount = abs($amount);

						if (empty($amount) || $amount == 0)
						{

							return false;
						}

						if (self::$finance->accountNumberExists($accountnumber) == false)
						{

							return false;
						}

						if (self::$internet->ipExists($targetipaddress) == false)
						{

							return false;
						}

						$account = self::$finance->getByAccountNumber($accountnumber);

						$activeaccount = self::$finance->getByAccountNumber(self::$finance->getCurrentActiveAccount());

						if (self::$computer->getComputer($account->computerid)->ipaddress !== $targetipaddress)
						{

							return false;
						}

						if (self::$finance->canAfford($activeaccount->computerid, $activeaccount->userid, $amount) == false)
						{

							return false;
						}

						self::$finance->deposit($account->computerid, $account->userid, $amount);

						self::$finance->withdraw($activeaccount->computerid, $activeaccount->userid, $amount);

						$this->logActions('Transfered ' . Settings::setting('syscrack_currency') . number_format($amount) . ' from (' . self::$finance->getCurrentActiveAccount() . ') to (' . $account->accountnumber . ') to bank <' . $targetipaddress . '>',
							self::$computer->computerid(),
							$ipaddress);

						return false;
					}
					else
					{

						return false;
					}
				}
				else if ($action == "disconnect")
				{

					self::$finance->setCurrentActiveAccount(null);

					$this->redirect($this->getRedirect($ipaddress));
				}
			}

			return true;
		}
	}

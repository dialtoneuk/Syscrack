<?php

	namespace Framework\Syscrack\Game;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class Finance
	 *
	 * @package Framework\Syscrack\Game
	 */

	use Framework\Application\Settings;
	use Framework\Database\Tables\Banks;
	use Framework\Database\Tables\Computer;
	use Framework\Exceptions\SyscrackException;

	class Finance
	{

		/**
		 * @var Computer
		 */

		protected $computers;

		/**
		 * @var Banks
		 */

		protected $banks;

		/**
		 * Finance constructor.
		 */

		public function __construct()
		{

			$this->computers = new Computer();

			$this->banks = new Banks();
		}

		/**
		 * @param int $pick
		 *
		 * @return \Illuminate\Support\Collection
		 */

		public function getAllAccounts($pick = 32)
		{

			return $this->banks->getAllAccounts($pick = 32);
		}

		/**
		 * Gets the number of accounts
		 *
		 * @return int
		 */

		public function getAccountCount()
		{

			return $this->banks->getAccountCount();
		}

		/**
		 * Gets the users cash at the specified bank
		 *
		 * @param $userid
		 *
		 * @param $computerid
		 *
		 * @return int
		 */

		public function getUserCash($computerid, $userid)
		{

			$account = $this->getAccountAtBank($computerid, $userid);

			if ($account == null)
			{

				throw new SyscrackException();
			}

			return $account->cash;
		}

		/**
		 * Gets the total cash of a user
		 *
		 * @param $userid
		 *
		 * @return int
		 */

		public function getTotalUserCash($userid)
		{

			$banks = $this->getUserBankAccounts($userid);

			$sum = 0;

			foreach ($banks as $bank)
			{

				$sum += $bank->cash;
			}

			return $sum;
		}

		/**
		 * Gets all the computers who are banks
		 *
		 * @return mixed|null
		 */

		public function getBanks()
		{

			return $this->computers->getComputerByType(Settings::setting('syscrack_computers_bank_type'));
		}

		/**
		 * Removes an account from the bank
		 *
		 * @param $computerid
		 *
		 * @param $userid
		 */

		public function removeAccount($computerid, $userid)
		{

			$this->banks->deleteAccount($computerid, $userid);
		}

		/**
		 * Gets the users account at the specified bank
		 *
		 * @param $userid
		 *
		 * @param $computerid
		 *
		 * @return mixed|null
		 */

		public function getAccountAtBank($computerid, $userid)
		{

			$accounts = $this->banks->getAccountsOnComputer($computerid);

			if (empty($accounts))
			{

				return null;
			}

			foreach ($accounts as $account)
			{

				if ($account->userid == $userid)
				{

					return $account;
				}
			}

			return null;
		}

		public function setCurrentActiveAccount($accountnumber)
		{

			if (session_status() !== PHP_SESSION_ACTIVE)
			{

				throw new SyscrackException();
			}

			$_SESSION['activeaccount'] = $accountnumber;
		}

		public function getCurrentActiveAccount()
		{

			return $_SESSION['activeaccount'];
		}

		public function hasCurrentActiveAccount()
		{

			if (isset($_SESSION['activeaccount']) == false)
			{

				return false;
			}

			return true;
		}

		/**
		 * Gets the users bank account
		 *
		 * @param $userid
		 *
		 * @return \Illuminate\Support\Collection|null
		 */

		public function getUserBankAccounts($userid)
		{

			return $this->banks->getUserAccounts($userid);
		}

		/**
		 * Gets the account by its account number
		 *
		 * @param $accountnumber
		 *
		 * @return \Illuminate\Support\Collection|null
		 */

		public function getByAccountNumber($accountnumber)
		{

			return $this->banks->getByAccountNumber($accountnumber);
		}

		/**
		 * Returns true if the account number exists
		 *
		 * @param $accountnumber
		 *
		 * @return bool
		 */

		public function accountNumberExists($accountnumber)
		{

			if ($this->banks->getByAccountNumber($accountnumber) == null)
			{

				return false;
			}

			return true;
		}

		/**
		 * Returns true if the user has an account
		 *
		 * @param $userid
		 *
		 * @return bool
		 */

		public function hasAccount($userid)
		{

			if ($this->banks->getUserAccounts($userid) == null)
			{

				return false;
			}

			return true;
		}

		/**
		 * Returns true if we have an account at this computer
		 *
		 * @param $computerid
		 *
		 * @param $userid
		 *
		 * @return bool
		 */

		public function hasAccountAtComputer($computerid, $userid)
		{

			if ($this->getAccountAtBank($computerid, $userid) === null)
				return false;

			return true;
		}

		/**
		 * @param $computerid
		 *
		 * @param $userid
		 *
		 * @return int
		 */

		public function createAccount($computerid, $userid)
		{


			if ($this->getAccountAtBank($computerid, $userid) !== null)
			{

				throw new SyscrackException();
			}

			$this->banks->insertAccount(array(
				'computerid' => $computerid,
				'userid' => $userid,
				'accountnumber' => $this->getAccountNumber(),
				'cash' => Settings::setting('syscrack_bank_default_balance'),
				'timecreated' => time()
			));

			return $this->getAccountNumber();
		}

		/**
		 * Deposits ( adds ) money into an account
		 *
		 * @param $userid
		 *
		 * @param $computerid
		 *
		 * @param $amount
		 */

		public function deposit($computerid, $userid, $amount)
		{

			$this->banks->updateAccount($computerid, $userid, array(
				'cash' => $this->getUserCash($computerid, $userid) + $amount
			));
		}

		/**
		 * Withdraws ( takes ) money from a specified account
		 *
		 * @param $userid
		 *
		 * @param $amount
		 *
		 * @param $computerid
		 */

		public function withdraw($computerid, $userid, $amount)
		{

			$this->banks->updateAccount($computerid, $userid, array(
				'cash' => $this->getUserCash($computerid, $userid) - $amount
			));
		}

		/**
		 * Returns true if the user has enough cash to afford this transaction
		 *
		 * @param $userid
		 *
		 * @param int $amount
		 *
		 * @param $computerid
		 *
		 * @return bool
		 */

		public function canAfford($computerid, $userid, int $amount)
		{

			$cash = $this->getUserCash($computerid, $userid);

			if ($cash - $amount >= 0)
			{

				return true;
			}

			return false;
		}

		/**
		 * Gets the account number
		 *
		 * @return int|string
		 */

		private function getAccountNumber()
		{

			$number = 0;

			for ($i = 0; $i < Settings::setting('syscrack_bank_accountnumber_length'); $i++)
			{

				$number = $number . rand(0, 9);
			}

			return $number;
		}

		/**
		 * Returns true if the comptuer id is a bank
		 *
		 * @param $computerid
		 *
		 * @return bool
		 */

		private function isBank($computerid)
		{

			if ($this->computers->getComputer($computerid)->type != Settings::setting('syscrack_computers_bank_type'))
			{

				return false;
			}

			return true;
		}
	}
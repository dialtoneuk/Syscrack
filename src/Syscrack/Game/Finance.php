<?php
	declare(strict_types=1);

	namespace Framework\Syscrack\Game;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class Finance
	 *
	 * @package Framework\Syscrack\Game
	 */

	use Framework\Application\Settings;
	use Framework\Application\UtilitiesV2\Container;
	use Framework\Database\Tables\Banks;
	use Framework\Database\Tables\Computer;

	/**
	 * Class Finance
	 * @package Framework\Syscrack\Game
	 */
	class Finance
	{

		/**
		 * @var Computer
		 */

		protected static $computers;

		/**
		 * @var Banks
		 */

		protected static $banks;

		/**
		 * Finance constructor.
		 */

		public function __construct()
		{

			if( isset( self::$computers ) == false )
				self::$computers = new Computer();

			if( isset( self::$banks ) == false )
				self::$banks = new Banks();
		}

		/**
		 * @return \Illuminate\Support\Collection
		 */

		public function getAllAccounts()
		{

			return self::$banks->getAllAccounts();
		}

		/**
		 * Gets the number of accounts
		 *
		 * @return int
		 */

		public function getAccountCount()
		{

			return self::$banks->getAccountCount();
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
				throw new \Error();

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

			if( empty( $banks ) )
				return 0;

			$sum = 0;

			foreach ($banks as $bank)
				$sum += $bank->cash;

			return $sum;
		}

		/**
		 * Gets all the computers who are banks
		 *
		 * @return mixed|null
		 */

		public function getBanks()
		{

			return self::$computers->getComputerByType(Settings::setting('computers_type_bank'));
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

			self::$banks->deleteAccount($computerid, $userid);
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

			$accounts = self::$banks->getAccountsOnComputer($computerid);

			if (empty($accounts))
				return null;

			foreach ($accounts as $account)
				if ($account->userid == $userid)
					return $account;

			return null;
		}

		/**
		 * @param $accountnumber
		 */

		public function setCurrentActiveAccount($accountnumber)
		{

			if( Container::exist('session') == false )
				return;

			if( Container::get('session')->isLoggedIn() == false )
				return;

			$_SESSION['activeaccount'] = $accountnumber;
		}

		/**
		 * @return mixed
		 */

		public function getCurrentActiveAccount()
		{

			return $_SESSION['activeaccount'];
		}

		/**
		 * @return bool
		 */

		public function hasCurrentActiveAccount()
		{

			if (isset($_SESSION['activeaccount']) == false)
				return false;

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

			return self::$banks->getUserAccounts($userid);
		}

		/**
		 * Gets the account by its account number
		 *
		 * @param $accountnumber
		 *
		 * @return \Illuminate\Support\Collection|null|\stdClass
		 */

		public function getByAccountNumber($accountnumber)
		{

			return self::$banks->getByAccountNumber($accountnumber);
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

			if (self::$banks->getByAccountNumber($accountnumber) == null)
				return false;

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

			if (self::$banks->getUserAccounts($userid) == null)
				return false;


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
				throw new \Error();

			self::$banks->insertAccount([
				'computerid' => $computerid,
				'userid' => $userid,
				'accountnumber' => $this->getAccountNumber(),
				'cash' => Settings::setting('bank_default_balance'),
				'timecreated' => time()
			]);

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

			self::$banks->updateAccount($computerid, $userid, [
				'cash' => $this->getUserCash($computerid, $userid) + $amount
			]);
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

			self::$banks->updateAccount($computerid, $userid, [
				'cash' => $this->getUserCash($computerid, $userid) - $amount
			]);
		}

		/**
		 * Returns true if the user has enough cash to afford this transaction
		 *
		 * @param $userid
		 *
		 * @param float $amount
		 *
		 * @param $computerid
		 *
		 * @return bool
		 */

		public function canAfford($computerid, $userid, float $amount)
		{

			$cash = $this->getUserCash($computerid, $userid);

			if ($cash - $amount >= 0)
				return true;

			return false;
		}

		/**
		 * Gets the account number
		 *
		 * @return int|string
		 */

		private function getAccountNumber()
		{
			return mt_rand( Settings::setting('bank_minnumber'), Settings::setting('bank_maxnumber'));
		}
	}
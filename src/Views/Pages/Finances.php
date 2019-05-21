<?php

	namespace Framework\Views\Pages;

	/**
	 * Lewis Lancaster 2016
	 *
	 * Class Finances
	 *
	 * @package Framework\Views\Pages
	 */

	use Framework\Application\Render;
	use Framework\Application\Utilities\PostHelper;
	use Framework\Syscrack\Game\AccountDatabase;
	use Framework\Syscrack\Game\Finance;
	use Framework\Syscrack\Game\Metadata;
	use Framework\Views\BaseClasses\Page as BaseClass;

	class Finances extends BaseClass
	{

		/**
		 * @var Finance
		 */

		protected static $finance;

		/**
		 * @var Metadata
		 */

		protected static $metadata;

		/**
		 * @var AccountDatabase
		 */

		protected static $bankdatabase;


		/**
		 * Finances constructor.
		 */

		public function __construct()
		{

			if (isset(self::$finance) == false)
				self::$finance = new Finance();

			if (isset(self::$metadata) == false)
				self::$metadata = new Metadata();

			if (isset(self::$bankdatabase) == false)
				self::$bankdatabase = new AccountDatabase();

			parent::__construct(true, true, true, true);
		}

		/**
		 * Returns the pages flight mapping
		 *
		 * @return array
		 */

		public function mapping()
		{

			return array(
				[
					'/finances/', 'page'
				],
				[
					'GET /finances/transfer/', 'transfer'
				],
				[
					'POST /finances/transfer', 'transferProcess'
				]
			);
		}

		/**
		 * Default page
		 */

		public function page()
		{

			$userid = self::$session->userid();
			$accounts = self::$finance->getUserBankAccounts($userid);
			$addresses = [];
			$metaset = [];

			if (empty($accounts) == false)
			{

				foreach ($accounts as $account)
				{
					$addresses[$account->computerid] = @self::$computer->getComputer($account->computerid)->ipaddress;
					$metaset[$account->computerid] = @self::$metadata->get($account->computerid);
				}
			}
			else
				$accounts = [];

			$this->getRender('syscrack/page.finances', ['accounts' => $accounts, 'cash' => self::$finance->getTotalUserCash($userid), 'bankdatabase' => self::$bankdatabase->getDatabase($userid), 'addresses' => $addresses, 'metaset' => $metaset], self::$computer->computerid(), $userid);
		}

		public function transfer()
		{

			Render::view('syscrack/page.finances.transfer', [], $this->model());
		}

		public function transferProcess()
		{

			if (PostHelper::hasPostData() == false)
			{

				$this->page();
			}
			else
			{

				if (PostHelper::checkForRequirements(['accountnumber', 'targetaccount', 'ipaddress', 'amount']) == false)
				{

					$this->formError('Missing information', 'finances/transfer');
				}

				$accountnumber = PostHelper::getPostData('accountnumber');
				$targetaccount = PostHelper::getPostData('targetaccount');
				$ipaddress = PostHelper::getPostData('ipaddress');
				$amount = PostHelper::getPostData('amount');

				if (is_numeric($amount) == false)
				{

					$this->formError('Please enter a number for the amount', 'finances/transfer');
				}

				$amount = abs($amount);

				if ($amount == 0)
				{

					$this->formError('Please enter a number higher than zero', 'finances/transfer');
				}

				if ($accountnumber == $targetaccount)
				{

					$this->formError('You cant transfer to your self, funnily enough', 'finances/transfer');
				}

				if (self::$finance->accountNumberExists($accountnumber) == false)
				{

					$this->formError('Account does not exist', 'finances/transfer');
				}

				if (self::$finance->accountNumberExists($targetaccount) == false)
				{

					$this->formError('Account does not exist', 'finances/transfer');
				}

				$account = self::$finance->getByAccountNumber($accountnumber);

				if ($account->userid !== self::$session->userid())
				{

					$this->formError('You do not own this account', 'finances/transfer');
				}

				$target = self::$finance->getByAccountNumber($targetaccount);

				if ($this->computer->getComputer($target->computerid)->ipaddress !== $ipaddress)
				{

					$this->formError('Account does not exist at remote bank', 'finances/transfer');
				}

				if (self::$finance->canAfford($account->computerid, self::$session->userid(), $amount) == false)
				{

					$this->formError('You cannot afford this transaction', 'finances/transfer');
				}

				self::$finance->deposit($target->computerid, $target->userid, $amount);

				self::$finance->withdraw($account->computerid, $account->userid, $amount);

				$this->formSuccess('finances/transfer');
			}
		}
	}
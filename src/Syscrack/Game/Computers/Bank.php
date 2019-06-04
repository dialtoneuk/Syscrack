<?php

	namespace Framework\Syscrack\Game\Computers;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class Bank
	 *
	 * @package Framework\Syscrack\Game\Computer
	 */


	use Framework\Syscrack\Game\Bases\BaseComputer;
	use Framework\Syscrack\Game\Finance;

	class Bank extends BaseComputer
	{

		/**
		 * @var Finance
		 */

		protected static $finance;

		/**
		 * Npc constructor.
		 */

		public function __construct()
		{

			if (isset(self::$finance) == false)
				self::$finance = new Finance();

			parent::__construct(true);
		}

		/**
		 * The configuration of this computer
		 *
		 * @return array
		 */

		public function configuration()
		{

			return array(
				'installable' => false,
				'type' => 'bank',
				'reloadable' => true,
			);
		}

		/**
		 * What to do when you login to this computer
		 *
		 * @param $computerid
		 *
		 * @param $ipaddress
		 */

		public function onLogin($computerid, $ipaddress)
		{

			if (self::$internet->ipExists($ipaddress) == false)
				throw new \Error();

			self::$internet->setCurrentConnectedAddress($ipaddress);

			$this->log($computerid, 'Logged in as root', $this->localhost());
			$this->logRemote($this->localhost(), 'Logged in as root at <' . $ipaddress . '>');

			parent::onLogin( $computerid, $ipaddress );
		}

		/**
		 * What to do when you logout of a computer
		 *
		 * @param $computerid
		 *
		 * @param $ipaddress
		 */

		public function onLogout($computerid, $ipaddress)
		{

			if (self::$internet->ipExists($ipaddress) == false)
				throw new \Error();

			if (self::$finance->hasCurrentActiveAccount() == true)
				self::$finance->setCurrentActiveAccount(null);

			self::$internet->setCurrentConnectedAddress(null);
		}
	}
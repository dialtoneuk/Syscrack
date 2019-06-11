<?php
	declare(strict_types=1);

	namespace Framework\Syscrack\Game\Computers;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class Vpc
	 *
	 * @package Framework\Syscrack\Game\Computer
	 */

	use Framework\Application\Settings;

	use Framework\Syscrack\Game\AccountDatabase;
	use Framework\Syscrack\Game\AddressDatabase;
	use Framework\Syscrack\Game\Bases\BaseComputer;
	use Framework\Syscrack\Game\Inventory;

	/**
	 * Class Vpc
	 * @package Framework\Syscrack\Game\Computers
	 */
	class Vpc extends BaseComputer
	{

		/**
		 * @var AddressDatabase
		 */

		protected static $addressdatabase;

		/**
		 * @var AccountDatabase
		 */

		protected static $accountdatabase;

		/**
		 * @var Inventory
		 */

		protected static $inventory;
		/**
		 * Vpc constructor.
		 */

		public function __construct()
		{

			if (isset(self::$addressdatabase) == false)
				self::$addressdatabase = new AddressDatabase();

			if (isset(self::$accountdatabase) == false)
				self::$accountdatabase = new AccountDatabase();

			if( isset( self::$inventory ) == false )
				self::$inventory = new Inventory();

			parent::__construct(true);
		}

		/**
		 * The configuration
		 *
		 * @return array
		 */

		public function configuration()
		{

			return [
				'installable' => true,
				'type' => 'vpc',
				'reloadable' => false,
			];
		}

		/**
		 * @param $computerid
		 * @param $userid
		 * @param array $software
		 * @param array $hardware
		 * @param array $custom
		 */

		public function onStartup($computerid, $userid, array $software = [], array $hardware = [], array $custom = [])
		{

			if (self::$addressdatabase->hasDatabase($userid) == false)
				self::$addressdatabase->saveDatabase($userid);

			if (self::$accountdatabase->hasDatabase($userid) == false)
				self::$accountdatabase->saveDatabase($userid, []);

			if( empty( self::$inventory->get( $userid )->contents()["items"] ) )
				self::$inventory->save( $userid, self::$inventory::dataInstance(["items" => []]));

			parent::onStartup($computerid, $userid, $software, $hardware, $custom);
		}

		/**
		 * What to do on reset
		 *
		 * @param $computerid
		 */

		public function onReset($computerid)
		{

			$userid = self::$computer->getComputer($computerid)->userid;

			if (self::$addressdatabase->hasDatabase($userid) == false)
				self::$addressdatabase->saveDatabase($userid, []);

			if (self::$accountdatabase->hasDatabase($userid) == false)
				self::$accountdatabase->saveDatabase($userid, []);

			if( Settings::setting("inventory_reset") )
				self::$inventory->save( $userid, self::$inventory::dataInstance(["items" => []]));

			parent::onReset($computerid);
		}

		/**
		 * What to do on login
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

			parent::onLogin( $computerid, $ipaddress);
		}

		/**
		 * What do on logout
		 *
		 * @param $computerid
		 *
		 * @param $ipaddress
		 */

		public function onLogout($computerid, $ipaddress)
		{

			if (self::$internet->ipExists($ipaddress) == false)
				throw new \Error();

			self::$internet->setCurrentConnectedAddress(null);

			parent::onLogout( $computerid, $ipaddress );
		}
	}
<?php
	declare(strict_types=1);
	/**
	 * Created by PhpStorm.
	 * User: newsy
	 * Date: 13/05/2019
	 * Time: 22:45
	 */

	namespace Framework\Syscrack\Game\Bases;

	use Framework\Syscrack\Game\Computer;
	use Framework\Syscrack\Game\Software;
	use Framework\Syscrack\Game\Interfaces\Item;
	use Framework\Syscrack\User;
	use Framework\Application\UtilitiesV2\Conventions\ItemSettingData;

	/**
	 * Class BaseItem
	 * @package Framework\Syscrack\Game\Bases
	 */
	class BaseItem implements Item
	{

		/**
		 * @var Computer
		 */

		protected static $computer;
		/**
		 * @var User
		 */
		protected static $user;
		/**
		 * @var Software
		 */
		protected static $software;

		/**
		 * BaseItem constructor.
		 */

		public function __construct()
		{

			if( isset( self::$computer ) == false )
				self::$computer = new Computer();

			if( isset( self::$user ) == false )
				self::$user = new User();

			if( isset( self::$software ) == false )
				self::$software = new Software();
		}

		/**
		 * @param ItemSettingData|null $data
		 *
		 * @return ItemSettingData
		 */

		public function settings( ItemSettingData $data = null ): ItemSettingData
		{

			if( $data == null )
				$data = new ItemSettingData([]);

			$data->tradeable = true;
			$data->equippable = false;

			return( $data );
		}

		/**
		 * @param $itemid
		 * @param $userid
		 * @param $targetid
		 *
		 * @return bool
		 */

		public function used( $itemid, $userid, $targetid ): bool
		{

			return( true );
		}

		/**
		 * @param $itemid
		 * @param $userid
		 * @param $computerid
		 *
		 * @return bool
		 */

		public function equipped($itemid, $userid, $computerid): bool
		{

			return( true );
		}

		/**
		 * @param $itemid
		 * @param $userid
		 * @param $targetid
		 *
		 * @return bool
		 */

		public function traded( $itemid, $userid, $targetid ): bool
		{

			return( true );
		}
	}
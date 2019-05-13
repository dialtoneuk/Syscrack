<?php
	/**
	 * Created by PhpStorm.
	 * User: newsy
	 * Date: 13/05/2019
	 * Time: 22:45
	 */

	namespace Framework\Syscrack\Game\Bases;

	use Framework\Syscrack\Game\Interfaces\Item;
	use Framework\Syscrack\Computer;
	use Framework\Syscrack\User;
	use Framework\Syscrack\Software;
	use Framework\Application\UtilitiesV2\Conventions\ItemSettingData;

	class BaseItem extends Item
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
		 *
		 * @return bool
		 */

		public function used( $itemid, $userid ): bool
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

		public function equipped( $itemid, $userid, $computerid )
		{

			return ( true );
		}

		/**
		 * @param $itemid
		 * @param $userid
		 * @param $targetid
		 *
		 * @return bool
		 */

		public function traded( $itemid, $userid, $targetid )
		{

			return( true );
		}
	}
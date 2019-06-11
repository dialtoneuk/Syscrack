<?php
	declare(strict_types=1);
	/**
	 * Created by PhpStorm.
	 * User: newsy
	 * Date: 13/05/2019
	 * Time: 22:53
	 */

	namespace Framework\Syscrack\Game;

	use Framework\Application;
	use Framework\Application\UtilitiesV2\Constructor;
	use Framework\Syscrack\Game\Bases\BaseItem;
	use Framework\Application\UtilitiesV2\Conventions\ItemSettingData;

	/**
	 * Class Items
	 * @package Framework\Syscrack\Game
	 */
	class Items
	{

		/**
		 * @var array
		 */
		protected static $items;

		/**
		 * Items constructor.
		 *
		 * @param bool $auto_create
		 */

		public function __construct( $auto_create = true )
		{

			if( isset( self::$items ) == false && $auto_create )
				$this->create();
		}

		/**
		 * Creates the item classes ready for reading
		 */

		public function create()
		{

			if( isset( self::$items ) == false )
				self::$items = [];
			else
				return null;

			$constructor = new Constructor(  Application::globals()->ITEMS_FILEPATH , Application::globals()->ITEMS_NAMESPACE );
			$result = @$constructor->createAll( true );

			if( $result === false )
				throw new \Error("Unable to create item classes");

			self::$items = $result;
		}

		/**
		 * @param $item
		 *
		 * @return ItemSettingData|\stdClass
		 */

		public function settings( $item ): ItemSettingData
		{


			if( $this->has( $item ) == false )
				throw new \Error("item does not exist: " . $item );

			$class = $this->item( $item );
			return( $class->settings() );
		}

		/**
		 * @param $item
		 * @param $itemid
		 * @param $userid
		 * @param $targetid
		 *
		 * @return bool
		 */

		public function used( $item, $itemid, $userid, $targetid ): bool
		{

			if( $this->has( $item ) == false )
				throw new \Error("item does not exist: " . $item );

			$class = $this->item( $item );
			return( $class->used( $itemid, $userid, $targetid ) );
		}

		/**
		 * @param $item
		 * @param $itemid
		 * @param $userid
		 * @param $computerid
		 *
		 * @return bool
		 */

		public function equipped( $item, $itemid, $userid, $computerid ): bool
		{

			if( $this->has( $item ) == false )
				throw new \Error("item does not exist: " . $item );

			$class = $this->item( $item );
			return( $class->used( $itemid, $userid, $computerid ) );
		}

		/**
		 * @param $item
		 * @param $itemid
		 * @param $userid
		 * @param $targetid
		 *
		 * @return bool
		 */

		public function traded( $item, $itemid, $userid, $targetid ): bool
		{

			if( $this->has( $item ) == false )
				throw new \Error("item does not exist: " . $item );

			$class = $this->item( $item );
			return( $class->used( $itemid, $userid, $targetid ) );
		}

		/**
		 * @param $item
		 *
		 * @return BaseItem
		 */

		public function item( $item ): BaseItem
		{

			return(self::$items->$item);
		}

		/**
		 * @param $item
		 *
		 * @return bool
		 */

		public function has( $item ): bool
		{

			return( isset(self::$items->$item) );
		}
	}
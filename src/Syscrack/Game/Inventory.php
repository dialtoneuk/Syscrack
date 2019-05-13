<?php
	/**
	 * Created by PhpStorm.
	 * User: newsy
	 * Date: 13/05/2019
	 * Time: 23:06
	 */

	namespace Framework\Syscrack\Game;

	use Framework\Application\Utilities\FileSystem;
	use Framework\Syscrack\Game\Items;
	use Framework\Syscrack\Game\Bases\BaseItem;
	use Framework\Application\UtilitiesV2\Conventions\ItemData;
	use Framework\Application\UtilitiesV2\Conventions\InventoryData;
	use Framework\Application\UtilitiesV2\Conventions\ItemSettingsData;

	class Inventory
	{

		/**
		 * @var Items
		 */
		protected static $items;
		/**
		 * @var array
		 */
		protected $last_inventory = [];

		/**
		 * Inventory constructor.
		 */

		public function __construct()
		{

			if( isset( self::$items ) == false )
				self::$items = new Items( true );
		}

		/**
		 * @param $userid
		 * @param InventoryData $inventory
		 */

		public function save( $userid, InventoryData $inventory )
		{

			$this->last_inventory = $inventory->contents();
			$this->write( $userid, $inventory );
		}

		/**
		 * @param $userid
		 *
		 * @return InventoryData
		 */

		public function get( $userid ):InventoryData
		{

			$data = @$this->read( $this->path( $userid ) );

			if( $data === false )
				throw new \Error("Path does not exist: " . $this->path( $userid ) );

			$this->last_inventory = $data;

			return( new InventoryData( $data ) );
		}

		/**
		 * @param $item
		 *
		 * @return BaseItem
		 */

		public function instance( $item ): BaseItem
		{

			return( self::$items->item( $item ) );
		}

		/**
		 * @param $itemid
		 *
		 * @return mixed
		 */

		public function find( $itemid ): ItemData
		{

			if( $this->exists( $itemid ) == false )
				throw new \Error("Item does not exist: " . $itemid );

			$inventory = $this->get( $userid );
			return( new ItemData( $inventory->items[ $itemid ] ) );
		}

		/**
		 * @param $item
		 * @param $userid
		 *
		 * @return InventoryData
		 */

		public function add( $item, $userid ): InventoryData
		{

			if( self::$items->has( $item ) == false )
				throw new \Error("Item does not exist: " . $item );

			$inventory = $this->get( $userid );
			$settings = self::$items->settings( $item );
			$data = new ItemData();
			$itemid = $inventory->lastid + 1;

			$inventory->items[ $itemid ] = [
				'userid'        => @$settings->userid,
				'name'          => @$settings->name,
				'icon'          => @$settings->icon,
				'description'   => @$settings->description,
				'item'          => @$item
			]

			$inventory->lastid = $itemid;
			return( $inventory );
		}

		/**
		 * @param $itemid
		 * @param $userid
		 *
		 * @return bool
		 */

		public function has( $itemid, $userid ): bool
		{

			$inventory = $this->get( $userid );
			return( isset( $inventory->items[ $itemid ] ) )
		}

		/**
		 * @param $itemid
		 * @param $userid
		 *
		 * @return InventoryData
		 */

		public function delete( $itemid, $userid ): InventoryData
		{

			if( $this->exists( $itemid ) == false )
				throw new \Error("Item does not exist: " . $itemid );

			$inventory = $this->get( $userid );
			unset( $inventory->items[ $itemid ] );
			return( $inventory );
		}

		/**
		 * @return InventoryData
		 */

		public function last():InventoryData
		{

			return( new InventoryData( $this->last_inventory ) );
		}

		/**
		 * @param $userid
		 *
		 * @return string
		 */

		private function path( $userid ):string
		{

			return( Settings:setting("inventory_filepath") . $userid );
		}

		/**
		 * @param $path
		 *
		 * @return array
		 */

		private function read( $path ):array
		{

			if( FileSystem::exists( $path ) == false )
				throw new \Error("Path does not exist: " . $path );

			return( FileSystem:readJson( $path ) );
		}

		/**
		 * @param $path
		 * @param InventoryData $inventory
		 */

		private function write( $path, InventoryData $inventory ): void
		{

			if( FileSystem::exists( $path ) == false )
				throw new \Error("Path does not exist: " . $path );

			FileSystem::write( $path, $inventory->contents() );
		}
	}
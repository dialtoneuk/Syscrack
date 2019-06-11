<?php
	declare(strict_types=1);
	/**
	 * Created by PhpStorm.
	 * User: newsy
	 * Date: 13/05/2019
	 * Time: 23:06
	 */

	namespace Framework\Syscrack\Game;

	use Framework\Application\Settings;
	use Framework\Application\Utilities\FileSystem;
	use Framework\Syscrack\Game\Bases\BaseItem;
	use Framework\Application\UtilitiesV2\Conventions\ItemData;
	use Framework\Application\UtilitiesV2\Conventions\InventoryData;

	/**
	 * Class Inventory
	 * @package Framework\Syscrack\Game
	 */
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
			$this->write( $this->path( $userid ), $inventory );
		}

		/**
		 * @param $userid
		 *
		 * @return InventoryData
		 */

		public function get( $userid ):?InventoryData
		{

			$data = @$this->read( $this->path( $userid ) );

			if( $data === false || empty( $data ) )
				return self::dataInstance(["items" => []]);

			$this->last_inventory = $data;

			return( self::dataInstance( $data ) );
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
		 * @param $userid
		 *
		 * @return ItemData
		 */

		public function find( $itemid, $userid ): ItemData
		{

			if( $this->exists( $itemid, $userid ) == false )
				throw new \Error("Item does not exist: " . $itemid );

			$inventory = $this->get( $userid );
			return( new ItemData( $inventory->items[ $itemid ] ) );
		}

		/**
		 * @param $item
		 * @param $userid
		 *
		 * @return int
		 */

		public function add( $item, $userid ): int
		{

			if( self::$items->has( $item ) == false )
				throw new \Error("Item does not exist: " . $item );

			$inventory = $this->get( $userid )->contents();
			$settings = self::$items->settings( $item );
			$itemid = @$inventory["lastid"] + 1;
			$inventory["items"][ $itemid ] = [
					'userid'        => $userid,
					'name'          => @$settings->name,
					'icon'          => @$settings->icon,
					'description'   => @$settings->description,
					'item'          => @$item
				];

			$inventory["lastid"] = $itemid;
			$this->save( $userid, self::dataInstance( $inventory ) );

			return( $itemid );
		}

		/**
		 * @param $itemid
		 * @param $userid
		 *
		 * @return bool
		 */

		public function exists( $itemid, $userid ): bool
		{

			$inventory = $this->get( $userid );
			return( isset( $inventory->items[ $itemid ] ) );
		}

		/**
		 * @param $itemid
		 * @param $userid
		 *
		 * @return InventoryData
		 */

		public function delete( $itemid, $userid ): InventoryData
		{

			if( $this->exists( $itemid, $userid) == false )
				throw new \Error("Item does not exist: " . $itemid );

			$inventory = $this->get( $userid )->contents();
			unset( $inventory["items"][ $itemid ] );

			return( self::dataInstance( $inventory ) );
		}

		/**
		 * @param $userid
		 */

		public function wipe( $userid )
		{

			FileSystem::delete( $this->path( $userid ) );
		}

		/**
		 * @return InventoryData
		 */

		public function last():InventoryData
		{

			return( self::dataInstance( $this->last_inventory ) );
		}

		/**
		 * @param $userid
		 *
		 * @return string
		 */

		private function path( $userid ):string
		{

			return( Settings::setting("inventory_filepath") . $userid  . ".json");
		}

		/**
		 * @param $path
		 *
		 * @return array
		 */

		private function read( $path ):array
		{

			if( FileSystem::exists( $path ) == false )
				return [];

			return( FileSystem::readJson( $path ) );
		}

		/**
		 * @param $path
		 * @param InventoryData $inventory
		 */

		private function write( $path, InventoryData $inventory ): void
		{

			FileSystem::writeJson( $path, $inventory->contents() );
		}

		/**
		 * @param array|null $data
		 *
		 * @return InventoryData
		 */

		public static function dataInstance( array $data=null )
		{

			return new InventoryData( $data );
		}

		public function has() { }
	}
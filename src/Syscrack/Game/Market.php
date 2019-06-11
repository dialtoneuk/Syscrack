<?php
	declare(strict_types=1);

	namespace Framework\Syscrack\Game;

	use Framework\Application\Settings;
	use Framework\Application\Utilities\FileSystem;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class Market
	 *
	 * @package Framework\Syscrack\Game
	 */
	class Market
	{

		/**
		 * @var Computer
		 */

		protected static $computer;

		/**
		 * Market constructor.
		 */

		public function __construct()
		{

			if (isset(self::$computer) == false)
				self::$computer = new Computer();

		}

		/**
		 * Returns true if this computer is a market
		 *
		 * @param $computerid
		 *
		 * @return bool
		 */

		public function isMarket($computerid)
		{

			if (self::$computer->getComputerType($computerid) != Settings::setting('syscrack_computers_market_type'))
				return false;

			return true;
		}

		/**
		 * Returns true if this itemid is a software
		 *
		 * @param $computerid
		 *
		 * @param $itemid
		 *
		 * @return bool
		 */

		public function isSoftware($computerid, $itemid)
		{

			$stock = $this->getStock($computerid);

			if (isset($stock[$itemid]) == false)
				throw new \Error();


			if ($stock[$itemid]['type'] == 'software')
				return true;


			return false;
		}

		/**
		 * Returns true if the software data is present
		 *
		 * @param $computerid
		 *
		 * @param $itemid
		 *
		 * @return bool
		 */

		public function hasCorrectSoftwareData($computerid, $itemid)
		{

			$stock = $this->getStock($computerid);

			if (isset($stock[$itemid]) == false)
				throw new \Error();


			if (isset($stock[$itemid]['softwareid']) == false)
				return false;


			return true;
		}

		/**
		 * Returns true if this itemid is a hardware
		 *
		 * @param $computerid
		 *
		 * @param $itemid
		 *
		 * @return bool
		 */

		public function isHardware($computerid, $itemid)
		{

			$stock = $this->getStock($computerid);

			if (isset($stock[$itemid]) == false)
				throw new \Error();


			if ($stock[$itemid]['type'] == 'hardware')
				return true;


			return false;
		}

		/**
		 * Gets this markets purchases
		 *
		 * @param $computerid
		 *
		 * @return mixed
		 */

		public function getPurchases($computerid)
		{

			return FileSystem::readJson($this->getFilePath($computerid) . 'purchases.json');
		}

		/**
		 * Gets all the purchases by a computer
		 *
		 * @param $computerid
		 *
		 * @param $targetid
		 *
		 * @return array|null
		 */

		public function getPurchasesByComputer($computerid, $targetid)
		{

			$purchases = $this->getPurchases($computerid);

			if (empty($purchases))
				return null;


			$result = [];

			foreach ($purchases as $purchase)
				if($purchase['computerid'] == $targetid)
					$result[] = $purchase;

			return $result;
		}

		/**
		 * Gets this markets stock
		 *
		 * @param $computerid
		 *
		 * @return mixed
		 */

		public function getStock($computerid)
		{

			return FileSystem::readJson($this->getFilePath($computerid) . 'stock.json');
		}

		/**
		 * Gets the stocks item
		 *
		 * @param $computerid
		 *
		 * @param $itemid
		 *
		 * @return mixed
		 */

		public function getStockItem($computerid, $itemid)
		{

			$stock = $this->getStock($computerid);

			if (isset($stock[$itemid]) == false)
				throw new \Error();

			return $stock[$itemid];
		}

		/**
		 * Checks the market to see if it is currently valid and all files exist
		 *
		 * @param $computerid
		 *
		 * @return bool
		 */

		public function check($computerid)
		{

			if (FileSystem::directoryExists($this->getFilePath($computerid)) == false)
				return false;


			if (FileSystem::exists($this->getFilePath($computerid) . 'purchases.json') == false)
				return false;


			if (FileSystem::exists($this->getFilePath($computerid) . 'stock.json') == false)
				return false;


			return true;
		}

		/**
		 * Returns true if this computer has stock
		 *
		 * @param $computerid
		 *
		 * @return bool
		 */

		public function hasStock($computerid)
		{

			if (FileSystem::exists($this->getFilePath($computerid) . 'stock.json') == false)
				return false;


			if (empty($this->getStock($computerid)))
				return false;

			return true;
		}

		/**
		 * Checks the purchsaes to see if the target computer has made a purchase, if
		 * item is set it will also check if the specific itemid has been purchased
		 *
		 * @param $computerid
		 *
		 * @param $targetid
		 *
		 * @param null $itemid
		 *
		 * @return bool
		 */

		public function hasPurchase($computerid, $targetid, $itemid = null)
		{

			$purchases = $this->getPurchases($computerid);

			foreach ($purchases as $purchase)
				if ($purchase['computerid'] == $targetid)
					if ($itemid !== null)
						if ($purchase['itemid'] == $itemid)
							return true;
						else
							continue;

			return false;
		}

		/**
		 * Adds a purchase to the market
		 *
		 * @param $computerid
		 *
		 * @param $targetid
		 *
		 * @param $itemid
		 */

		public function addPurchase($computerid, $targetid, $itemid)
		{

			$purchases = $this->getPurchases($computerid);

			if ($this->hasPurchase($computerid, $targetid, $itemid))
			{

				throw new \Error();
			}

			$purchases[] = [
				'computerid' => $targetid,
				'itemid' => $itemid,
				'timepurchased' => time()
			];

			$this->save($computerid, $purchases);
		}

		/**
		 * Adds a stock item to the market
		 *
		 * @param $computerid
		 *
		 * @param $itemid
		 *
		 * @param array $data
		 */

		public function addStockItem($computerid, $itemid, array $data = ['name' => 'Default CPU', 'type' => 'hardware', 'hardware' => 'cpu', 'value' => '1000', 'price' => 100, 'quantity' => 10])
		{

			$stock = $this->getStock($computerid);

			if ($this->hasStockItem($computerid, $itemid) == true)
			{

				throw new \Error();
			}

			$stock[$itemid] = array_merge($data, [
				'timeadded' => time()
			]);

			$this->save($computerid, $stock, 'stock.json');
		}

		/**
		 * Removes an item from the stock listing
		 *
		 * @param $computerid
		 *
		 * @param $itemid
		 */

		public function removeStockItem($computerid, $itemid)
		{

			$stock = $this->getStock($computerid);

			if (isset($stock[$itemid]) == false)
			{

				throw new \Error();
			}

			unset($stock[$itemid]);

			$this->save($computerid, $stock . 'stock.json');
		}

		/**
		 * Updates a stock item
		 *
		 * @param $computerid
		 *
		 * @param $itemid
		 *
		 * @param array $data
		 */

		public function updateStockItem($computerid, $itemid, array $data = [])
		{

			$item = $this->getStockItem($computerid, $itemid);

			foreach ($item as $key => $value)
			{

				foreach ($data as $index => $replace)
				{

					if (isset($item[$item]))
					{

						$item[$item] = $replace;
					}
				}
			}

			$stock = $this->getStock($computerid);

			if (isset($stock[$itemid]) == false)
			{

				throw new \Error();
			}

			$stock[$itemid] = $item;

			$this->save($computerid, $stock, 'stock.json');
		}

		/**
		 * Will return true if this stock item exists, if soldout then will also
		 * check if this item is sold out.
		 *
		 * @param $computerid
		 *
		 * @param $itemid
		 *
		 * @param bool $soldout
		 *
		 * @return bool
		 */

		public function hasStockItem($computerid, $itemid, $soldout = true)
		{

			$stock = $this->getStock($computerid);

			if (isset($stock[$itemid]) == false)
			{

				return false;
			}

			if ($soldout)
			{

				if ($stock[$itemid]['quantity'] <= 0)
				{

					return false;
				}
			}

			return true;
		}

		/**
		 * Saves the corresponding file
		 *
		 * @param $computerid
		 *
		 * @param array $data
		 *
		 * @param string $file
		 */

		public function save($computerid, array $data = [], $file = 'purchases.json')
		{

			FileSystem::writeJson($this->getFilePath($computerid) . $file, $data);
		}

		/**
		 * Gets the filepath of the markets files
		 *
		 * @param $computerid
		 *
		 * @return string
		 */

		public function getFilePath($computerid)
		{

			return Settings::setting('syscrack_market_location') . $computerid . '/';
		}
	}
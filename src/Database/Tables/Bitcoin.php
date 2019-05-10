<?php

	namespace Framework\Database\Tables;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class Bitcoin
	 *
	 * @package Framework\Database\Tables
	 */

	use Framework\Database\Table;

	class Bitcoin extends Table
	{

		/**
		 * Gets a bitcoin wallet
		 *
		 * @param $bitcoinid
		 *
		 * @return mixed|null
		 */

		public function getBitcoinWallet($bitcoinid)
		{

			$array = array(
				'bitcoinid' => $bitcoinid
			);

			$result = $this->getTable()->where($array)->get();

			return ($result->isEmpty()) ? null : $result[0];
		}

		/**
		 * Returns the users bitcoins wallets
		 *
		 * @param $userid
		 *
		 * @return \Illuminate\Support\Collection|null
		 */

		public function getUserBitcoinWallets($userid)
		{

			$array = array(
				'userid' => $userid
			);

			$result = $this->getTable()->where($array)->get();

			return ($result->isEmpty()) ? null : $result;
		}

		/**
		 * Finds a bitcoin wallet through its wallet
		 *
		 * @param $wallet
		 *
		 * @return mixed|null
		 */

		public function findBitcoinWallet($wallet)
		{

			$array = array(
				'wallet' => $wallet
			);

			$result = $this->getTable()->where($array)->get();

			return ($result->isEmpty()) ? null : $result[0];
		}

		/**
		 * Finds the wallets by their server and computerid
		 *
		 * @param $computerid
		 *
		 * @return \Illuminate\Support\Collection|null
		 */

		public function findByServer($wallet, $computerid)
		{

			$array = array(
				'wallet' => $wallet,
				'computerid' => $computerid
			);

			$result = $this->getTable()->where($array)->get();

			return ($result->isEmpty()) ? null : $result;
		}

		/**
		 * Gets all the wallets tied to a specific computerid
		 *
		 * @param $computerid
		 *
		 * @return \Illuminate\Support\Collection|null
		 */

		public function getByServer($computerid)
		{

			$array = array(
				'computerid' => $computerid
			);

			$result = $this->getTable()->where($array)->get();

			return ($result->isEmpty()) ? null : $result;
		}

		public function insertWallet($array)
		{

			return $this->getTable()->insertGetId($array);
		}

		public function updateWallet($wallet, $values)
		{

			$array = array(
				'wallet' => $wallet
			);

			$this->getTable()->where($array)->update($values);
		}
	}
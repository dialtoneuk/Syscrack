<?php
	declare(strict_types=1);

	namespace Framework\Database\Tables;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class Api
	 *
	 * @package Framework\Database\Tables
	 */

	use Framework\Database\Table;

	/**
	 * Class Api
	 * @package Framework\Database\Tables
	 */
	class Api extends Table
	{

		/**
		 * Gets the Api by the API key
		 *
		 * @param $apikey
		 *
		 * @return mixed
		 */

		public function getApiByKey($apikey)
		{

			$array = [
				'apikey' => $apikey
			];

			$result = $this->getTable()->where($array)->get();

			return ($result->isEmpty()) ? null : $result[0];
		}

		/**
		 * Gets all the API by this user
		 *
		 * @param $userid
		 *
		 * @return \Illuminate\Support\Collection|null
		 */

		public function getApiByUser($userid)
		{

			$array = [
				'userid' => $userid
			];

			$result = $this->getTable()->where($array)->get();

			return ($result->isEmpty()) ? null : $result;
		}

		/**
		 * Gets the API through the Api ID
		 *
		 * @param $accessid
		 *
		 * @return mixed
		 */

		public function getApi($accessid)
		{

			$array = [
				'accessid' => $accessid
			];

			$result = $this->getTable()->where($array)->get();

			return ($result->isEmpty()) ? null : $result[0];
		}
	}
<?php
	declare(strict_types=1);

	namespace Framework\Database\Tables;

	/**
	 * Lewis Lancaster 2016
	 *
	 * Class Software
	 *
	 * @package Framework\Database\Tables
	 */

	use Framework\Database\Table;

	/**
	 * Class Software
	 * @package Framework\Database\Tables
	 */
	class Software extends Table
	{

		/**
		 * Gets the software from its ID
		 *
		 * @param $softwareid
		 *
		 * @return mixed|null
		 *
		 */

		public function getSoftware($softwareid)
		{

			$array = [
				'softwareid' => $softwareid
			];

			$result = $this->getTable()->where($array)->get();

			return ($result->isEmpty()) ? null : $result->first();
		}

		/**
		 * Gets all the software related to a user
		 *
		 * @param $userid
		 *
		 * @return mixed|null
		 *
		 */

		public function getUserSoftware($userid)
		{

			$array = [
				'userid' => $userid
			];

			$result = $this->getTable()->where($array)->get();

			return ($result->isEmpty()) ? null : $result[0];
		}

		/**
		 * Gets software by type and computerid
		 *
		 * @param $type
		 *
		 * @param $computerid
		 *
		 * @return \Illuminate\Support\Collection|null
		 *
		 */

		public function getTypeOnComputer($type, $computerid)
		{

			$array = [
				'type' => $type,
				'computerid' => $computerid
			];

			$result = $this->getTable()->where($array)->get();

			return ($result->isEmpty()) ? null : $result;
		}

		/**
		 * Gets the software by a computer
		 *
		 * @param $computerid
		 *
		 * @return \Illuminate\Support\Collection|null
		 *
		 */

		public function getByComputer($computerid)
		{

			$array = [
				'computerid' => $computerid
			];

			$result = $this->getTable()->where($array)->get();

			return ($result->isEmpty()) ? null : $result;
		}

		/**
		 * Gets the installed software
		 *
		 * @param $computerid
		 *
		 * @return \Illuminate\Support\Collection|null
		 *
		 */

		public function getInstalledSoftware($computerid)
		{

			$array = [
				'computerid' => $computerid,
				'installed' => true
			];

			$result = $this->getTable()->where($array)->get();

			return ($result->isEmpty()) ? null : $result;
		}

		/**
		 * Gets the software by their type
		 *
		 * @param $type
		 *
		 * @return mixed|null
		 *
		 */

		public function getSoftwareByType($type)
		{

			$array = [
				'type' => $type
			];

			$result = $this->getTable()->where($array)->get();

			return ($result->isEmpty()) ? null : $result[0];
		}

		/**
		 * Gets the last modified software
		 *
		 * @param $computerid
		 *
		 * @param $time
		 *
		 * @param $type
		 *
		 * @return mixed|null
		 */

		public function getLastModified($computerid, $time, $type)
		{

			$array = [
				'computerid' => $computerid,
				'lastmodified', '>', $time,
				'type' => $type
			];

			$result = $this->getTable()->where($array)->get();

			return ($result->isEmpty()) ? null : $result[0];
		}

		/**
		 * Updates a software
		 *
		 * @param $softwareid
		 *
		 * @param $values
		 *
		 */

		public function updateSoftware($softwareid, $values)
		{

			$array = [
				'softwareid' => $softwareid
			];

			$this->getTable()->where($array)->update($values);
		}

		/**
		 * Deletes the software by the computer
		 *
		 * @param $computerid
		 *
		 */

		public function deleteSoftwareByComputer($computerid)
		{

			$array = [
				'computerid' => $computerid
			];

			$this->getTable()->where($array)->delete();
		}

		/**
		 * Deletes a software
		 *
		 * @param $softwareid
		 *
		 */

		public function deleteSoftware($softwareid)
		{

			$array = [
				'softwareid' => $softwareid
			];

			$this->getTable()->where($array)->delete();
		}

		/**
		 * Inserts a software
		 *
		 * @param $array
		 *
		 * @return int
		 *
		 */

		public function insertSoftware($array)
		{

			return $this->getTable()->insertGetId($array);
		}
	}
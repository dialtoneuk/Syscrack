<?php
	declare(strict_types=1);

	namespace Framework\Database\Tables;

	/**
	 * Lewis Lancater 2017
	 *
	 * Class Processes
	 *
	 * @package Framework\Database\Tables
	 */

	use Framework\Database\Table;

	/**
	 * Class Processes
	 * @package Framework\Database\Tables
	 */
	class Processes extends Table
	{

		/**
		 * @param $processid
		 *
		 * @return mixed|null
		 */
		public function getProcess($processid)
		{

			$array = [
				'processid' => $processid
			];

			$result = $this->getTable()->where($array)->get();

			return ($result->isEmpty()) ? null : $result[0];
		}

		/**
		 * @param $userid
		 *
		 * @return \Illuminate\Support\Collection|null
		 */
		public function getUserProcesses($userid)
		{

			$array = [
				'userid' => $userid
			];

			$result = $this->getTable()->where($array)->get();

			return ($result->isEmpty()) ? null : $result;
		}

		/**
		 * @param $computerid
		 *
		 * @return \Illuminate\Support\Collection|null
		 */
		public function getComputerProcesses($computerid)
		{

			$array = [
				'computerid' => $computerid
			];

			$result = $this->getTable()->where($array)->get();

			return ($result->isEmpty()) ? null : $result;
		}

		/**
		 * @param $array
		 *
		 * @return int
		 */
		public function insertProcess($array)
		{

			return $this->getTable()->insertGetId($array);
		}

		/**
		 * @param $processid
		 * @param $values
		 */
		public function updateProcess($processid, $values)
		{

			$array = [
				'processid' => $processid
			];

			$this->getTable()->where($array)->update($values);
		}

		/**
		 * @param $processid
		 */
		public function trashProcess($processid)
		{
			$array = [
				'processid' => $processid
			];

			$this->getTable()->where($array)->delete();
		}
	}
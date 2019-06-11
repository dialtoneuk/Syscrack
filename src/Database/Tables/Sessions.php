<?php
	declare(strict_types=1);

	namespace Framework\Database\Tables;

	/**
	 * Lewis Lancaster 2016
	 *
	 * Class Sessions
	 *
	 * @package Framework\Database\Tables
	 */

	use Framework\Database\Table;

	/**
	 * Class Sessions
	 * @package Framework\Database\Tables
	 */
	class Sessions extends Table
	{

		/**
		 * Gets the session
		 *
		 * @param $sessionid
		 *
		 * @return mixed
		 */

		public function getSession($sessionid)
		{

			$array = [
				'sessionid' => $sessionid
			];

			$result = $this->getTable()->where($array)->get();

			return ($result->isEmpty()) ? null : $result[0];
		}

		/**
		 * Trashes all the user sessions
		 *
		 * @param $userid
		 */

		public function trashUserSessions($userid)
		{

			$array = [
				'userid' => $userid
			];

			$this->getTable()->where($array)->delete();
		}

		/**
		 * Gets by user agent
		 *
		 * @param $useragent
		 *
		 * @return array|null|\stdclass
		 */

		public function getByUserAgent($useragent)
		{

			$array = [
				'useragent' => $useragent
			];

			$result = $this->getTable()->where($array)->get();

			return ($result->isEmpty()) ? null : $result[0];
		}

		/**
		 * Gets a session by their last action
		 *
		 * @param $time
		 *
		 * @return \Illuminate\Support\Collection|null
		 */

		public function getSessionsByLastAction($time)
		{

			$result = $this->getTable()->where('lastaction', '>', $time)->get();

			return ($result->isEmpty()) ? null : $result;
		}

		/**
		 * Returns all the sessions
		 *
		 * @return \Illuminate\Support\Collection
		 */

		public function getAllSessions()
		{

			return $this->getTable()->get();
		}

		/**
		 * Trashes a session
		 *
		 * @param $sessionid
		 */

		public function trashSession($sessionid)
		{

			$array = [
				'sessionid' => $sessionid
			];

			$this->getTable()->where($array)->delete();
		}

		/**
		 * Updates the session
		 *
		 * @param $sessionid
		 *
		 * @param $values
		 */

		public function updateSession($sessionid, $values)
		{

			$array = [
				'sessionid' => $sessionid
			];

			$this->getTable()->where($array)->update($values);
		}

		/**
		 * Inserts the session
		 *
		 * @param $array
		 */

		public function insertSession($array)
		{

			$this->getTable()->insert($array);
		}
	}
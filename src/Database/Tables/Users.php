<?php

	namespace Framework\Database\Tables;

	/**
	 * Lewis Lancaster 2016
	 *
	 * Class Users
	 *
	 * @package Framework\Database\Tables
	 */

	use Framework\Database\Table;

	class Users extends Table
	{

		/**
		 * @param bool $safe
		 *
		 * @return \Illuminate\Support\Collection
		 */

		public function getUsers( $safe = true )
		{

			if( $safe )
				$result = $this->getTable()->get(["username","userid","email","group"]);
			else
				$result = $this->getTable()->get();

			return( $result );
		}

		/**
		 * @param $userid
		 * @param bool $safe
		 *
		 * @return mixed|null
		 */

		public function getUser($userid, $safe=true )
		{

			$array = array(
				'userid' => $userid
			);

			if( $safe )
				$result = $this->getTable()->where($array)->get(["username","userid","email","group"]);
			else
				$result = $this->getTable()->where($array)->get();

			return ($result->isEmpty()) ? null : $result[0];
		}


		/**
		 * Deletes a software
		 *
		 * @param $softwareid
		 *
		 */

		public function deleteUser($userid)
		{

			$array = array(
				'userid' => $userid
			);

			$this->getTable()->where($array)->delete();
		}

		/**
		 * Gets a user by their username
		 *
		 * @param $username
		 *
		 * @return mixed|null
		 */

		public function getByUsername($username)
		{

			$array = array(
				'username' => $username
			);

			$result = $this->getTable()->where($array)->get();

			return ($result->isEmpty()) ? null : $result[0];
		}

		/**
		 * Gets a user by their email
		 *
		 * @param $email
		 *
		 * @return mixed
		 */

		public function getByEmail($email)
		{

			$array = array(
				'email' => $email
			);

			$result = $this->getTable()->where($array)->get();

			return ($result->isEmpty()) ? null : $result[0];
		}

		/**
		 * Updates a user
		 *
		 * @param $userid
		 *
		 * @param $values
		 */

		public function updateUser($userid, $values)
		{

			$array = array(
				'userid' => $userid
			);

			$this->getTable()->where($array)->update($values);
		}

		/**
		 * Inserts a new user
		 *
		 * @param $array
		 *
		 * @return int
		 */

		public function insertUser($array)
		{

			return $this->getTable()->insertGetId($array);
		}
	}
<?php
	declare(strict_types=1);

	namespace Framework\Database\Tables;

	/**
	 * Lewis Lancaster 2016
	 *
	 * Class Users
	 *
	 * @package Framework\Database\Tables
	 */

	use Framework\Database\Table;
	use Framework\Syscrack\Game\ModLoader;

	/**
	 * Class Users
	 * @package Framework\Database\Tables
	 */
	class Users extends Table
	{

		/**
		 * @param bool $safe
		 *
		 * @return \Illuminate\Support\Collection
		 */

		public function getUsers( bool $safe=true )
		{

			if( ModLoader::modded() && $safe !== true  )
				throw new \Error("Mod " . ModLoader::$last["mod"] . " in class " . ModLoader::$last["class"] . " tried to do unsafe database query");

			if( $safe !== false )
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

		public function getUser($userid, bool $safe=true )
		{

			if( ModLoader::modded() && $safe !== true  )
				throw new \Error("Mod " . @ModLoader::$last["mod"] . " in class " . @ModLoader::$last["class"] . " tried to do unsafe database query");

			$array = [
				'userid' => $userid
			];

			if( $safe !== false )
				$result = $this->getTable()->where($array)->get(["username","userid","email","group"]);
			else
				$result = $this->getTable()->where($array)->get();

			return ($result->isEmpty()) ? null : $result[0];
		}


		/**
		 * Deletes a software
		 *
		 * @param $userid
		 */

		public function deleteUser($userid)
		{

			$array = [
				'userid' => $userid
			];

			$this->getTable()->where($array)->delete();
		}

		/**
		 * @param $username
		 * @param bool $safe
		 *
		 * @return mixed|null
		 */

		public function getByUsername($username, bool $safe=true )
		{

			if( ModLoader::modded() && $safe !== true  )
				throw new \Error("Mod " . ModLoader::$last["mod"] . " in class " . ModLoader::$last["class"] . " tried to do unsafe database query");

			$array = [
				'username' => $username
			];

			if( $safe !== false )
				$result = $this->getTable()->where($array)->get(["username","userid","email","group"]);
			else
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

			$array = [
				'email' => $email
			];

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

			if( ModLoader::modded() )
				throw new \Error("Mod " . ModLoader::$last["mod"] . " in class " . ModLoader::$last["class"] . " tried to update user settings the wrong way");

			$array = [
				'userid' => $userid
			];

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
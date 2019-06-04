<?php

	namespace Framework\Syscrack;

	/**
	 * Lewis Lancaster 2016
	 *
	 * Class User
	 *
	 * @package Framework\Syscrack
	 */

	use Framework\Database\Tables\Users as Database;


	class User
	{

		/**
		 * @var Database
		 */

		protected static $database;

		/**
		 * User constructor.
		 */

		public function __construct()
		{
			if (isset(self::$database) == false)
				self::$database = new Database();
		}

		/**
		 * Checks if the user exists
		 *
		 * @param $userid
		 *
		 * @return bool
		 */

		public function userExists($userid)
		{

			if (self::$database->getUser($userid) == null)
			{

				return false;
			}

			return true;
		}

		/**
		 * Returns true if the username exists
		 *
		 * @param $username
		 *
		 * @return bool
		 */

		public function usernameExists($username)
		{

			if (self::$database->getByUsername($username) == null)
			{

				return false;
			}

			return true;
		}

		/**
		 * Should only be used in tests
		 *
		 * @param $userid
		 */

		public function delete($userid)
		{

			self::$database->deleteUser($userid);
		}

		/**
		 * Finds a user by their username
		 *
		 * @param $username
		 *
		 * @return mixed
		 */

		public function findByUsername($username)
		{

			$result = self::$database->getByUsername($username);

			if ($result == null)
			{

				throw new \Error();
			}

			return $result->userid;
		}

		/**
		 * @param $userid
		 * @param bool $safe
		 *
		 * @return mixed|null
		 */

		public function getUser($userid, $safe = true)
		{

			if ($this->userExists($userid) == false)
			{

				throw new \Error();
			}

			return self::$database->getUser($userid, $safe );
		}

		/**
		 * Gets the users username
		 *
		 * @param $userid
		 *
		 * @return string
		 */

		public function getUsername($userid)
		{

			if ($this->userExists($userid) == false)
			{

				throw new \Error();
			}

			return $this->getUser($userid)->username;
		}

		/**
		 * Gets the users password
		 *
		 * @param $userid
		 *
		 * @return \___PHPSTORM_HELPERS\static
		 */

		public function getPassword($userid)
		{

			if ($this->userExists($userid) == false)
			{

				throw new \Error();
			}

			return $this->getUser($userid, false)->password;
		}

		/**
		 * Gets the users email
		 *
		 * @param $userid
		 *
		 * @return \___PHPSTORM_HELPERS\static
		 */

		public function getEmail($userid)
		{

			if ($this->userExists($userid) == false)
			{

				throw new \Error();
			}

			return $this->getUser($userid)->email;
		}

		/**
		 * Gets the users salt
		 *
		 * @param $userid
		 *
		 * @return \___PHPSTORM_HELPERS\static
		 */

		public function getSalt($userid)
		{

			if ($this->userExists($userid) == false)
			{

				throw new \Error();
			}

			return $this->getUser($userid, false )->salt;
		}

		/**
		 * Updates the users email
		 *
		 * @param $userid
		 *
		 * @param $email
		 */

		public function updateEmail($userid, $email)
		{

			if ($this->userExists($userid) == false)
			{

				throw new \Error();
			}

			if ($this->isEmail($email) == false)
			{

				throw new \Error();
			}

			$array = array(
				'email' => $email
			);

			self::$database->updateUser($userid, $array);
		}

		/**
		 * Updates the users password
		 *
		 * @param $userid
		 *
		 * @param $password
		 */

		public function updatePassword($userid, $password)
		{

			if ($this->userExists($userid) == false)
			{

				throw new \Error();
			}

			$array = array(
				'password' => $password
			);

			self::$database->updateUser($userid, $array);
		}

		/**
		 * Updates the users group
		 *
		 * @param $userid
		 *
		 * @param $group
		 */

		public function updateGroup($userid, $group)
		{

			if ($this->userExists($userid) == false)
			{

				throw new \Error();
			}

			$array = array(
				'group' => $group
			);

			self::$database->updateUser($userid, $array);
		}

		/**
		 * Updates the users salt
		 *
		 * @param $userid
		 *
		 * @param $salt
		 */

		public function updateSalt($userid, $salt)
		{

			if ($this->userExists($userid) == false)
			{

				throw new \Error();
			}

			$array = array(
				'salt' => $salt
			);

			self::$database->updateUser($userid, $array);
		}

		/**
		 * Returns true if the user is an admin
		 *
		 * @param $userid
		 *
		 * @return bool
		 */

		public function isAdmin($userid)
		{

			if ($this->userExists($userid) == false)
			{

				throw new \Error();
			}

			if ($this->getUser($userid)->group !== 'admin')
			{

				return false;
			}

			return true;
		}

		/**
		 * Returns all the users currently in the database
		 *
		 * @return \Illuminate\Support\Collection
		 */

		public function getAllUsers()
		{

			return self::$database->getUsers();
		}

		/**
		 * Returns the number of users
		 *
		 * @return int
		 */

		public function getUsersCount()
		{

			return self::$database->getUsers()->count();
		}

		/**
		 * Returns true if it is an email.
		 *
		 * @param $email
		 *
		 * @return bool
		 */

		private function isEmail($email)
		{

			if (filter_var($email, FILTER_VALIDATE_EMAIL))
			{

				return true;
			}

			return false;
		}
	}
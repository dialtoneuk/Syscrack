<?php

	namespace Framework\Syscrack;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class Register
	 *
	 * @package Framework\Syscrack
	 */

	use Framework\Application\Settings;
	use Framework\Application\Utilities\Hashes;
	use Framework\Database\Tables\Users as Database;
	use Framework\Exceptions\SyscrackException;

	class Register
	{

		/**
		 * @var Database
		 */

		protected $database;

		/**
		 * Register constructor.
		 */

		public function __construct()
		{

			$this->database = new Database();
		}

		/**
		 * Registers a new user and returns a verification token
		 *
		 * @param $username
		 *
		 * @param $password
		 *
		 * @param $email
		 *
		 * @return mixed
		 */

		public function register($username, $password, $email)
		{

			if (filter_var($email, FILTER_VALIDATE_EMAIL) == false)
			{

				throw new SyscrackException('Your email is not a valid email');
			}

			if ($this->isUsernameTaken($username))
			{

				throw new SyscrackException('Username is already taken');
			}

			if ($this->isEmailUnused($email))
			{

				throw new SyscrackException('Email is already taken');
			}

			$data = $this->prepareArray($username, $password, $email);

			if (empty($data))
			{

				throw new SyscrackException('Failed to prepare array');
			}

			$userid = $this->database->insertUser($data);

			if (empty($userid))
			{

				throw new SyscrackException('Internal error');
			}

			$verification = new Verification();

			if ($verification->hasVerificationRequest($userid))
			{

				return $verification->getToken($userid);
			}

			return $verification->addRequest($userid, $email);
		}

		/**
		 * Returns true if an email is unused
		 *
		 * @param $email
		 *
		 * @return bool
		 */

		public function isEmailUnused($email)
		{

			if ($this->database->getByEmail($email) == null)
			{

				return false;
			}

			return true;
		}

		/**
		 * Checks if the username is taken
		 *
		 * @param $username
		 *
		 * @return bool
		 */

		public function isUsernameTaken($username)
		{

			if ($this->database->getByUsername($username) == null)
			{

				return false;
			}

			return true;
		}

		/**
		 * Prepares the array of data to be passed to the database ( query builder )
		 *
		 * @param $username
		 *
		 * @param $password
		 *
		 * @param $email
		 *
		 * @return array
		 */

		private function prepareArray($username, $password, $email)
		{

			$salt = $this->generateSalt();

			if ($salt == null)
			{

				throw new SyscrackException();
			}

			$password = $this->saltPassword($password, $salt);

			if ($this->database->getUsers()->isEmpty() && Settings::setting('user_first_signup_admin'))
			{

				$group = Settings::setting('user_group_admin');
			}
			else
			{

				$group = Settings::setting('user_default_group');
			}

			$array = array(
				'username' => $username,
				'email' => $email,
				'password' => $password,
				'salt' => $salt,
				'group' => $group
			);

			return $array;
		}

		/**
		 * Generates a new salt
		 *
		 * @return array|null|string
		 */

		private function generateSalt()
		{

			return Hashes::sha1(Hashes::randomBytes());
		}

		/**
		 * Gives that password some nice salt
		 *
		 * @param $password
		 *
		 * @param $salt
		 *
		 * @return array|null|string
		 */

		private function saltPassword($password, $salt)
		{

			return Hashes::sha1($salt, $password);
		}
	}
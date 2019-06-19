<?php
	declare(strict_types=1);

	namespace Framework\Syscrack\Login;

	/**
	 * Lewis Lancaster 2016
	 *
	 * Class Account
	 *
	 * @package Framework\Syscrack\Login
	 */

	use Framework\Application\Settings;
	use Framework\Application\Utilities\Hashes;
	use Framework\Syscrack\User;
	use Framework\Syscrack\Verification;

	/**
	 * Class Account
	 * @package Framework\Syscrack\Login
	 */
	class Account
	{

		/**
		 * @var User
		 */

		protected $user;

		/**
		 * @var Verification
		 */

		protected $verification;

		/**
		 * @var \Error
		 */

		public static $error;

		/**
		 * Account constructor.
		 */

		public function __construct()
		{

			$this->user = new User();

			$this->verification = new Verification();
		}

		/**
		 * @param $username
		 * @param $password
		 *
		 * @return bool
		 */

		public function loginAccount($username, $password)
		{

			try
			{

				if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $username))
					throw new \Error('Username is invalid and must not contain any special characters');

				if ($this->user->usernameExists($username) == false)
					throw new \Error('Invalid Credentials');

				$userid = $this->user->findByUsername($username);

				if (Settings::setting('login_admins_only') == true)
					if ($this->user->isAdmin($userid) == false)
						throw new \Error('Sorry, the game is currently in admin mode, please try again later');

				if ($this->checkPassword($userid, $password, $this->user->getSalt($userid)) == false)
					throw new \Error('Invalid Credentials');


				if ($this->verification->isVerified($userid) == false)
					throw new \Error('Please verify your email');

				return true;

			}
			catch ( \Error $error)
			{

				self::$error = $error;
			}

			return false;
		}

		/**
		 * Gets the users ID from their username
		 *
		 * @param $username
		 *
		 * @return mixed
		 */

		public function getUserID($username)
		{

			return $this->user->findByUsername($username);
		}

		/**
		 * Checks to see if a password is valid
		 *
		 * @param $userid
		 *
		 * @param $password
		 *
		 * @param $salt
		 *
		 * @return bool
		 */

		private function checkPassword($userid, $password, $salt)
		{

			if ($this->user->userExists($userid) == false)
			{

				throw new \Error();
			}

			$accountpassword = $this->user->getPassword($userid);

			if (Hashes::sha1($salt, $password) !== $accountpassword)
			{

				return false;
			}

			return true;
		}
	}
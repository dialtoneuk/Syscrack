<?php

	namespace Framework\Syscrack;

	/**
	 * Lewis Lancaster 2016
	 *
	 * Class Verification
	 *
	 * @package Framework\Syscrack
	 */

	use Framework\Application\Utilities\Hashes;
	use Framework\Database\Tables\Verifications as Database;


	class Verification
	{

		/**
		 * @var Database
		 */

		protected $database;

		/**
		 * Verification constructor.
		 */

		public function __construct()
		{

			$this->database = new Database();
		}

		/**
		 * Gets the request email of a user
		 *
		 * @param $userid
		 *
		 * @param bool $single
		 *
		 * @return array
		 */

		public function getRequestEmail($userid, $single = false)
		{

			if ($this->isVerified($userid) == true)
			{

				throw new \Error();
			}

			$emails = $this->database->getUserRequests($userid);

			if ($single == true)
			{

				return $emails[0]->email;
			}

			$array = array();

			foreach ($emails as $email)
			{

				$array[] = $email->email;
			}

			if (empty($array))
			{

				throw new \Error();
			}

			return $array;
		}

		/**
		 * Gets the users first token
		 *
		 * @param $userid
		 *
		 * @return mixed
		 */

		public function getToken($userid)
		{

			return $this->database->getUserRequests($userid)[0]->token;
		}

		/**
		 * Gets the user behind a token
		 *
		 * @param $token
		 *
		 * @return mixed
		 */

		public function getTokenUser($token)
		{

			$result = $this->database->getToken($token);

			if ($result == null)
			{

				return null;
			}

			return $result->userid;
		}

		/**
		 * Resets the request
		 *
		 * @param $userid
		 *
		 * @param $email
		 *
		 * @return string
		 */

		public function resetRequest($userid, $email)
		{

			if ($this->isVerified($userid) == true)
			{

				throw new \Error();
			}

			if ($this->isEmail($email) == false)
			{

				throw new \Error();
			}

			$this->database->deleteUserRequests($userid);

			if ($this->isVerified($userid) == true)
			{

				throw new \Error();
			}

			$token = $this->addRequest($userid, $email);

			if (empty($token))
			{

				throw new \Error();
			}

			return $token;
		}

		/**
		 * Adds a verification request
		 *
		 * @param $userid
		 *
		 * @param $email
		 *
		 * @return string
		 */

		public function addRequest($userid, $email)
		{

			if ($this->isVerified($userid) == false)
			{

				throw new \Error('User is already has verification request');
			}

			$token = $this->generateToken();

			if (empty($token))
			{

				throw new \Error('Verification token is empty');
			}

			if ($this->isEmail($email) == false)
			{

				throw new \Error('Email does not exist');
			}

			$array = array(
				'userid' => $userid,
				'token' => $token,
				'email' => $email
			);

			$this->database->insertRequest($array);

			return $token;
		}

		/**
		 * Verifies a user
		 *
		 * @param $token
		 *
		 * @return bool
		 */

		public function verifyUser($token)
		{

			if ($this->database->getToken($token) == null)
			{

				throw new \Error('token is invalid');
			}

			$userid = $this->database->getToken($token)->userid;

			if ($this->isVerified($userid) == true)
			{

				throw new \Error();
			}

			$this->database->deleteUserRequests($userid);

			if ($this->hasVerificationRequest($userid) == true)
			{

				throw new \Error();
			}

			return true;
		}

		/**
		 * Returns true if the user is verified
		 *
		 * @param $userid
		 *
		 * @return bool
		 */

		public function isVerified($userid)
		{

			if ($this->hasVerificationRequest($userid) == true)
			{

				return false;
			}

			return true;
		}

		/**
		 * Checks if the user has a verification request
		 *
		 * @param $userid
		 *
		 * @return bool
		 */

		public function hasVerificationRequest($userid)
		{

			if ($this->database->getUserRequests($userid) == null)
			{

				return false;
			}

			return true;
		}

		/**
		 * Returns true if the item is an email
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

		/**
		 * Generates a token
		 *
		 * @return string
		 */

		private function generateToken()
		{

			return md5(Hashes::randomBytes());
		}
	}
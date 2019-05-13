<?php
	/**
	 * Created by PhpStorm.
	 * User: newsy
	 * Date: 05/05/2019
	 * Time: 21:10
	 */

	namespace Framework\Tests;

	use Framework\Application\Settings;
	use Framework\Syscrack\Game\Computer;
	use Framework\Syscrack\Game\Internet;
	use Framework\Syscrack\Register;
	use Framework\Syscrack\User;
	use Framework\Syscrack\Verification;

	class RegisterTest extends BaseTestCase
	{

		/**
		 * @var Register
		 */

		protected static $register;

		/**
		 * @var User
		 */

		protected static $user;

		/**
		 * @var Verification
		 */

		protected static $verification;

		/**
		 * @var Internet
		 */

		protected static $internet;

		/**
		 * @var Computer
		 */

		protected static $computer;
		/**
		 * @var string
		 */

		protected static $username = "testaccount";

		/**
		 * @var string
		 */

		protected static $password = "test12345";

		/**
		 * @var string
		 */

		protected static $email = "test@syscrack.co.uk";

		/**
		 * @var string
		 */
		protected static $token;

		/**
		 *
		 */

		public static function setUpBeforeClass(): void
		{

			self::$register = new Register();
			self::$user = new User();
			self::$verification = new Verification();
			self::$computer = new Computer();
			self::$internet = new Internet();
			parent::setUpBeforeClass(); // TODO: Change the autogenerated stub
		}

		public static function tearDownAfterClass(): void
		{

			self::$user->delete(self::$user->findByUsername(self::$username));

			parent::tearDownAfterClass(); // TODO: Change the autogenerated stub
		}

		public function testRegister()
		{

			$result = self::$register->register(self::$username, self::$password, self::$email);

			$this->assertNotEmpty($result);
			$this->assertIsString($result);

			self::$token = $result;
		}

		public function testVerification()
		{

			$this->assertNotEmpty(self::$verification);

			if (empty(self::$verification))
				return;

			$userid = self::$verification->getTokenUser(self::$token);
			$this->assertTrue(self::$verification->verifyUser(self::$token));

			$computerid = self::$computer->createComputer($userid, Settings::setting('syscrack_startup_default_computer'), self::$internet->getIP());

			if (empty($computerid))
				throw new \Error();

			$class = self::$computer->getComputerClass(Settings::setting('syscrack_startup_default_computer'));

			if ($class instanceof \Framework\Syscrack\Game\Interfaces\Computer == false)
				throw new \Error();

			$class->onStartup($computerid, $userid, [], Settings::setting('syscrack_default_hardware'));
		}
	}

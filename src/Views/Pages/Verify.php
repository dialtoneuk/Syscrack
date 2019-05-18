<?php

	namespace Framework\Views\Pages;

	/**
	 * Lewis Lancaster 2016
	 *
	 * Class Verify
	 *
	 * @package Framework\Views\Pages
	 */

	use Framework\Application\Settings;
	use Framework\Application\Utilities\PostHelper;
	use Framework\Exceptions\SyscrackException;
	use Framework\Syscrack\Game\Interfaces\Computer;
	use Framework\Syscrack\Verification;
	use Framework\Views\BaseClasses\Page as BaseClass;
	use Framework\Views\Structures\Page as Structure;

	class Verify extends BaseClass implements Structure
	{

		/**
		 * @var Verification
		 */

		protected static $verification;

		/**
		 * Verify constructor.
		 */

		public function __construct()
		{

			if (isset(self::$verification) == false)
				self::$verification = new Verification();

			parent::__construct(true, true, false, true);

		}

		/**
		 * Returns the pages mapping
		 *
		 * @return array
		 */

		public function mapping()
		{

			return array(
				[
					'GET /verify/', 'page'
				],
				[
					'POST /verify/', 'process'
				]
			);
		}

		/**
		 * Default page
		 */

		public function page()
		{

			if (isset($_GET['token']))
			{

				$_GET['token'] = htmlspecialchars($_GET['token'], ENT_QUOTES, 'UTF-8');

				if (self::$verification->getTokenUser($_GET['token']) == null)
				{

					$this->formError('Sorry, this token is invalid...');
				}

				$userid = self::$verification->getTokenUser($_GET['token']);

				if ($userid == null)
				{

					$this->formError('Sorry, this token isnt tied to a user, try again?');
				}

				if (self::$verification->verifyUser($_GET['token']) == false)
				{

					$this->formError('Sorry, failed to verify, try again?');
				}


				if (Settings::setting('syscrack_startup_on_verification') == true)
				{

					$computerid = self::$computer->createComputer($userid, Settings::setting('syscrack_startup_default_computer'), self::$internet->getIP());

					if (empty($computerid))
						throw new SyscrackException();

					$class = self::$computer->getComputerClass(Settings::setting('syscrack_startup_default_computer'));

					if ($class instanceof Computer == false)
						throw new SyscrackException();

					$class->onStartup($computerid, $userid, [], Settings::setting('syscrack_default_hardware'));
				}


				$this->formSuccess('login');
			}
			else
				$this->getRender('syscrack/page.verify', []);
		}

		/**
		 * Processes the verification request
		 */

		public function process()
		{

			if (PostHelper::hasPostData() == false)
			{

				$this->formError('Please enter a token');
			}

			if (PostHelper::checkForRequirements(['token']) == false)
			{

				$this->formError('Please enter a token');
			}

			$token = PostHelper::getPostData('token', true);

			$userid = self::$verification->getTokenUser($token);

			if ($userid == null)
			{

				$this->formError('Sorry, this token is not tied to a user, try again?');
			}

			if (self::$verification->verifyUser($token) == false)
			{

				$this->formError('Sorry, failed to verify, try again?');
			}

			try
			{

				if (Settings::setting('syscrack_startup_on_verification') == true)
				{

					$computerid = self::$computer->createComputer($userid, Settings::setting('syscrack_startup_default_computer'), self::$internet->getIP());

					if (empty($computerid))
					{

						throw new SyscrackException();
					}

					$class = self::$computer->getComputerClass(Settings::setting('syscrack_startup_default_computer'));

					if ($class instanceof Computer == false)
					{

						throw new SyscrackException();
					}

					$class->onStartup($computerid, $userid, [], Settings::setting('syscrack_default_hardware'));
				}
			} catch (\Exception $error)
			{

				$this->formError('Sorry, your account has been verified but we encountered an error: ' . $error->getMessage());
			}

			$this->formSuccess('login');
		}
	}
<?php
	declare(strict_types=1);

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

	use Framework\Syscrack\Game\Interfaces\Computer;
	use Framework\Syscrack\Verification;
	use Framework\Views\BaseClasses\Page as BaseClass;

	/**
	 * Class Verify
	 * @package Framework\Views\Pages
	 */
	class Verify extends BaseClass
	{

		/**
		 * @var Verification
		 */

		protected static $verification;

		/**
		 * Verify constructor.
		 *
		 * @param bool $requirelogin
		 * @param bool $update
		 * @param bool $admin_only
		 */

		public function __construct(bool $requirelogin = false, bool $update = true, bool $admin_only = false) { parent::__construct($requirelogin, $update, $admin_only); }

		/**
		 * Verify setup
		 */

		public static function setup( $autoload = true, $session = true )
		{

			if (isset(self::$verification) == false)
				self::$verification = new Verification();

			parent::setup( $autoload, $session );

		}

		/**
		 * Returns the pages mapping
		 *
		 * @return array
		 */

		public function mapping()
		{

			return [
				[
					'GET /verify/', 'page'
				],
				[
					'POST /verify/', 'process'
				]
			];
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
					$this->formError('Sorry, this token is invalid...');
				else
				{

					$userid = self::$verification->getTokenUser($_GET['token']);

					if ($userid == null)
						$this->formError('Sorry, this token isnt tied to a user, try again?');
					elseif(self::$verification->verifyUser($_GET['token']) == false)
						$this->formError('Sorry, failed to verify, try again?');
					elseif (Settings::setting('startup_verification') == true)
					{

						$computerid = self::$computer->createComputer($userid, Settings::setting('startup_computer'), self::$internet->getIP());

						if (empty($computerid))
							throw new \Error();

						$class = self::$computer->getComputerClass(Settings::setting('startup_computer'));

						if ($class instanceof Computer == false)
							throw new \Error();

						$class->onStartup($computerid, $userid, [], Settings::setting('default_hardware'));
					}

					$this->formSuccess('login');
				}
			}
			else
				$this->getRender('syscrack/page.verify', [], $this->model() );
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

				if (Settings::setting('startup_verification') == true)
				{

					$computerid = self::$computer->createComputer($userid, Settings::setting('startup_computer'), self::$internet->getIP());

					if (empty($computerid))
					{

						throw new \Error();
					}

					$class = self::$computer->getComputerClass(Settings::setting('startup_computer'));

					if ($class instanceof Computer == false)
					{

						throw new \Error();
					}

					$class->onStartup($computerid, $userid, [], Settings::setting('default_hardware'));
				}
			} catch (\Exception $error)
			{

				$this->formError('Sorry, your account has been verified but we encountered an error: ' . $error->getMessage());
			}

			$this->formSuccess('login');
		}
	}
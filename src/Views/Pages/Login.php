<?php

	namespace Framework\Views\Pages;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class Login
	 *
	 * @package Framework\Views\Pages
	 */

	use Framework\Application\Render;
	use Framework\Application\Settings;
	use Framework\Application\Utilities\PostHelper;

	use Framework\Syscrack\Login\Account;
	use Framework\Views\BaseClasses\Page as BaseClass;

	class Login extends BaseClass
	{

		/**
		 * @var Account
		 */

		protected static $login;

		/**
		 * Login constructor.
		 */

		public function __construct()
		{

			if (isset(self::$login) == false)
				self::$login = new Account();

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
					'GET /login/', 'page'
				],
				[
					'POST /login/', 'process'
				]
			);
		}

		/**
		 * Default page
		 */

		public function page()
		{

			Render::view('syscrack/page.login', [], $this->model());
		}

		/**
		 * Processes a login request
		 */

		public function process()
		{

			try
			{

				if (PostHelper::hasPostData() == false)
					$this->formError('Blank Form');
				else if (PostHelper::checkForRequirements(['username', 'password']) == false)
					$this->formError('Missing Information');
				else
				{

					$username = PostHelper::getPostData('username');
					$password = PostHelper::getPostData('password');

					$result = @self::$login->loginAccount($username, $password);

					if ($result === false)
						/** @noinspection PhpUndefinedVariableInspection */
						$this->formError(self::$login::$error->getMessage());
					else
					{

						$userid = self::$login->getUserID($username);

						if (Settings::setting('login_cleanup_old_sessions') == true)
							self::$session->cleanupSession($userid);

						self::$session->insertSession($userid);
						$this->addConnectedComputer($userid);
						$this->formSuccess('game' );
					}
				}
			}
			catch ( \Error $error )
			{

				$this->formError( $error->getMessage() );
			}
		}

		/**
		 * Adds the current connected computer to the session
		 *
		 * @param $userid
		 */

		private function addConnectedComputer($userid)
		{

			if (self::$computer->userHasComputers($userid) == false)
				throw new \Error('User has no computers');

			self::$computer->setCurrentUserComputer(self::$computer->getUserMainComputer($userid)->computerid);
		}
	}
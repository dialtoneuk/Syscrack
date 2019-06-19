<?php
	declare(strict_types=1);

	namespace Framework\Views\Pages;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class Register
	 *
	 * @package Framework\Views\Pages
	 */

	use Framework\Application;
	use Framework\Application\UtilitiesV2\Container;
	use Framework\Application\Mailer;
	use Framework\Application\Render;
	use Framework\Application\Settings;
	use Framework\Application\Utilities\PostHelper;
	use Framework\Syscrack\BetaKeys;
	use Framework\Syscrack\Register as Account;
	use Framework\Views\BaseClasses\Page as BaseClass;

	/**
	 * Class Register
	 * @package Framework\Views\Pages
	 */
	class Register extends BaseClass
	{

		/**
		 * @var Mailer
		 */

		protected static $mailer;

		/**
		 * @var BetaKeys
		 */

		protected static $betakeys;

		/**
		 * Register constructor.
		 */

		public function __construct()
		{

			if (isset(self::$mailer) == false)
				self::$mailer = new Mailer();

			if (Settings::setting('user_require_betakey'))
				self::$betakeys = new BetaKeys();

			parent::__construct(true, true, false, true);
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
					'GET /register/', 'page'
				],
				[
					'POST /register/', 'process'
				]
			];
		}

		/**
		 * Default page
		 */

		public function page()
		{

			if ( self::$session->isLoggedIn() )
				Render::redirect(Application::globals()->CONTROLLER_INDEX_ROOT . Settings::setting('controller_index_page'));

			Render::view('syscrack/page.register');
		}

		/**
		 * Processes the register request
		 */

		public function process()
		{

			if (Container::get('session')->isLoggedIn())
				Render::redirect(Application::globals()->CONTROLLER_INDEX_ROOT . Settings::setting('controller_index_page'));

			if ( self::$request->empty() )
				$this->formError('Missing Information');
			else if (Settings::setting('user_allow_registrations') == false)
				$this->formError('Registration is currently disabled, sorry...');
			else if ( self::$request->compare(['username', 'password', 'email']) == false)
				$this->formError('Failed compare');
			else
			{

				if (strlen(self::$request->password) < Settings::setting('registration_password_length'))
					$this->formError('Your password is too small, it needs to be longer than ' . Settings::setting('registration_password_length') . ' characters');
				else
				{

					$register = new Account();

					if (Settings::setting('user_require_betakey') && isset( self::$request->betakey ) == false
						&& self::$betakeys->exists( self::$request->betakey ) == false)
						$this->formError('Invalid key, please check for any white spaces or errors in the key and try again');
					else
					{

						if (Settings::setting('user_require_betakey'))
							self::$betakeys->remove( self::$request->betakey );

						if ($register->register(self::$request->username, self::$request->password, self::$request->email ) == false)
							$this->formError(\Framework\Syscrack\Register::$error->getMessage() );
						else
						{

							if (Settings::setting('registration_verification'))
							{
								$this->email(self::$request->email, ['token' => \Framework\Syscrack\Register::$token ]);
								$this->redirect('verify');
							}
							else
								$this->redirect('verify?token=' . \Framework\Syscrack\Register::$token );
						}
					}
				}
			}
		}

		/**
		 * @param self::$request->email
		 * @param array $variables
		 *
		 * @return bool
		 */

		private function email( $email, array $variables)
		{

			$body = self::$mailer->parse(self::$mailer->getTemplate('email.verify.php'), $variables);
			$result = null;

			if (empty($body))
			{

				throw new \Error();
			}

			try
			{
				$result = self::$mailer->send($body, 'Verify your email', self::$request->email);
			}
			catch (\phpmailerException $e)
			{

				Container::get('application')->getErrorHandler()->handleError( $e );
			}

			if ($result == false)
			{

				return false;
			}

			return true;
		}

		public function verification() { }
	}
<?php

	namespace Framework\Views\Pages;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class Register
	 *
	 * @package Framework\Views\Pages
	 */

	use Framework\Application\Container;
	use Framework\Application\Mailer;
	use Framework\Application\Render;
	use Framework\Application\Settings;
	use Framework\Application\Utilities\PostHelper;
	use Framework\Exceptions\SyscrackException;
	use Framework\Syscrack\BetaKeys;
	use Framework\Syscrack\Register as Account;
	use Framework\Views\BaseClasses\Page as BaseClass;
	use Framework\Views\Structures\Page as Structure;

	class Register extends BaseClass implements Structure
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

			parent::__construct(false, true, false, true);
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
					'GET /register/', 'page'
				],
				[
					'POST /register/', 'process'
				]
			);
		}

		/**
		 * Default page
		 */

		public function page()
		{

			if (Container::getObject('session')->isLoggedIn())
				Render::redirect(Settings::setting('controller_index_root') . Settings::setting('controller_index_page'));

			Render::view('syscrack/page.register');
		}

		/**
		 * Processes the register request
		 */

		public function process()
		{

			if (Container::getObject('session')->isLoggedIn())
				Render::redirect(Settings::setting('controller_index_root') . Settings::setting('controller_index_page'));

			if (PostHelper::hasPostData() == false)
				$this->formError('Missing Information');
			else if (Settings::setting('user_allow_registrations') == false)
				$this->formError('Registration is currently disabled, sorry...');
			else if (PostHelper::checkForRequirements(['username', 'password', 'email']) == false)
				$this->formError('Missing Information');

			$username = PostHelper::getPostData('username');
			$password = PostHelper::getPostData('password');
			$email = PostHelper::getPostData('email');

			if (empty($username) || empty($password) || empty($email))
				$this->formError('Missing Information');
			else if (strlen($password) < Settings::setting('registration_password_length'))
				$this->formError('Your password is too small, it needs to be longer than ' . Settings::setting('registration_password_length') . ' characters');
			else
			{

				$register = new Account();

				if (Settings::setting('user_require_betakey') && PostHelper::checkForRequirements(['betakey']) == false
					&& self::$betakeys->exists(PostHelper::getPostData('betakey')) == false)
					$this->formError('Invalid key, please check for any white spaces or errors in the key and try again');
				else
				{

					if (Settings::setting('user_require_betakey'))
						self::$betakeys->remove(PostHelper::getPostData('betakey'));

					$result = @$register->register($username, $password, $email);

					if ($result === false)
						$this->formError("An error occured while trying to create your account. Its been logged and we are on it. Please try again later.");
					else
					{

						if (Settings::setting('registration_verification'))
						{
							$this->sendEmail($email, array('token' => $result));
							$this->redirect('verify');
						}
						else
							$this->redirect('verify?token=' . $result);
					}
				}
			}
		}

		/**
		 * @param $email
		 * @param array $variables
		 *
		 * @return bool
		 */

		private function sendEmail($email, array $variables)
		{

			$body = self::$mailer->parse(self::$mailer->getTemplate('email.verify.php'), $variables);

			if (empty($body))
			{

				throw new SyscrackException();
			}

			$result = self::$mailer->send($body, 'Verify your email', $email);

			if ($result == false)
			{

				return false;
			}

			return true;
		}
	}
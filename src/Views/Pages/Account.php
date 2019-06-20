<?php
	declare(strict_types=1);

	namespace Framework\Views\Pages;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class Account
	 *
	 * @package Framework\Views\Pages
	 */

	use Framework\Views\BaseClasses\Page as BaseClass;

	/**
	 * Class Account
	 * @package Framework\Views\Pages
	 */
	class Account extends BaseClass
	{

		/**
		 * Account constructor.
		 *
		 * @param bool $requirelogin
		 * @param bool $update
		 * @param bool $admin_only
		 */

		public function __construct(bool $requirelogin = true, bool $update = true, bool $admin_only = false) { parent::__construct($requirelogin, $update, $admin_only); }

		/**
		 * Account setup
		 */

		public static function setup( $autoload = true, $session = true )
		{

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
					'/account/logout/', 'logout'
				],
				[
					'GET /account/settings/', "settings"
				],
				[
					'POST /account/settings/', "settingsProcess"
				],
			];
		}

		public function settings()
		{


		}


		public function settingsProcess()
		{


		}

		/**
		 * Default page
		 */

		public function logout()
		{

			parent::$session->cleanupSession(parent::$session->userid());
			parent::$session->destroySession(true);

			$this->formSuccess('login');
		}
	}
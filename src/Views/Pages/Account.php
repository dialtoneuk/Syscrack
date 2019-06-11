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
		 */

		public function __construct()
		{

			parent::__construct(true, true, true, true);
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
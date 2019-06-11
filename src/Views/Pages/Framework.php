<?php
	declare(strict_types=1);

	namespace Framework\Views\Pages;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class Framework
	 *
	 * @package Framework\Views\Pages
	 */

	use Framework\Application\Render;
	use Framework\Application\Settings;
	use Framework\Views\BaseClasses\Page as BaseClass;

	/**
	 * Class Framework
	 * @package Framework\Views\Pages
	 */
	class Framework extends BaseClass
	{

		/**
		 * Framework constructor.
		 */

		public function __construct()
		{

			parent::__construct(false, true);
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
					'/framework/', 'redirectIndex'
				],
				[
					'/framework/error/', 'redirectIndex'
				],
				[
					'/framework/error/database/', 'databaseError'
				],
				[
					'/framework/error/session/', 'sessionError'
				],
				[
					'/framework/error/notfound/', 'notFound'
				]
			];
		}

		/**
		 * Redirects the user to the index
		 */

		public function redirectIndex()
		{

			Render::redirect(Settings::setting('controller_index_root') . Settings::setting('controller_index_page'));
		}

		/**
		 * Renders the 404 page
		 */

		public function notFound()
		{

			Render::view('error/page.404', ['page' => $this->getCurrentPage()], $this->model());
		}

		/**
		 * Renders the database error page
		 */

		public function databaseError()
		{

			Render::view('error/page.database', $this->model());
		}

		/**
		 * Renders the session error page
		 */

		public function sessionError()
		{

			Render::view('error/page.session', $this->model());
		}
	}
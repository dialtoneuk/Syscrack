<?php

	namespace Framework\Views\Pages;

	/**
	 * Lewis Lancaster 2016
	 *
	 * Class Error
	 *
	 * @package Framework\Views\Pages
	 */

	use Framework\Application\UtilitiesV2\Container;
	use Framework\Application\Render;
	use Framework\Application\Settings;
	use Framework\Views\BaseClasses\Page as BaseClass;

	class Error extends BaseClass
	{

		/**
		 * Error constructor.
		 */

		public function __construct()
		{

			parent::__construct(true, false);
		}

		/**
		 * Returns the pages flight mapping
		 *
		 * @return array
		 */

		public function mapping()
		{

			return array(
				[
					'/error/', 'page'
				]
			);
		}

		/**
		 * Default page
		 */

		public function page()
		{

			if( Container::exist("application") == false )
				die("Application global not set so not able to display you the error that just occured. It did just happen though, and you should check your logs ( if you are an sysadmin )");
			else
			{

				if( Settings::setting('error_logging') == false || Settings::setting('error_display_page') == false )
					\Flight::notFound();

				if( Container::get('application')->getErrorHandler()->hasErrors() == false )
					\Flight::notFound();

				$error = Container::get('application')->getErrorHandler()->getLastError();

				Render::view('error/page.error', ['error' => $error ], $this->model() );
			}
		}
	}
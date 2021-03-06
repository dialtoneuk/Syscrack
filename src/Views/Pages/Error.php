<?php
	declare(strict_types=1);

	namespace Framework\Views\Pages;

	/**
	 * Lewis Lancaster 2016
	 *
	 * Class Error
	 *
	 * @package Framework\Views\Pages
	 */

	use Framework\Application\ErrorHandler;
	use Framework\Application\UtilitiesV2\Container;
	use Framework\Application\Render;
	use Framework\Application\Settings;
	use Framework\Views\BaseClasses\Page as BaseClass;

	/**
	 * Class Error
	 * @package Framework\Views\Pages
	 */
	class Error extends BaseClass
	{

		/**
		 * Error constructor.
		 *
		 * @param bool $requirelogin
		 * @param bool $update
		 * @param bool $admin_only
		 */

		public function __construct(bool $requirelogin = false, bool $update = true, bool $admin_only = false) { parent::__construct($requirelogin, $update, $admin_only); }

		/**
		 * Error Setup
		 */

		public static function setup( $autoload = true, $session = true )
		{

			parent::setup(true, false);
		}

		/**
		 * Returns the pages flight mapping
		 *
		 * @return array
		 */

		public function mapping()
		{

			return [
				[
					'/error/', 'page'
				]
			];
		}

		/**
		 * Default page
		 */

		public function page()
		{

			try
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
			catch ( \Error $error  )
			{

				ErrorHandler::prettyPrint( $error );
			}
			catch ( \RuntimeException $error )
			{

				ErrorHandler::prettyPrint( $error );
			}
			catch ( \Exception $error )
			{

				ErrorHandler::prettyPrint( $error );
			}
			catch ( \ErrorException $error )
			{

				ErrorHandler::prettyPrint( $error );
			}
		}
	}
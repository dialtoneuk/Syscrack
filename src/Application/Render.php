<?php
	/**
	 * Created by PhpStorm.
	 * User: lewis
	 * Date: 26/01/2018
	 * Time: 22:31
	 */

	namespace Framework\Application;

	use Flight;
	use Framework\Application\UtilitiesV2\Format;
	use Framework\Application\UtilitiesV2\Interfaces\Response;


	/**
	 * Class Render
	 * @package Framework\Application
	 */

	class Render
	{

		/**
		 * @var array
		 */

		public static $stack = [];

		/**
		 * @var string
		 */

		public static $last_redirect = "";
		/**
		 * Renders a template, takes a model if the mode is MVC
		 *
		 * @param $template
		 *
		 * @param array $array
		 *
		 * @param mixed $model
		 */

		public static function view($template, $array = [], $model = null)
		{

			if (Settings::setting('render_log'))
				self::$stack[] = [
					'template' => $template,
					'array' => $array
				];

			if( $model !== null )
				if( isset( $array["model"] ) == false )
					$array["model"] = $model;

			if( isset( $array["settings"] ) == false )
				$array["settings"] = Settings::settings();

			if( isset( $array["form"] ) == false && FormContainer::empty() == false )
			{
				if (Settings::setting('error_use_session')
					&& Container::getObject('session')->isLoggedIn()
					&& isset($_SESSION["errors"]))
					$array["form"] = $_SESSION["errors"];
				else
					foreach (FormContainer::contents() as $response) /** @var Response $response */
						$array["form"][] = $response->get();

				if (Settings::setting('render_json_output'))
					Flight::json( Format::toArray( $array ) );
				else
					Flight::render(self::getViewFolder() . DIRECTORY_SEPARATOR . $template, $array );
			}
			else
				if (Settings::setting('render_json_output'))
					Flight::json( $array );
				else
					Flight::render(self::getViewFolder() . DIRECTORY_SEPARATOR . $template, $array );
		}

		/**
		 * Redirects the header
		 *
		 * @param $url
		 *
		 * @param int $code
		 */

		public static function redirect($url, $code = 303)
		{

			self::$last_redirect = $url;

			if (Settings::setting('render_mvc_output') == true)
			{
				if (Settings::setting('render_json_output') == true)
				{

					Flight::json(array('redirect' => $url, 'session' => $_SESSION));
				}
				else
				{

					Flight::redirect($url, $code);
				}
			}
			else
			{

				Flight::redirect($url, $code);
			}
		}

		public static function getAssetsLocation()
		{

			return '/'
				. Settings::setting('syscrack_view_location')
				. '/'
				. Settings::setting('render_folder')
				. '/';
		}

		/**
		 * Gets the current view folder
		 *
		 * @return mixed
		 */

		private static function getViewFolder()
		{

			return Settings::setting('render_folder');
		}
	}
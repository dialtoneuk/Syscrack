<?php
	declare(strict_types=1);
	/**
	 * Created by PhpStorm.
	 * User: lewis
	 * Date: 26/01/2018
	 * Time: 22:31
	 */

	namespace Framework\Application;

	use Flight;
	use Framework\Application\Utilities\FileSystem;
	use Framework\Application\UtilitiesV2\Interfaces\Response;
	use Framework\Syscrack\Game\Preferences;
	use Framework\Syscrack\Game\Themes;
	use Framework\Application\UtilitiesV2\Container;
	use Framework\Views\BaseClasses\Page;

	/**
	 * Class Render
	 * @package Framework\Application
	 */

	class Render
	{


		/**
		 * @var array
		 */

		public static $raw;

		/**
		 * @var array
		 */

		public static $stack = [];

		/**
		 * @var string
		 */

		public static $last_redirect = "";

		/**
		 * @var Themes
		 */

		public static $themes;

		/**
		 * @var Preferences
		 */

		protected static $preferences;

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

			if( isset( self::$themes ) == false )
				self::$themes = new Themes();

			if( isset( self::$preferences ) == false )
				self::$preferences  = new Themes();

			if( $model == null )
				if( isset( $array["model"] ) == false )
					$array["model"] = Page::$model;

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

			if( isset( $array["page"] ) == false )
			{

				if( substr(  $_SERVER["REQUEST_URI"], -1  ) == "/" )
					$array["page"] = substr( $_SERVER["REQUEST_URI"], 0, strlen( $_SERVER["REQUEST_URI"] ) - 1 );
				else
					$array["page"] = $_SERVER["REQUEST_URI"];

				if( substr( $array["page"], 0, 1  ) == "/" )
					$array["page"] = substr( $_SERVER["REQUEST_URI"], 1, strlen( $_SERVER["REQUEST_URI"] ) - 1 );

				if( substr( $array["page"], -1  ) == "/" )
					$array["page"] = substr(  $array["page"], 0, strlen( $array["page"] ) - 1 );
			}

			if( isset( $array["form"] ) == false && FormContainer::empty() == false )
				if (Settings::setting('error_use_session')
					&& Container::get('session')->isLoggedIn()
					&& isset($_SESSION["errors"]))
					$array["form"] = $_SESSION["errors"];
				else
					foreach (FormContainer::contents() as $response) /** @var Response $response */
						$array["form"][] = $response->get();

			if( isset( $array["assets"] ) == false )
				$array["assets"] = self::processAssets( self::getAssets() );

			self::$raw = $array;

			if (Settings::setting('render_json_output'))
				Flight::json( $array );
			else
				Flight::render(Settings::setting("syscrack_view_location") . DIRECTORY_SEPARATOR . self::getViewFolder( $template ), $array );
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


			if( $url !== "/" && substr( $url, -1  ) == "/" )
				$url = substr(   $url, 0, strlen( $url ) - 1 );

			if( Settings::setting('error_use_session') )
			{

				$path = $url;
				if( $path !== "/" && substr( $path, 0, 1 ) == "/" )
					$path = substr(   $path, 1, strlen( $path ) );
				$contents = FormContainer::contents();

				if( isset( $_SESSION["form"][ $path ] ) == false )
					$_SESSION["form"][ $path ] = [];
				$_SESSION["form"][ $path ] = $contents;
			}

			self::$last_redirect = $url;

			if (Settings::setting('render_mvc_output') == true)
			{
				if (Settings::setting('render_json_output') == true)
				{

					Flight::json(['redirect' => $url, 'session' => $_SESSION]);
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

		/**
		 * @param $assets
		 *
		 * @return array
		 */
		public static function processAssets( $assets )
		{

			if( isset( self::$themes ) == false )
				self::$themes = new Themes();

			$results = [];

			foreach( $assets as $key=>$asset )
			{

				if ($key == "css")
					$extension = ".css";
				else if ($key == "js")
					$extension = ".js";
				else if ($key == "js_header")
					$extension = ".js";
				else if ($key == "img")
					$extension = ".png";
				else
					$extension = "";

				if( $key == "js_header" )
					$folder = "js";
				else
					$folder = $key;

				if ($extension !== "")
				{

					foreach ($asset as $file)
						if (FileSystem::exists(FileSystem::separate(Settings::setting("syscrack_view_location")
							, Settings::setting("render_folder")
							, $folder
							, $file . $extension
						)))
							$results[$key][] = "/" .FileSystem::separate(Settings::setting("syscrack_view_location"), Settings::setting("render_folder"), $folder, $file . $extension);
						else if (self::$themes->hasBase(self::$themes->currentTheme()))
							$results[$key][] = "/" . FileSystem::separate(Settings::setting("syscrack_view_location"), self::$themes->base(self::$themes->currentTheme()), $folder, $file . $extension);
				}
				else
					foreach ($asset as $file )
						$results[$key][] = "/" .FileSystem::separate(Settings::setting("syscrack_view_location"), Settings::setting("render_folder"), $folder, $file);
			}

			return( $results );
		}

		/**
		 * @return array|mixed
		 */
		public static function getAssets()
		{

			if( isset( self::$themes ) == false )
				self::$themes = new Themes();

			if( self::$themes->hasAssets( self::$themes->currentTheme() ) == false )
				if( self::$themes->hasBase( self::$themes->currentTheme() ) )
				{

					$base = self::$themes->getTheme( self::$themes->base( self::$themes->currentTheme() ) );

					if( isset( $base->data["assets"] ) == false )
						return [];

					return( $base->data["assets"]  );
				}
				else
					return [];
			else
				return( self::$themes->assets( self::$themes->currentTheme() ) );
		}

		/**
		 * @param $template
		 *
		 * @return mixed
		 */

		private static function getViewFolder( $template )
		{

			if( self::$themes->hasBase( self::$themes->currentTheme() ) )
				$base = self::$themes->base( self::$themes->currentTheme() );
			else
				$base = null;

			if( FileSystem::exists(
				FileSystem::separate(
					Settings::setting("syscrack_view_location"), Settings::setting("render_folder"), $template
					) . ".php"
				) == false)
			{

				if( $base === null )
					throw new \Error("Unable to find template at: " . FileSystem::separate(
							Settings::setting("syscrack_view_location"), Settings::setting("render_folder"), $template
						));
				elseif( FileSystem::exists( FileSystem::separate( Settings::setting("syscrack_view_location"), $base, $template) . ".php" ) )
					return( FileSystem::separate( $base, $template ) );
				else
					throw new \Error("Unable to find template at: " . FileSystem::separate(
							Settings::setting("syscrack_view_location"), $base, $template
						));
			}
			else
				return( FileSystem::separate( Settings::setting("render_folder"), $template ) );
		}
	}
<?php
	/**
	 * Created by PhpStorm.
	 * User: newsy
	 * Date: 20/06/2019
	 * Time: 21:12
	 */

	namespace Framework\Views;


	use Framework\Application;
	use Framework\Application\UtilitiesV2\Moddable;
	use Framework\Application\Settings;
	use Framework\Application\UtilitiesV2\Format;
	use Framework\Views\Structures\Page;

	class ControllerV3 extends Moddable
	{

		public static $successful;
		public static $error;

		/**
		 * ControllerV3 constructor.
		 *
		 * @param string $base_filepath
		 * @param string $base_namespace
		 * @param string $mod_type
		 */

		public function __construct($base_filepath = "", $base_namespace = "", string $mod_type = "views")
		{

			if( $base_filepath == "" )
				$base_filepath = Application::globals()->CONTROLLER_FILEPATH;

			if( $base_namespace == "" )
				$base_namespace = Application::globals()->CONTROLLER_NAMESPACE;

			parent::__construct( $base_filepath, $base_namespace, $mod_type);
		}

		/**
		 * Runs the controller
		 */

		public function run()
		{

			$page = $this->page();
			Format::filter( $page );

			try
			{

				if( $this->exist( $page ) == false )
					\Flight::notFound();
				else
				{

					$result = $this->execute( $page, function( $class )
					{

						if( class_exists( $class ) == false )
							throw new \Error("Class does not exist: " . $class );

						Application\UtilitiesV2\Debug::message("calling " . $class . " startup");
						forward_static_call( $class . "::setup");
						Application\UtilitiesV2\Debug::message("successful " . $class . " startup");

						/**
						 * @var $class Page
						 */
						$name = $class;
						$class = new $class;

						if( empty( $class->mapping() ) )
							throw new \Error("Invalid mapping:" . $name );

						foreach ($class->mapping() as $route)
						{

							if (method_exists( $class, $route[1]) == false)
								throw new \Error('Method does not exist in class: ' . $route[0] . " => " . $route[1]);

							\Flight::route($route[0], [$class, $route[1]]);
						}

						return( true );
					});

					if( $result === false )
						Application\ErrorHandler::prettyPrint( Moddable::$error );
					else
						self::$successful = true;
				}
			}
			catch ( \Error $error )
			{

				self::$error = $error;
				self::$successful = false;
			}
			catch ( \RuntimeException $error )
			{

				self::$error = $error;
				self::$successful = false;
			}
		}

		/**
		 * @return mixed
		 */

		public function page()
		{

			$url = $this->getURL();

			if ($this->checkURL($url) == false)
			{

				Flight::notFound();
				exit;
			}

			$page = $this->getPage($url);

			if (empty($page))
				$page = Application::globals()->CONTROLLER_INDEX_PAGE;
			else
				if (Application::globals()->CONTROLLER_INDEX_ROOT !== '/')
					if ('/' . $page[0] == Application::globals()->CONTROLLER_INDEX_ROOT)
						if (isset($page[1]) == false)
							$page = $page[0];
						else
							$page = $page[1];
					else
						$page = $page[0];
				else
					$page = $page[0];

			if ($this->isIndex($page))
				$page = Application::globals()->CONTROLLER_INDEX_PAGE;

			return( $page );
		}

		/**
		 * Returns true if we have URL Keys
		 *
		 * @param $page
		 *
		 * @return bool
		 */

		private function hasURLKey($page)
		{

			if (explode('?', $page))
				return true;

			return false;
		}

		/**
		 * Removes a URL key from the page
		 *
		 * @param $page
		 *
		 * @return mixed
		 */

		private function removeURLKey($page)
		{

			$keys = explode('?', $page);

			if (empty($keys))
				throw new \Error();

			return reset($keys);
		}

		/**
		 * Gets the page
		 *
		 * @param $url
		 *
		 * @return array
		 */

		private function getPage($url)
		{

			return array_values(array_filter(explode('/', $url)));
		}

		/**
		 * Checks the URL
		 *
		 * @param $url
		 *
		 * @return bool
		 */

		private function checkURL($url)
		{

			if (strlen($url) > Application::globals()->MAX_URL_LENGTH )
				return false;

			return true;
		}

		/**
		 * Gets the URL
		 *
		 * @return mixed
		 */

		private function getURL()
		{

			$result = strip_tags( @$_SERVER['REQUEST_URI'] );

			if( $this->hasURLKey( $result ) )
				$result = $this->removeURLKey( $result );

			return( $result );
		}

		/**
		 * Returns true if we are the index
		 *
		 * @param $page
		 *
		 * @return bool
		 */

		private function isIndex($page)
		{

			if (Application::globals()->CONTROLLER_INDEX_ROOT !== '/')
				if ('/' . $page == Application::globals()->CONTROLLER_INDEX_ROOT)
					return true;
				else
					if ($page == Application::globals()->CONTROLLER_INDEX_ROOT)
						return true;

			return false;
		}
	}
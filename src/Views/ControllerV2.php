<?php
	/**
	 * Created by PhpStorm.
	 * User: newsy
	 * Date: 20/06/2019
	 * Time: 04:52
	 */

	namespace Framework\Views;


	use Framework\Application;
	use Framework\Application\UtilitiesV2\Collection;
	use Framework\Syscrack\Game\ModLoader;
	use Flight;
	use Framework\Views\Structures\Page;
	use Framework\Application\Settings;

	class ControllerV2 extends Collection
	{

		/**
		 * @var \Error
		 */

		protected static $error;

		/**
		 * @var Middlewares
		 */

		protected static $middlewares;

		/**
		 * ControllerV2 constructor.
		 *
		 * @param string $filepath
		 * @param string $namespace
		 * @param bool $auto_create
		 */

		public function __construct($filepath="", $namespace="", bool $auto_create = false)
		{

			if( Application::globals()->MIDDLEWARE_ENABLED )
				if( isset( self::$middlewares ) == false )
					self::$middlewares = new Middlewares();

			parent::__construct( $filepath = Application::globals()->CONTROLLER_FILEPATH, $namespace = Application::globals()->CONTROLLER_NAMESPACE, $auto_create);
		}

		/**
		 * Runs the controller
		 */

		public function run()
		{

			if( Application::globals()->MODS_ENABLED )
				$mods = $this->mods();
			else
				$mods = [];

			$pages = array_merge( $mods, $this->namespace( $this->constructor->crawl() ) );

			if( $this->conflicts( $pages ) )
				throw new \Error("Conflict in pages: ");

			$class = $this->find( $pages, $this->page() );

			if( $class === null )
				\Flight::notFound();
			else
			{

				if( Application::globals()->MIDDLEWARE_ENABLED )
				{

					$enabled = true;

					if( Settings::hasSetting("middlewares_disabled_pages") )
						foreach( Settings::setting("middlewares_disabled_pages") as $banned_page )
							if( strtolower( $banned_page ) == strtolower(  $this->page() ) )
								$enabled = false;

					if( $enabled )
						if( self::$middlewares->hasMiddlewares() )
							self::$middlewares->processMiddlewares();
				}

				if( class_exists( $class ) == false )
					throw new \Error("Class does not exist: " . $class );
				else
				{

					//Lets call our set up
					forward_static_call( $class . "::setup");

					//Create the class
					$class = new $class;

					//Do flight
					$this->flight( $class );
				}
			}
		}

		/**
		 * @param Page $page
		 */

		public function flight( Page $page )
		{

			$pages = $page->mapping();

			foreach ($pages as $route)
			{

				if (method_exists($page, $route[1]) == false)
					throw new \Error('Method does not exist in class: ' . $route[0] . " => " . $route[1]);

				Flight::route($route[0], [$page, $route[1]]);
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
				$page = Settings::setting('controller_index_page');
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
				$page = Settings::setting('controller_index_page');

			return( $page );
		}

		/**
		 * @return array
		 */

		public function mods()
		{

			if( ModLoader::loaded() == false )
				return [];
			else
				return( ModLoader::factoryClasses('views') );
		}

		/**
		 * @param $classes
		 * @param $page
		 *
		 * @return null
		 */

		private function find( $classes, $page )
		{

			foreach( $classes as $class )
			{

				$explode = explode("\\", $class );

				if( empty( $explode ) )
					return null;

				$classname = last( $explode );

				if( strtolower( $classname ) == strtolower( $page ) )
					return( $class );
			}

			return null;
		}

		/**
		 * @param $classes
		 *
		 * @return bool
		 */

		private function conflicts( $classes )
		{

			try
			{

				$processed = [];

				foreach( $classes as $class )
				{

					$explode = explode("\\", $class );

					if( empty( $explode ) )
						throw new \Error("invalid: " . $class );

					$class = last( $explode );

					if( empty( $processed ) )
						$processed[ $class ] = true;
					else
						if( isset( $processed[ $class ] ) )
							throw new \Error($class . "conflicting with other classes");
						else
							$processed[ $class ] = true;
				}
			}
			catch ( \Error $error )
			{

				self::$error = $error;
				return( true );
			}

			return( false );
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
		 * @param array $classes
		 *
		 * @return array
		 */

		private function namespace( array $classes )
		{

			foreach( $classes as $key=>$value )
				$classes[ $key ] =  Application::globals()->CONTROLLER_NAMESPACE . $value;

			return( $classes );
		}

		/**
		 * Gets the URL
		 *
		 * @return mixed
		 */

		private function getURL()
		{

			$result = strip_tags($_SERVER['REQUEST_URI']);

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
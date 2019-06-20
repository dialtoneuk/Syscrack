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

	class ControllerV2 extends Collection
	{

		/**
		 * ControllerV2 constructor.
		 *
		 * @param string $filepath
		 * @param string $namespace
		 * @param bool $auto_create
		 */

		public function __construct($filepath="", $namespace="", bool $auto_create = false)
		{

			parent::__construct( $filepath = Application::globals()->CONTROLLER_FILEPATH, $namespace = Application::globals()->CONTROLLER_NAMESPACE, $auto_create);
		}

		public function start()
		{

			if( Application::globals()->MODS_ENABLED )
				$mods = $this->mods();
			else
				$mods = [];

			$pages = array_merge( $mods, $this->namespace( $this->constructor->crawl() ) );

			if( $this->compare( $pages, $this->page() ) == false )
				\Flight::notFound();
			else
			{

				//Code here
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
		 * @return bool
		 */

		private function compare( $classes, $page )
		{

			foreach( $classes as $class )
			{

				$explode = explode("\\", $class );

				if( empty( $explode ) )
					return false;

				$classname = last( $explode );

				if( strtolower( $classname ) == $page );
					return true;
			}

			return false;
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

			return strip_tags($_SERVER['REQUEST_URI']);
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
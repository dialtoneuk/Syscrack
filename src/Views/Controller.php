<?php

	namespace Framework\Views;

	/**
	 * Lewis Lancaster 2016
	 *
	 * Class Controller
	 *
	 * @package Framework\Views
	 */

	use Error;
	use Flight;
	use Framework\Application\Settings;
	use Framework\Application\Utilities\Factory;
	use Framework\Views\Structures\Page;

	class Controller
	{

		/**
		 * @var array
		 */

		protected $middlewares;

		/**
		 * @var Factory
		 */

		protected $factory;

		/**
		 * Public variable of the current page
		 *
		 * @var string
		 */

		public $page;

		/**
		 * Controller constructor.
		 */

		public function __construct()
		{

			$this->factory = new Factory(Settings::setting('controller_namespace'));
		}

		/**
		 * Runs the controller
		 */

		public function run()
		{

			$url = $this->getURL();

			if ($this->checkURL($url) == false)
			{

				Flight::notFound();

				exit;
			}

			$page = $this->getPage($url);

			if (empty($page))
			{

				$page = Settings::setting('controller_index_page');
			}
			else
			{

				//TODO: Make the index root work.. kinda works now but...

				if (Settings::setting('controller_index_root') !== '/')
				{

					if ('/' . $page[0] == Settings::setting('controller_index_root'))
					{

						if (isset($page[1]) == false)
						{

							$page = $page[0];
						}
						else
						{

							$page = $page[1];
						}
					}
					else
					{

						$page = $page[0];
					}
				}
				else
				{

					$page = $page[0];
				}
			}

			if ($this->isIndex($page))
			{

				$page = Settings::setting('controller_index_page');
			}

			$this->page = $page;

			//Disables the developer page from the root

			if (Settings::setting('developer_disabled') == true)
			{

				if ($page == Settings::setting('developer_page'))
				{

					Flight::notFound();

					exit;
				}
			}

			if ($this->hasURLKey($page))
			{

				$page = $this->removeURLKey($page);
			}

			try
			{

				$this->createPage($page);
			} catch (Error $error)
			{

				throw new \Error($page . " =>" . $error->getMessage() . " at line " . $error->getLine());
			}

			if (Settings::setting('middlewares_enabled'))
			{

				if ($this->canExecuteMiddlewaresOnPage($page) == true)
				{

					$this->processMiddlewares();
				}
			}
		}

		/**
		 * Gets an instance of the page class
		 *
		 * @param $page
		 *
		 * @return Page
		 */

		public function getPageClass($page)
		{

			if ($this->factory->classExists($page) == false)
			{

				return null;
			}

			return $this->factory->createClass($page);
		}

		/**
		 * Processes the middlewares
		 */

		private function processMiddlewares()
		{

			$middlewares = new Middlewares();

			if ($middlewares->hasMiddlewares())
			{

				$middlewares->processMiddlewares();
			}
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

			if (Settings::setting('controller_index_root') !== '/')
			{

				if ('/' . $page == Settings::setting('controller_index_root'))
				{

					return true;
				}
			}
			else
			{

				if ($page == Settings::setting('controller_index_root'))
				{

					return true;
				}
			}

			return false;
		}

		/**
		 * Creates the page class
		 *
		 * @param $page
		 */

		private function createPage($page)
		{

			if ($this->factory->classExists($page) == false)
			{

				return;
			}

			$this->processClass($this->factory->createClass($page));
		}


		/**
		 * Processes the class
		 *
		 * @param Page $class
		 */

		private function processClass(Page $class)
		{

			if ($class instanceof Page == false)
			{

				throw new \Error('Class does not have required interface');
			}

			$this->processFlightRoutes($class, $class->mapping());
		}

		/**
		 * Returns true if we can execute middlewares on the current page
		 *
		 * @param $page
		 *
		 * @return bool
		 */

		private function canExecuteMiddlewaresOnPage($page)
		{

			foreach (Settings::setting('middlewares_disabled_pages') as $value)
			{

				if ($page == $value)
				{

					return false;
				}
			}

			return true;
		}

		/**
		 * Processes the flight route
		 *
		 * @param $class
		 *
		 * @param $array
		 *
		 * @return bool
		 */

		private function processFlightRoutes($class, $array)
		{

			foreach ($array as $route)
			{


				if (method_exists($class, $route[1]) == false)
				{

					throw new \Error('Method does not exist in class: ' . $route[0] . " => " . $route[1]);
				}

				Flight::route($route[0], array($class, $route[1]));
			}

			return true;
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
			{

				return true;
			}

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
			{

				throw new \Error();
			}

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

			if (strlen($url) > Settings::setting('controller_url_length'))
			{

				return false;
			}

			return true;
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
	}
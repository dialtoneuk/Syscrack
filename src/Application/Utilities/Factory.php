<?php
	declare(strict_types=1);

	namespace Framework\Application\Utilities;

	/**
	 * Lewis Lancaster 2016
	 *
	 * Class Factory
	 *
	 * @package Framework\Views
	 */

	use Framework\Application\UtilitiesV2\Container;
	use Framework\Syscrack\Game\Interfaces\Computer;
	use Framework\Syscrack\Game\Interfaces\Software;
	use ReflectionClass;

	/**
	 * Class Factory
	 * @package Framework\Application\Utilities
	 */
	class Factory
	{

		/**
		 * Holds the namespace
		 *
		 * @var string
		 */

		public $namespace;

		/**
		 * Holds an array of the created classes
		 *
		 * @var array
		 */

		protected $classes = [];

		/**
		 * Factory constructor.
		 *
		 * @param string $namespace
		 */

		public function __construct($namespace = 'Framework\\Views\\Pages\\')
		{

			$this->namespace = $namespace;
		}

		/**
		 * Creates the class
		 *
		 * @param $class
		 *
		 * @return mixed
		 */

		public function createClass($class)
		{

			$classnamespace = $this->getClass($class);

			if ($classnamespace == $this->namespace)
			{

				throw new \Error('No Class Given');
			}

			$pageclass = new $classnamespace;

			if (empty($pageclass))
			{

				throw new \Error('Class is Empty');
			}

			$this->classes[$class] = $pageclass;

			return $pageclass;
		}

		/**
		 * Returns true if a clas exists
		 *
		 * @param $class
		 *
		 * @return bool
		 */

		public function classExists($class)
		{

			if (class_exists($this->namespace . ucfirst($class)))
			{

				return true;
			}

			return false;
		}

		/**
		 * Returns true if the factory has this kind of class
		 *
		 * @param $name
		 *
		 * @return bool
		 */

		public function hasClass($name)
		{

			if ($this->findClass($name) == null)
			{

				return false;
			}

			return true;
		}

		/**
		 * Finds a class by its name
		 *
		 * @param $name
		 *
		 * @return mixed|null
		 */

		public function findClass($name)
		{

			foreach ($this->classes as $class)
			{

				try
				{
					$reflection = new ReflectionClass($class);
				}
				catch (\ReflectionException $e)
				{

					Container::get('application')->getErrorHandler()->handleError( $e );
				}

				if (empty($reflection))
				{

					throw new \Error();
				}

				if (strtolower($reflection->getShortName()) == strtolower($name))
				{

					return $class;
				}
			}

			return null;
		}

		/**
		 * Gets all of the classes
		 *
		 * @return array|\stdClass|Software|Computer
		 */

		public function getAllClasses()
		{

			return $this->classes;
		}

		/**
		 * Returns the path of this class
		 *
		 * @param $class
		 *
		 * @return string
		 */

		private function getClass($class)
		{

			return sprintf('%s%s', $this->namespace, ucfirst($class));
		}
	}
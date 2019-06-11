<?php
	declare(strict_types=1);

	namespace Framework\Application\UtilitiesV2;

	use Framework\Application;

	/**
	 * Class Collector
	 * @package Framework\Application\UtilitiesV2
	 */

	class Collector
	{

		/**
		 * @var \stdClass
		 */

		protected static $classes = null;

		/**
		 * @throws \Error
		 */

		public static function initialize()
		{

			if ( Application::globals()->DEBUG_ENABLED )
				Debug::message("Collector intialized");

			self::$classes = [];
		}

		/**
		 * @param null $namespace
		 * @param $class
		 *
		 * @return mixed
		 * @throws \Error
		 */

		public static function new($class, $namespace = null)
		{

			if (self::hasInitialized() == false)
				throw new \Error("Initialize first");

			if ($namespace == null)
				$namespace = Application::globals()->COLLECTOR_DEFAULT_NAMESPACE;

			if (self::exists($namespace, $class) == false)
				throw new \Error("Namespace does not exist: " . $namespace . $class);

			if (isset(self::$classes[$namespace . $class]))
			{

				Debug::message("Collector returning pre created class: " . $class);

				return (self::$classes[$namespace . $class]);
			}

			$full_namespace = $namespace . $class;
			self::$classes[$full_namespace] = new $full_namespace;

			Debug::message("Collector returning newly created class: " . $class);

			return (self::$classes[$full_namespace]);
		}

		/**
		 * @param $class
		 * @param null $namespace
		 *
		 * @return mixed
		 * @throws \Error
		 */

		public static function get($class, $namespace = null)
		{

			if (self::hasInitialized() == false)
				throw new \Error("Initialize first");

			if ($namespace == null)
				$namespace = Application::globals()->COLLECTOR_DEFAULT_NAMESPACE;

			return (self::$classes[$namespace . $class]);
		}

		/**
		 * @param $class
		 * @param $as
		 * @param null $namespace
		 *
		 * @return mixed
		 * @throws \Error
		 */

		public static function as($class, $as, $namespace = null)
		{

			if (self::hasInitialized() == false)
				throw new \Error("Initialize first");

			if ($namespace == null)
				$namespace = Application::globals()->COLLECTOR_DEFAULT_NAMESPACE;

			if (isset(self::$classes->$as))
				throw new \Error("Class already exists");

			if (self::exists($namespace, $class) == false)
				throw new \Error("Namespace does not exist: " . $namespace . $class);

			$full_namespace = $namespace . $class;
			self::$classes[$as] = new $full_namespace;

			Debug::message("Collector returning newly created class which is refered to as: " . $as . " ( actual name is " . $class . " )");

			return (self::$classes[$as]);
		}

		/**
		 * @param $class
		 * @param null $namespace
		 *
		 * @return bool
		 */

		public static function exist($class, $namespace = null)
		{

			return (isset(self::$classes[ $namespace . $class ]));
		}


		/**
		 * @return mixed
		 */

		public static function all()
		{

			return (self::$classes);
		}

		/**
		 * @param $namespace
		 * @param $class
		 *
		 * @return bool
		 */

		private static function exists($namespace, $class)
		{

			return (class_exists($namespace . $class));
		}

		/**
		 * @return bool
		 */

		private static function hasInitialized()
		{

			if (self::$classes === null)
				return false;

			return true;
		}
	}
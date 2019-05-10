<?php

	namespace Framework\Application\UtilitiesV2;


	class Collector
	{

		/**
		 * @var \stdClass
		 */

		protected static $classes = null;

		/**
		 * @throws \RuntimeException
		 */

		public static function initialize()
		{

			if (DEBUG_ENABLED)
				Debug::message("Collector intialized");

			self::$classes = [];
		}

		/**
		 * @param null $namespace
		 * @param $class
		 *
		 * @return mixed
		 * @throws \RuntimeException
		 */

		public static function new($class, $namespace = null)
		{

			if (self::hasInitialized() == false)
				throw new \RuntimeException("Initialize first");

			if ($namespace == null)
				$namespace = COLLECTOR_DEFAULT_NAMESPACE;

			if (self::exists($namespace, $class) == false)
				throw new \RuntimeException("Namespace does not exist: " . $namespace . $class);

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
		 * @throws \RuntimeException
		 */

		public static function get($class, $namespace = null)
		{

			if (self::hasInitialized() == false)
				throw new \RuntimeException("Initialize first");

			if ($namespace == null)
				$namespace = COLLECTOR_DEFAULT_NAMESPACE;

			return (self::$classes[$namespace . $class]);
		}

		/**
		 * @param $class
		 * @param $as
		 * @param null $namespace
		 *
		 * @return mixed
		 * @throws \RuntimeException
		 */

		public static function as($class, $as, $namespace = null)
		{

			if (self::hasInitialized() == false)
				throw new \RuntimeException("Initialize first");

			if ($namespace == null)
				$namespace = COLLECTOR_DEFAULT_NAMESPACE;

			if (isset(self::$classes->$as))
				throw new \RuntimeException("Class already exists");

			if (self::exists($namespace, $class) == false)
				throw new \RuntimeException("Namespace does not exist: " . $namespace . $class);

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

			return (isset(self::$classes[$namespace]));
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
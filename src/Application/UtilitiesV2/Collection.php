<?php

	namespace Framework\Application\UtilitiesV2;

	/**
	 * Created by PhpStorm.
	 * User: lewis
	 * Date: 29/08/2018
	 * Time: 21:35
	 */
	abstract class Collection
	{

		/**
		 * @var Constructor
		 */

		protected $constructor;

		/**
		 * @var \Error|null
		 */

		protected $last_error = null;

		/**
		 * ConstructorClass constructor.
		 *
		 * @param $filepath
		 * @param $namespace
		 * @param bool $auto_create
		 */

		public function __construct($filepath, $namespace, $auto_create = true)
		{

			$this->constructor = new Constructor($filepath, $namespace);

			if ($auto_create)
				$this->create();
		}

		/**
		 * @return bool
		 */

		protected final function create()
		{

			if ($this->getLastError() == null)
				$this->setLastError();

			try
			{

				$this->constructor->createAll();
				return true;
			} catch (\Error $error)
			{

				$this->setLastError($error);
			}

			return false;
		}

		/**
		 * @param callable $callback
		 */

		public final function iterate(callable $callback)
		{

			if ($this->constructor->isEmpty())
				throw new \Error("constructor is empty");

			$instances = $this->constructor->getAll(true);

			foreach ($instances as $key => $instance)
				$callback($instance, $key, $this->constructor);
		}

		/**
		 * @param $class_name
		 *
		 * @return mixed
		 */

		public final function get($class_name)
		{

			return ($this->constructor->get($class_name));
		}

		/**
		 * Creates a single class
		 *
		 * @param $class_name
		 */

		public final function single($class_name)
		{

			$this->constructor->createSingular($class_name);
		}

		/**
		 * @param $class_name
		 *
		 * @return bool
		 */

		public function exist($class_name)
		{

			return ($this->constructor->exist($class_name));
		}

		/**
		 * @return \Exception|\Error null
		 */

		public final function getLastError()
		{

			if (empty($this->last_error) || $this->last_error == null)
				return null;

			return ($this->last_error);
		}

		/**
		 * @param null|\Exception $error
		 */

		protected final function setLastError($error = null)
		{

			if ($error !== null)
				if ($error instanceof \Exception == false)
					throw new \Error("invalid error type");

			$this->last_error = $error;
		}
	}
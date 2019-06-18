<?php
	declare(strict_types=1);

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

				if( Debug::isCMD() )
					Debug::msg("Caught error with constructor: " . $error->getMessage() . " in " . $error->getFile() . " on " . $error->getLine() );

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
		 * @param $classname
		 *
		 * @return mixed
		 */

		public final function get($classname)
		{

			return ($this->constructor->get($classname));
		}

		/**
		 * Creates a single class
		 *
		 * @param $classname
		 */

		public final function single($classname)
		{

			$this->constructor->createSingular($classname);
		}

		/**
		 * @param $classname
		 *
		 * @return bool
		 */

		public function exist($classname)
		{

			return ($this->constructor->exist($classname));
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
				if ($error instanceof \Exception == false || $error instanceof \Error == false )
					throw new \Error("invalid error type");

			$this->last_error = $error;
		}
	}
<?php
	/**
	 * Created by PhpStorm.
	 * User: lewis
	 * Date: 20/07/2018
	 * Time: 18:53
	 */

	namespace Framework\Application\UtilitiesV2;


	use Framework\Application\UtilitiesV2\Interfaces\Migrator as MigratorInterface;

	class Migrator
	{

		/**
		 * @var Constructor
		 */

		protected $constructor;

		/**
		 * Migrator constructor.
		 *
		 * @param bool $auto_create
		 *
		 * @throws \RuntimeException
		 */

		public function __construct($auto_create = true)
		{

			$this->constructor = new Constructor(MIGRATOR_ROOT, MIGRATOR_NAMESPACE);

			if ($auto_create == true)
				$this->create();
		}

		/**
		 * @throws \RuntimeException
		 */

		public function create()
		{

			if (empty($this->constructor->createAll()))
				throw new \RuntimeException("No classes found");
		}

		/**
		 * @throws \RuntimeException
		 */

		public function process()
		{

			if (empty($this->constructor->getAll()))
				$this->create();

			foreach ($this->constructor->getAll() as $class => $instance)
			{

				if ($class == FRAMEWORK_BASECLASS)
					continue;

				if ($instance instanceof MigratorInterface == false)
					throw new \RuntimeException("Incorrect class type: " . $class);

				if (Debug::isCMD())
					Debug::echo("Starting: " . MIGRATOR_NAMESPACE . $class, 5);

				/**
				 * @var $instance MigratorInterface
				 */

				$instance->migrate();

				if (Debug::isCMD())
					Debug::echo("Finished: " . MIGRATOR_NAMESPACE . $class, 5);
			}
		}
	}
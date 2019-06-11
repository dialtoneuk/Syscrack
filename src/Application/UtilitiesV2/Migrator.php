<?php
	declare(strict_types=1);
	/**
	 * Created by PhpStorm.
	 * User: lewis
	 * Date: 20/07/2018
	 * Time: 18:53
	 */

	namespace Framework\Application\UtilitiesV2;


	use Framework\Application\UtilitiesV2\Interfaces\Migrator as MigratorInterface;
	use Framework\Application;

	/**
	 * Class Migrator
	 * @package Framework\Application\UtilitiesV2
	 */
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
		 * @throws \Error
		 */

		public function __construct($auto_create = true)
		{

			$this->constructor = new Constructor(Application::globals()->MIGRATOR_ROOT, Application::globals()->MIGRATOR_NAMESPACE);

			if ($auto_create == true)
				$this->create();
		}

		/**
		 * @throws \Error
		 */

		public function create()
		{

			if (empty($this->constructor->createAll()))
				throw new \Error("No classes found");
		}

		/**
		 * @throws \Error
		 */

		public function process()
		{

			if (empty($this->constructor->getAll()))
				$this->create();

			foreach ($this->constructor->getAll() as $class => $instance)
			{

				if ($class == Application::globals()->FRAMEWORK_BASECLASS)
					continue;

				if ($instance instanceof MigratorInterface == false)
					throw new \Error("Incorrect class type: " . $class);

				if (Debug::isCMD())
					Debug::echo("Starting: " . Application::globals()->MIGRATOR_NAMESPACE . $class, 5);

				/**
				 * @var MigratorInterface $instance
				 */

				$instance->migrate();

				if (Debug::isCMD())
					Debug::echo("Finished: " . Application::globals()->MIGRATOR_NAMESPACE . $class, 5);
			}
		}
	}
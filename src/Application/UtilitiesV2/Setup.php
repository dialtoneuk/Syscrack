<?php
	/**
	 * Created by PhpStorm.
	 * User: lewis
	 * Date: 21/07/2018
	 * Time: 02:58
	 */

	namespace Framework\Application\UtilitiesV2;


	use Framework\Application\UtilitiesV2\Interfaces\Setup as SetupInterface;

	class Setup extends Collection
	{

		/**
		 * Setup constructor.
		 *
		 * @param string $filepath
		 * @param string $namespace
		 * @param bool $auto_create
		 */

		public function __construct($filepath, $namespace, bool $auto_create = true)
		{

			parent::__construct($filepath, $namespace, $auto_create);
		}

		/**
		 * @throws \Error
		 */

		public function process()
		{

			if ($this->getLastError() !== null)
				$this->setLastError();

			if ($this->constructor->isEmpty())
				throw new \Error("constructor is empty");

			foreach ($this->constructor->getAll() as $class => $instance)
			{

				if ($instance instanceof SetupInterface == false)
					throw new \Error("Incorrect class type: " . $class);

				/**
				 * @var $instance Setup
				 */

				if ($instance->process() == false)
					return false;

				return true;
			}

			return false;
		}
	}
<?php
	declare(strict_types=1);

	namespace Framework\Application\UtilitiesV2;

	use Framework\Application\UtilitiesV2\Conventions\FileData;
	use Framework\Application\UtilitiesV2\Conventions\TokenData;
	use Framework\Application\UtilitiesV2\Interfaces\Maker;
	use Framework\Application;

	/**
	 * Created by PhpStorm.
	 * User: lewis
	 * Date: 31/08/2018
	 * Time: 21:43
	 */
	class Makers extends Collection
	{

		/**
		 * Makers constructor.
		 *
		 * @param string $filepath
		 * @param string $namespace
		 * @param bool $auto_create
		 */

		public function __construct($filepath = null, $namespace = null, bool $auto_create = true)
		{

			if ($filepath === null)
				$filepath = Application::globals()->MAKER_FILEPATH;

			if ($namespace === null)
				$namespace = Application::globals()->MAKER_NAMESPACE;

			parent::__construct($filepath, $namespace, $auto_create);
		}

		/**
		 * @param TokenData $values
		 * @param string $class_name
		 * @param $path
		 * @param FileData $template
		 *
		 * @return FileData
		 * @throws \Exception
		 */

		public function process(TokenData $values, $class_name, $path, FileData $template = null)
		{

			if ($this->exist($class_name) == false)
				throw new \Error("class does not exist");

			$instance = $this->constructor->get($class_name);

			/** @var Maker $instance */
			if ($instance instanceof Maker == false)
				throw new \Error("invalid instance");

			try
			{

				$instance->before($template);

				if (empty($instance->requiredTokens()) == false)
					if ($this->checkTokenData($values, $instance->requiredTokens()) == false)
						throw new \Error("invalid token data");

				return ($instance->make($values, $path));

			} catch (\Exception $exception)
			{

				throw $exception;
			}
		}

		/**
		 * @param $class_name
		 *
		 * @return array
		 */

		public function getRequiredTokens($class_name)
		{


			if ($this->exist($class_name) == false)
				throw new \Error("class does not exist");

			$instance = $this->constructor->get($class_name);

			/** @var Maker $instance */
			if ($instance instanceof Maker == false)
				throw new \Error("invalid instance");

			return ($instance->requiredTokens());
		}

		/**
		 * @param $class_name
		 *
		 * @return string
		 */

		public function getNamespace($class_name)
		{

			if ($this->exist($class_name) == false)
				throw new \Error("class does not exist");

			$instance = $this->constructor->get($class_name);

			/** @var Maker $instance */
			if ($instance instanceof Maker == false)
				throw new \Error("invalid instance");

			return ($instance->namespace());
		}

		/**
		 * @param $class_name
		 *
		 * @return string
		 */

		public function getFilepath($class_name)
		{

			if ($this->exist($class_name) == false)
				throw new \Error("class does not exist");

			$instance = $this->constructor->get($class_name);

			/** @var Maker $instance */
			if ($instance instanceof Maker == false)
				throw new \Error("invalid instance");

			return ($instance->filepath());
		}

		/**
		 * @param TokenData $values
		 * @param $requirements
		 *
		 * @return bool
		 */

		private function checkTokenData(TokenData $values, $requirements)
		{

			foreach ($requirements as $key => $value)
				if (isset($values->values[$value]) == false)
					return false;

			return true;
		}
	}
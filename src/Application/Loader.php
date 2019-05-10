<?php

	namespace Framework\Application;

	/**
	 * Lewis Lancaster 2016
	 *
	 * Class Loader
	 *
	 * @package Framework
	 */

	use Exception;
	use Framework\Exceptions\ApplicationException;
	use ReflectionMethod;

	class Loader
	{

		/**
		 * @var array
		 */

		protected $classes = [];


		/**
		 * Loads the payload
		 */

		public function loadPaypload()
		{

			$array = $this->readPayload();

			if (empty($array))
			{

				throw new ApplicationException();
			}

			foreach ($array as $class => $method)
			{

				if (isset($this->classes[$class]))
				{

					if ($this->isMethodStatic($class, $method))
						forward_static_call(array($this->classes[$class], $method));

					else
						call_user_func(array($this->classes[$class], $method));

				}
				else
				{

					if ($this->isMethodStatic($class, $method))
					{

						$this->classes[$class] = $method;

						forward_static_call(array($class, $method));
					}
					else
					{

						$this->createClass($class);

						call_user_func(array($this->classes[$class], $method));
					}
				}

			}
		}

		/**
		 * Creates a class
		 *
		 * @param $class
		 *
		 * @return mixed
		 */

		private function createClass($class)
		{

			$class_instance = new $class;

			if (isset($this->classes[$class]))
			{

				return $class_instance;
			}

			$this->classes[$class] = $class;

			return $class_instance;
		}

		/**
		 * Returns true if the instance is a static method
		 *
		 * @param $class
		 *
		 * @param $method
		 *
		 * @return bool
		 */

		private function isMethodStatic($class, $method)
		{

			try
			{

				$class = new ReflectionMethod($this->returnStaticHead($class, $method));

				if ($class->isStatic())
				{

					return true;
				}

				return false;
			} catch (Exception $error)
			{

				return false;
			}
		}

		/**
		 * Reads the payload
		 *
		 * @return mixed
		 */

		private function readPayload()
		{

			if (file_exists($this->getFileLocation()) == false)
			{

				return null;
			}

			return json_decode(file_get_contents($this->getFileLocation()), true);
		}

		/**
		 * Gets the payload location
		 *
		 * @return string
		 */

		private function getFileLocation()
		{

			return $_SERVER['DOCUMENT_ROOT'] . '/data/config/autoloader.json';
		}

		/**
		 * Returns the head of a function
		 *
		 * @param $class
		 *
		 * @param $method
		 *
		 * @return string
		 */

		private function returnStaticHead($class, $method)
		{

			return sprintf('%s::%s', $class, $method);
		}
	}
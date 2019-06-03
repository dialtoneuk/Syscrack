<?php

	namespace Framework\Application\Api;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class Controller
	 *
	 * @package Framework\Application\Api
	 */

	use Framework\Application\Api\Structures\Endpoint;
	use Framework\Application\Settings;
	use Framework\Application\Utilities\Factory;
	use Framework\Application\Utilities\PostHelper;
	use Framework\Exceptions\ApplicationException;
	use ReflectionClass;

	class Controller
	{

		/**
		 * @var Factory
		 */

		protected $factory;

		/**
		 * Controller constructor.
		 */

		public function __construct()
		{

			$this->factory = new Factory('Framework\\Application\\Api\\Endpoints\\');
		}

		/**
		 * Returns true if this endpoint clsas exists
		 *
		 * @param $endpoint
		 *
		 * @return bool
		 */

		public function hasEndpoint($endpoint)
		{

			if ($this->factory->classExists($endpoint) == false)
			{

				return false;
			}

			return true;
		}

		/**
		 * Creates a new endpoitn class
		 *
		 * @param $endpoint
		 *
		 * @return mixed
		 */

		public function createEndpoint($endpoint)
		{

			return $this->factory->createClass($endpoint);
		}

		/**
		 * Processes the endpoint and class the retrospective method
		 *
		 * @param $endpoint
		 *
		 * @param $method
		 *
		 * @return mixed
		 * @throws \ReflectionException
		 */

		public function processEndpoint($endpoint, $method)
		{

			if ($this->hasEndpoint($endpoint) == false)
			{

				throw new ApplicationException();
			}

			$endpoint = $this->createEndpoint($endpoint);

			if ($endpoint instanceof Endpoint == false)
			{

				throw new ApplicationException();
			}

			if ($this->methodExists($endpoint, $method) == false)
			{

				throw new ApplicationException();
			}

			if ($this->isMethodSafe($endpoint, $method) == false)
			{

				throw new ApplicationException();
			}

			if ($this->isMethodCallable($endpoint, $method) == false)
			{

				throw new ApplicationException();
			}

			if ($this->methodHasRequirements($endpoint, $method))
			{

				$requirements = $this->getRequirements($endpoint, $method);

				if (PostHelper::checkForRequirements($requirements) == false)
				{

					throw new ApplicationException();
				}

				$requirements = PostHelper::returnRequirements($requirements);

				if (empty($requirements))
				{

					throw new ApplicationException();
				}

				return call_user_func_array(array($endpoint, $method), $requirements);
			}

			return $endpoint->{$method};
		}

		/**
		 * Checks if the method is safe to call
		 *
		 * @param Endpoint $endpoint
		 *
		 * @param $method
		 *
		 * @return bool
		 * @throws \ReflectionException
		 */

		public function isMethodSafe(Endpoint $endpoint, $method)
		{

			if ($method[0] == '_')
			{

				return false;
			}

			if (strlen($method) > Settings::setting('api_method_length'))
			{

				return false;
			}

			if ($this->isMethodCallable($endpoint, $method) == false)
			{

				return false;
			}

			return true;
		}

		/**
		 * @param Endpoint $endpoint
		 * @param $method
		 *
		 * @return bool
		 * @throws \ReflectionException
		 */

		private function isMethodCallable(Endpoint $endpoint, $method)
		{

			$reflection = new ReflectionClass($endpoint);

			if ($reflection->getMethod($method)->isPublic() == false)
			{

				return false;
			}

			return true;
		}

		/**
		 * Gets this endpoints requirements
		 *
		 * @param Endpoint $class
		 *
		 * @param $method
		 *
		 * @return mixed
		 */

		private function getRequirements(Endpoint $class, $method)
		{

			$requirements = $class->requirements();

			if (isset($requirements[$method]) == false)
			{

				throw new ApplicationException();
			}

			return $requirements[$method];
		}

		/**
		 * Checks if the method has requirements
		 *
		 * @param Endpoint $class
		 *
		 * @param $method
		 *
		 * @return bool
		 */

		private function methodHasRequirements(Endpoint $class, $method)
		{

			$requirements = $class->requirements();

			if (empty($requirements))
			{

				return false;
			}

			if (isset($requirements[$method]))
			{

				return true;
			}

			return false;
		}

		private function methodExists($class, $method)
		{

			if (method_exists($class, $method) == false)
			{

				return false;
			}

			return true;
		}
	}
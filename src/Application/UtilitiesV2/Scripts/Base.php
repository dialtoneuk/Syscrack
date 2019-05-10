<?php
	/**
	 * Created by PhpStorm.
	 * User: lewis
	 * Date: 22/07/2018
	 * Time: 01:01
	 */

	namespace Framework\Application\UtilitiesV2\Scripts;

	use Framework\Application\UtilitiesV2\Interfaces\Script;

//use Colourspace\Application;
//use Colourspace\Database\Connection;
//use Framework\Application\UtilitiesV2\Session;

	abstract class Base implements Script
	{

		/**
		 * @param $arguments
		 *
		 * @return bool
		 */

		public function execute($arguments)
		{

			return (true);
		}

		/**
		 * @return array|null
		 */

		public function requiredArguments()
		{

			return (null);
		}

		/**
		 * @return array
		 */

		public function help()
		{

			return ([
				"arguments" => $this->requiredArguments(),
				"help" => "No help available."
			]);
		}

		/**
		 * @param $arguments
		 *
		 * @return array
		 * @throws \RuntimeException
		 */

		protected function parse($arguments)
		{

			$returns = [];

			if (count(explode(",", $arguments)) == 1)
			{

				if (count(explode(":", $arguments)) == 1)
					throw new \RuntimeException("Invalid arguments");
				else
				{

					$arguments = explode(":", $arguments);
					$returns[$arguments[0]] = $arguments[1];
				}
			}
			else
			{

				$arguments = explode(",", $arguments);

				foreach ($arguments as $argument)
				{

					if (count(explode(":", $argument)) == 1)
						throw new \RuntimeException("Invalid arguments");
					else
					{

						$stems = explode(":", $argument);
						$returns[$stems[0]] = $stems[1];
					}
				}
			}

			return ($returns);
		}

		/**
		 * @param bool $session
		 *
		 * @throws \RuntimeException
		 */

		public final function initContainer($session = false)
		{

			/**
			 * Debug::message("Instancing a global of the application inside the container" );
			 *
			 * $application = new Application();
			 * $application->connection = new Connection();
			 *
			 * Container::add("application", $application );
			 * Container::get("application")->session = new Session( $session );
			 **/
		}
	}
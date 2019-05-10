<?php
	/**
	 * Created by PhpStorm.
	 * User: lewis
	 * Date: 22/07/2018
	 * Time: 01:38
	 */

	namespace Framework\Application\UtilitiesV2\Scripts;

	use Framework\Application\UtilitiesV2\Container;
	use Framework\Application\UtilitiesV2\Debug;


	class DebugConnection extends Base
	{

		/**
		 * @param $arguments
		 *
		 * @return bool
		 * @throws \RuntimeException
		 */

		public function execute($arguments)
		{

			if (Container::exist("application") == false)
				$this->initContainer();

			$application = Container::get("application");

			Debug::echo("Testing database connection", 3);

			if (@$application->connection->test() == false)
				return false;

			Debug::echo("Testing session capability", 3);

			try
			{

				$application->session->initialize(false);

				if ($application->session->all()->isEmpty())
					Debug::echo("Table successfully quiried", 4);
				else
					Debug::echo("Table successfully quiried", 4);
			} catch (\RuntimeException $error)
			{

				return (false);
			}

			return (true);
		}

		public function requiredArguments()
		{

			return (null);
		}
	}
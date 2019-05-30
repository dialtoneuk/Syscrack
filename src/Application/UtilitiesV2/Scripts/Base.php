<?php
	/**
	 * Created by PhpStorm.
	 * User: lewis
	 * Date: 22/07/2018
	 * Time: 01:01
	 */

	namespace Framework\Application\UtilitiesV2\Scripts;

	use Framework\Application\UtilitiesV2\Interfaces\Script;
	use Framework\Application\UtilitiesV2\Debug;
	use Framework\Database\Manager;

	/**
	 * Class Base
	 * @package Framework\Application\UtilitiesV2\Scripts
	 */

	abstract class Base implements Script
	{

		/**
		 * Base constructor.
		 */

		public function __construct()
		{

			if( Debug::isCMD() == false )
				throw new \Error("Attempted to create script class when CMD mode is in inactive");
		}

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
		 * @throws \Error
		 */

		protected function parse($arguments)
		{

			$returns = [];

			if (count(explode(",", $arguments)) == 1)
			{

				if (count(explode(":", $arguments)) == 1)
					throw new \Error("Invalid arguments");
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
						throw new \Error("Invalid arguments");
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
		 * @param $message
		 *
		 * @return bool
		 */

		protected function error( $message ): bool
		{

			Debug::echo( $message );
			return( false );
		}

		/**
		 *
		 */

		public final function initDatabase()
		{

			 Debug::message("Instancing a global of the application inside the container" );
			 new Manager( true );
		}
	}
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
		 * @throws \Error
		 */

		public function execute($arguments)
		{

			if( Container::exist('database') == false )
				$this->initDatabase();

			/**
			 * @var $database \Illuminate\Database\Capsule\Manager
			 */
			$database = Container::get('database');

			try
			{

				$database->getConnection()->getDatabaseName();
			}
			catch ( \Error $error )
			{

				Debug::echo("ERROR: " . $error->getMessage() );
				return false;
			}

			Debug::echo("\nConnection Successful!\n");
			Debug::echo("Database Information");
			Debug::echo( "name: " . $database->getConnection()->getDatabaseName(), 1 );
			Debug::echo( "driver: " . $database->getConnection()->getDriverName(), 1 );
			Debug::echo( $database->getConnection()->getQueryLog() );
			Debug::echo("");

			return (true);
		}

		public function requiredArguments()
		{

			return (null);
		}
	}
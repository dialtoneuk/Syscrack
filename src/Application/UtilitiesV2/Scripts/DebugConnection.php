<?php
	/**
	 * Created by PhpStorm.
	 * User: lewis
	 * Date: 22/07/2018
	 * Time: 01:38
	 */

	namespace Framework\Application\UtilitiesV2\Scripts;

	use Framework\Application\Container;
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

			if( Container::hasObject('database') == false )
				$this->initDatabase();

			/**
			 * @var $database \Illuminate\Database\Capsule\Manager
			 */
			$database = Container::getObject('database');

			try
			{

				$database->getConnection()->getDatabaseName();
			}
			catch ( \Error $error )
			{

				Debug::echo("ERROR: " . $error->getMessage() );
				return false;
			}

			Debug::echo("Information");
			Debug::echo( $database->getConnection()->getDatabaseName() );
			Debug::echo( $database->getConnection()->getDriverName() );
			Debug::echo("Query Log");
			Debug::echo( $database->getConnection()->getQueryLog() );

			return (true);
		}

		public function requiredArguments()
		{

			return (null);
		}
	}
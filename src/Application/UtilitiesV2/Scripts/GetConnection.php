<?php
	declare(strict_types=1);
	/**
	 * Created by PhpStorm.
	 * User: lewis
	 * Date: 22/07/2018
	 * Time: 01:01
	 */

	namespace Framework\Application\UtilitiesV2\Scripts;

	use Framework\Application\UtilitiesV2\Container;
	use Framework\Application\UtilitiesV2\Debug;

	/**
	 * Class GetConnection
	 * @package Framework\Application\UtilitiesV2\Scripts
	 */
	class GetConnection extends Base
	{

		/**
		 * @param $arguments
		 *
		 * @return bool
		 * @throws \Error
		 */

		public function execute($arguments)
		{

			if (Container::exist("application") == false)
				$this->initDatabase();

			Debug::echo(print_r(Container::get('database')->getConnection()->getQueryLog() ) );

			return (true);
		}

		/**
		 * @return array|null
		 */

		public function requiredArguments()
		{

			return (null);
		}
	}
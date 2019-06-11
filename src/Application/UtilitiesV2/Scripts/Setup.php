<?php
	declare(strict_types=1);
	/**
	 * Created by PhpStorm.
	 * User: lewis
	 * Date: 22/07/2018
	 * Time: 01:01
	 */

	namespace Framework\Application\UtilitiesV2\Scripts;


	/**
	 * Class Setup
	 * @package Framework\Application\UtilitiesV2\Scripts
	 */
	class Setup extends Base
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
	}
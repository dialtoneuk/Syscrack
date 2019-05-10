<?php
	/**
	 * Created by PhpStorm.
	 * User: lewis
	 * Date: 22/07/2018
	 * Time: 00:46
	 */

	namespace Framework\Application\UtilitiesV2\Interfaces;


	interface Script
	{

		/**
		 * @param $arguments
		 *
		 * @return bool
		 */

		public function execute($arguments);

		/**
		 * @return array|bool|null
		 */

		public function requiredArguments();


		/**
		 * @return array
		 */

		public function help();
	}
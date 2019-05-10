<?php
	/**
	 * Created by PhpStorm.
	 * User: lewis
	 * Date: 05/08/2018
	 * Time: 03:00
	 */

	namespace Framework\Application\UtilitiesV2\Migrators;


	use Framework\Application\UtilitiesV2\Interfaces\Migrator;

	abstract class Base implements Migrator
	{

		/**
		 * @return mixed
		 */

		public function migrate()
		{

			return (true);
		}
	}
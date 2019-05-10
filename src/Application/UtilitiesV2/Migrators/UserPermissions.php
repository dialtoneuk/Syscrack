<?php
	/**
	 * Created by PhpStorm.
	 * User: lewis
	 * Date: 20/07/2018
	 * Time: 20:20
	 */

	namespace Framework\Application\UtilitiesV2\Migrators;


	class UserPermissions extends Base
	{

		/**
		 * @throws \RuntimeException
		 */

		public function migrate()
		{

			if (file_exists(SYSCRACK_ROOT . USER_PERMISSIONS_ROOT) == false)
				mkdir(SYSCRACK_ROOT . USER_PERMISSIONS_ROOT);

			file_put_contents(SYSCRACK_ROOT . USER_PERMISSIONS_ROOT . "config.json", json_encode([
				"created" => time()
			]));
		}
	}
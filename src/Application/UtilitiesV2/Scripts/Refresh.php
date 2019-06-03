<?php

	namespace Framework\Application\UtilitiesV2\Scripts;

	use Framework\Application\UtilitiesV2\Container;
	use Framework\Application\UtilitiesV2\Scripts;
	use Framework\Application\UtilitiesV2\Debug;

	/**
	 * Created by PhpStorm.
	 * User: lewis
	 * Date: 31/08/2018
	 * Time: 17:37
	 */
	class Refresh extends Base
	{

		public function execute($arguments)
		{

			if (Instance::$active_instance == false)
				throw new \Error("Needs an active instance");

			//Remove
			Container::remove("scripts");

			//Add it again
			Container::add("scripts", new Scripts(["cmd/execute.php", CLI_DEFAULT_COMMAND ], true ));
			Debug::echo("Refreshed scripts engine updating instance...", 2);

			//Execute refresh
			Container::get('scripts')->execute('_Refresh');

			return parent::execute($arguments); // TODO: Change the autogenerated stub
		}

		public function help()
		{
			return ([
				"arguments" => $this->requiredArguments(),
				"help" => "Refreshes the instance, use this when you are developing new scripts. Once refreshed new classes will appear " .
					"however, you cannot modify the contents of a script then use refresh. it seems that PHP auto caches it when the instance is " .
					"created, so you'll have to restart your instance in order for code changes to have an effect!"
			]);
		}

	}
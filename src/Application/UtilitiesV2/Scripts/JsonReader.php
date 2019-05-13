<?php
	/**
	 * Created by PhpStorm.
	 * User: lewis
	 * Date: 05/08/2018
	 * Time: 14:16
	 */

	namespace Framework\Application\UtilitiesV2\Scripts;


	use Framework\Application\UtilitiesV2\Debug;

	class JsonReader extends Base
	{

		/**
		 * @param $arguments
		 *
		 * @return bool
		 * @throws \Error
		 */

		public function execute($arguments)
		{

			if (count(explode(".", $arguments["file"])) == 1)
				$arguments["file"] = $arguments["file"] . ".json";

			if (file_exists(SYSCRACK_ROOT . "config/" . $arguments["file"]) == false)
				throw new \Error("File does not exist");

			if (Debug::isCMD())
				Debug::echo("Opening file: " . $arguments["file"], 1);

			$contents = file_get_contents(SYSCRACK_ROOT . "config/" . $arguments["file"]);

			if (Debug::isCMD())
				Debug::echo($contents);

			return true;
		}

		/**
		 * @return array|null
		 */

		public function requiredArguments()
		{

			return ([
				"file"
			]);
		}
	}
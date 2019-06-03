<?php
	/**
	 * Created by PhpStorm.
	 * User: lewis
	 * Date: 21/07/2018
	 * Time: 03:26
	 */

	namespace Framework\Application\UtilitiesV2\Setups;

	use Framework\Application;

	class Ffmpeg extends Base
	{

		/**
		 * Aws constructor.
		 * @throws \Error
		 */

		public function __construct()
		{

			if ($this->exists(Application::globals()->FFMPEG_CONFIG_FILE) == false)
				throw new \Error("File does not exist");

			parent::__construct();
		}

		/**
		 * @return bool
		 */

		public function process()
		{

			$inputs = $this->getInputs([
				"root",
				"real",
				"timeout",
				"threads",
				"ffmpeg",
				"ffprobe"
			]);

			$inputs["files"] = [
				"ffmeg" => $inputs["ffmeg"],
				"ffprobe" => $inputs["ffprobe"]
			];

			unset($inputs["ffmeg"]);
			unset($inputs["ffprobe"]);

			$this->write(Application::globals()->FFMPEG_CONFIG_FILE, $inputs);

			return parent::process();
		}
	}
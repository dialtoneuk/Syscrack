<?php
	/**
	 * Created by PhpStorm.
	 * User: lewis
	 * Date: 06/07/2018
	 * Time: 16:15
	 */

	namespace Framework\Application\UtilitiesV2;

	use Framework\Application;

	class ResourceUnpacker
	{

		/**
		 * @var null|string
		 */

		protected $filepath;

		/**
		 * ResourceUnpacker constructor.
		 *
		 * @param null $filepath
		 *
		 * @throws \Error
		 */

		public function __construct($filepath = null)
		{

			if ($filepath == null)
				$filepath = Application::globals()->RESOURCE_COMBINER_FILEPATH;

			//if( file_exists( SYSCRACK_ROOT . $filepath ) == false )
			//throw new \Error("File does not exist " . SYSCRACK_ROOT . $filepath);

			$this->filepath = $filepath;
		}

		/**
		 * @throws \Error
		 */

		public function process()
		{

			$resources = $this->get();

			if (empty($resources))
				throw new \Error("Resources are empty");

			if (Debug::isCMD())
				Debug::echo("Total Packed Objects: " . count($resources), 4);

			$count = 0;

			foreach ($resources as $path => $contents)
			{

				if (Debug::isCMD())
					Debug::echo("Upacking File: " . $path, 5);

				if (file_exists(SYSCRACK_ROOT . $path))
				{

					if (Debug::isCMD())
						Debug::echo("Exists: " . $path, 6);

					continue;
				}

				$directory = $this->getDirectory($path);

				if (file_exists(SYSCRACK_ROOT . $directory) == false)
					$this->createFolder($directory);

				if (strpos($path, ".json") !== false )
				{

					if (Application::globals()->RESOURCE_COMBINER_PRETTY)
						$this->createFile($path, json_encode($contents, JSON_PRETTY_PRINT));
					else
						$this->createFile($path, json_encode($contents));
				}
				else
					$this->createFile($path, Format::decodeLargeText($contents));

				if (Application::globals()->RESOURCE_COMBINER_CHMOD)
					chmod(SYSCRACK_ROOT . $path, Application::globals()->RESOURCE_COMBINER_CHMOD_PERM);

				$count++;
			}

			if (Debug::isCMD())
				Debug::echo("Total Unpacked Objects: " . $count, 4);
		}

		/**
		 * @return mixed
		 * @throws \Error
		 */

		public function get()
		{

			return ($this->read());
		}

		/**
		 * @param string $contents
		 * @param bool $decode
		 * @param bool $array
		 *
		 * @return mixed
		 */

		public function unpackString(string $contents, $decode = true, $array = true)
		{

			if ($decode)
				return (json_decode(Format::decodeLargeText($contents), $array));
			else
				return (json_decode($contents, $array));
		}

		/**
		 * @param $filepath
		 * @param string $contents
		 *
		 * @throws \Error
		 */

		private function createFile($filepath, string $contents)
		{


			if (is_dir(SYSCRACK_ROOT . $filepath))
				throw new \Error("Not a file");

			if (file_exists(SYSCRACK_ROOT . $filepath))
				throw new \Error("File already exists");

			file_put_contents(SYSCRACK_ROOT . $filepath, $contents);

			if (Application::globals()->RESOURCE_COMBINER_CHMOD)
				chmod(SYSCRACK_ROOT . $filepath, Application::globals()->RESOURCE_COMBINER_CHMOD_PERM);
		}

		/**
		 * @param $directory
		 *
		 * @throws \Error
		 */

		private function createFolder($directory)
		{

			if (is_file(SYSCRACK_ROOT . $directory))
				throw new \Error("Not a folder");

			if (file_exists(SYSCRACK_ROOT . $directory))
				throw new \Error("Folder already exists");

			mkdir(SYSCRACK_ROOT . $directory);

			if (Application::globals()->RESOURCE_COMBINER_CHMOD)
				chmod(SYSCRACK_ROOT . $directory, Application::globals()->RESOURCE_COMBINER_CHMOD_PERM);
		}

		/**
		 * @param $filepath
		 *
		 * @return string
		 */

		private function getDirectory($filepath)
		{


			$exploded = explode("/", $filepath);
			array_pop($exploded);

			return (implode("/", $exploded));
		}

		/**
		 * @param bool $decode
		 * @param bool $array
		 *
		 * @return mixed
		 * @throws \Error
		 */

		private function read($decode = true, $array = true)
		{

			$contents = Format::decodeLargeText(file_get_contents(SYSCRACK_ROOT . $this->filepath));

			if (empty($contents))
				throw new \Error("Empty contents");

			return (json_decode($contents, $array));
		}
	}
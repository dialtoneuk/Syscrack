<?php
	/**
	 * Created by PhpStorm.
	 * User: lewis
	 * Date: 06/07/2018
	 * Time: 10:45
	 */

	namespace Framework\Application\UtilitiesV2;


	use Framework\Application;

	class ResourceCombiner
	{

		/**
		 * @var DirectoryOperator
		 */

		protected $directory;

		/**
		 * ResourceCombiner constructor.
		 *
		 * @param null $directory
		 *
		 * @throws \Error
		 */

		public function __construct($directory = null)
		{

			if ($directory == null)
				$directory = Application::globals()->RESOURCE_COMBINER_ROOT;

			//if( file_exists( SYSCRACK_ROOT . $directory ) == false )
			//throw new \Error("Folder does not exist " . SYSCRACK_ROOT . $directory);

			$this->directory = new DirectoryOperator($directory);
		}

		/**
		 * @return array|null
		 * @throws \Error
		 */

		public function build()
		{

			if ($this->directory->isEmpty())
				return null;

			if (Debug::isCMD())
				Debug::echo("Packing directory: " . $this->directory->path(), 4);

			$finished = false;
			$last = false;
			$result = [];
			$dirs = [];

			while ($finished == false)
			{

				$files = $this->omitRoot($this->directory->path(), $this->files());

				if (empty($files) == false)
				{

					foreach ($files as $file)
					{

						$operator = new FileOperator($this->directory->path() . $file);

						if (isset($result[$this->directory->path() . $file]))
							continue;

						if ($operator->isJson() == false)
						{

							if (defined("CMD"))
								Debug::echo("Packing file: " . $this->directory->path() . $file, 5);

							$result[$this->directory->path() . $file] = Format::largeText($operator->contents);
						}
						else
						{

							if (defined("CMD"))
								Debug::echo("Packing file: " . $this->directory->path() . $file, 5);

							$result[$this->directory->path() . $file] = $operator->decodeJSON(true);
						}
					}
				}

				if ($this->directory->hasDirs())
				{

					$dirs = array_merge($this->omitRoot("config/", $this->directory->getDirs()), $dirs);

					foreach ($dirs as $key => $dir)
					{

						//TODO: Move to a json file?
						if ($dir == "groups/user" || $dir == "debug/log")
							unset($dirs[$key]);
					}
				}
				else
				{

					if (empty($dirs))
						break;
				}

				if (empty($dirs) == false)
				{

					foreach ($dirs as $key => $dir)
					{

						$this->directory->setPath(Application::globals()->RESOURCE_COMBINER_ROOT . $dir . "/");
						unset($dirs[$key]);

						if (empty($dirs) == false)
							break;
						else
							$last = true;
					}
				}
				else
					$last = true;

			}

			if (Debug::isCMD())
				Debug::echo("Total Packed Objects: " . count($result), 4);

			return ($result);
		}

		/**
		 * @param $build
		 * @param null $filepath
		 * @param bool $encode
		 *
		 * @throws \Error
		 */

		public function save($build, $filepath = null, $encode = true)
		{

			if ($filepath == null)
				$filepath = Application::globals()->RESOURCE_COMBINER_FILEPATH;

			if (is_array($build) == false && is_object($build) == false)
				throw new \Error("Should either be array or object");


			if (Application::globals()->RESOURCE_COMBINER_PRETTY)
				$json = json_encode($build, JSON_PRETTY_PRINT);
			else
				$json = json_encode($build);

			if ($encode)
				file_put_contents(SYSCRACK_ROOT . $filepath, Format::largeText($json));
			else
				file_put_contents(SYSCRACK_ROOT . $filepath, $json);

			if (Application::globals()->RESOURCE_COMBINER_CHMOD)
				chmod(SYSCRACK_ROOT . $filepath, Application::globals()->RESOURCE_COMBINER_CHMOD_PERM);
		}

		/**
		 * @param $dirs
		 *
		 * @return mixed
		 * @throws \Error
		 */

		public function scrapeDirectory($dirs)
		{

			foreach ($dirs as $dir)
			{

				$directory = new DirectoryOperator($this->directory->path() . $dir . "/");

				if ($directory->isEmpty())
					continue;

				return ($this->omitRoot($this->directory->path() . $dir . "/", $directory->search([".json", ".html"])));
			}

			return null;
		}

		/**
		 * @param $files
		 *
		 * @return array
		 * @throws \Error
		 */

		public function scrapeFiles($files)
		{

			$contents = [];

			foreach ($files as $realfile)
			{

				$file = new FileOperator($this->directory->path() . $realfile);

				if ($file->isJSON() == false)
					$contents[$realfile] = $file->contents;
				else
					$contents[$realfile] = $file->decodeJSON(true);
			}

			return $contents;
		}

		/**
		 * @param $dir
		 *
		 * @return bool
		 * @throws \Error
		 */

		public function hasDirs($dir)
		{

			$directory = new DirectoryOperator($this->directory->path() . $dir . "/");

			if ($directory->isEmpty())
				return false;

			if ($directory->hasDirs() == false)
				return false;

			return true;
		}

		/**
		 * @param $dir
		 *
		 * @return array|bool|null
		 * @throws \Error
		 */

		public function getDirs($dir)
		{

			$directory = new DirectoryOperator($this->directory->path() . $dir . "/");

			if ($directory->isEmpty())
				return false;

			return ($directory->getDirs());
		}

		/**
		 * @return bool
		 * @throws \Error
		 */

		public function exist()
		{

			if (empty($this->files()))
				return false;

			return true;
		}

		/**
		 * @return array
		 * @throws \Error
		 */

		public function files()
		{

			return ($this->directory->search([".json", ".html"]));
		}

		/**
		 * @return array|null
		 * @throws \Error
		 */

		public function folders()
		{

			return ($this->directory->getDirs());
		}

		/**
		 * @param $path
		 * @param $contents
		 *
		 * @return mixed
		 */

		private function omitRoot($path, $contents)
		{

			if (empty($contents))
				return null;

			foreach ($contents as $key => $value)
			{

				$contents[$key] = str_replace(SYSCRACK_ROOT, "", $value);
				$contents[$key] = str_replace($path, "", $contents[$key]);
			}

			return $contents;
		}

	}
<?php
	/**
	 * Created by PhpStorm.
	 * User: lewis
	 * Date: 03/07/2018
	 * Time: 18:28
	 */

	namespace Framework\Application\UtilitiesV2;

	use Framework\Application;

	class ScriptBuilder
	{

		/**
		 * @var null|string
		 */
		protected $path;

		/**
		 * @var array
		 */
		protected $scripts;

		/**
		 * ScriptBuilder constructor.
		 *
		 * @param null $scripts_path
		 * @param bool $auto_init
		 *
		 * @throws \Error
		 */

		public function __construct($scripts_path = null, $auto_init = true)
		{

			if ($scripts_path == null)
				$scripts_path = Application::globals()->SCRIPT_BUILDER_ROOT;

			if (file_exists(SYSCRACK_ROOT . $scripts_path) == false)
				throw new \Error("Scripts do not exist");

			$this->path = $scripts_path;

			if ($auto_init == true)
				$this->initialize();
		}

		/**
		 * @return null
		 * @throws \Error
		 */

		public function initialize()
		{

			if ($this->hasScripts() == false)
				return;

			$scripts = $this->getScripts();

			foreach ($scripts as $script)
			{

				$this->scripts[$script] = $this->readContents($script);
			}

			if ($this->hasFolders())
			{

				$folders = $this->getFolders();

				foreach ($folders as $folder)
				{

					$scripts = $this->getScripts($folder);

					foreach ($scripts as $script)
					{

						$this->scripts[$script] = $this->readContents($script);
					}
				}
			}

			return;
		}

		/**
		 * @throws \Error
		 */

		public function build()
		{

			if ( Application::globals()->SCRIPT_BUILDER_FORCED == false )
			{

				if ($this->check() == false)
					return;
			}


			if (empty($this->scripts))
				throw new \Error("Scripts have not been initialized");


			$contents = "// Automatic script combiner/builder written by Lewis Lancsater" . "\n"
				. "// Auto combined at " . Format::timestamp() . "\n"
				. "// Will recombine at " . Format::timestamp(time() + Application::globals()->SCRIPT_BUILDER_FREQUENCY);

			foreach ($this->scripts as $script => $content)
			{

				$contents = $contents . <<<EOD
\n
// $script
// ==================================
$content
EOD;
			}

			file_put_contents(SYSCRACK_ROOT . Format::asset("js", Application::globals()->SCRIPT_BUILDER_COMPILED), $contents);

			return;
		}

		/**
		 * @return bool
		 */

		public function test()
		{

			if (file_exists(SYSCRACK_ROOT . Format::asset("js", Application::globals()->SCRIPT_BUILDER_COMPILED)) == false)
				return false;

			if (empty(file_get_contents(SYSCRACK_ROOT . Format::asset("js", Application::globals()->SCRIPT_BUILDER_COMPILED))))
				return false;

			return true;
		}

		/**
		 * @return bool
		 */

		public function check()
		{

			if (file_exists(SYSCRACK_ROOT . Format::asset("js", Application::globals()->SCRIPT_BUILDER_COMPILED)))
			{

				if (filemtime(SYSCRACK_ROOT . Format::asset("js", Application::globals()->SCRIPT_BUILDER_COMPILED)) > time() - (Application::globals()->SCRIPT_BUILDER_FREQUENCY))
					return false;
			}

			return true;
		}

		/**
		 * @param $script
		 *
		 * @return null
		 * @throws \Error
		 */

		private function readContents($script)
		{

			$file = new FileOperator($this->path . $script);

			if ($file->isEmpty())
				return null;

			return ($file->contents);
		}

		/**
		 * @param null $folder
		 *
		 * @return mixed
		 * @throws \Error
		 */

		private function getScripts($folder = null)
		{

			$directory = new DirectoryOperator($this->path . $folder);

			if ($directory->isEmpty())
				throw new \Error('No contents found');

			return ($this->omitRoot($directory->search()));
		}

		/**
		 * @return mixed
		 * @throws \Error
		 */

		private function getFolders()
		{

			$directory = new DirectoryOperator($this->path);

			if ($directory->isEmpty())
				throw new \Error('No contents found');

			return ($this->omitRoot($directory->getDirs()));
		}

		/**
		 * @return mixed
		 * @throws \Error
		 */

		private function hasFolders()
		{

			$directory = new DirectoryOperator($this->path);

			if (empty($directory->getDirs()))
				return false;

			return true;
		}


		/**
		 * @param null $folder
		 *
		 * @return bool
		 * @throws \Error
		 */

		private function hasScripts($folder = null)
		{

			$directory = new DirectoryOperator($this->path . $folder);

			if (empty($directory->search()))
				return false;

			return true;
		}

		/**
		 * @param $contents
		 *
		 * @return mixed
		 */

		private function omitRoot($contents)
		{

			foreach ($contents as $key => $value)
			{

				$contents[$key] = str_replace(SYSCRACK_ROOT, "", $value);
				$contents[$key] = str_replace($this->path, "", $contents[$key]);
			}

			return $contents;
		}
	}
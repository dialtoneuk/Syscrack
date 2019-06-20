<?php
	declare(strict_types=1);
	/**
	 * Created by PhpStorm.
	 * User: lewis
	 * Date: 30/06/2018
	 * Time: 00:25
	 */

	namespace Framework\Application\UtilitiesV2;

	use Framework\Application;
	use Framework\Application\Utilities\FileSystem;

	/**
	 * Class Constructor
	 * @package Framework\Application\UtilitiesV2
	 */
	class Constructor
	{

		/**
		 * @var \stdClass
		 */

		private $objects;

		/**
		 * @var string
		 */

		private $file_path;

		/**
		 * @var string
		 */

		private $namespace;

		/**
		 * Factory constructor.
		 *
		 * @param $filepath
		 * @param $namespace
		 *
		 * @throws \Error
		 */

		public function __construct($filepath, $namespace)
		{

			Debug::message('Constructor created with file_path ' . $filepath . ' and namespace of ' . $namespace);

			$this->objects = new \stdClass();

			if( FileSystem::directoryExists( $filepath ) == false )
				throw new \Error("Directory does not exist: " . FileSystem::getFilePath( $filepath ) );

			$this->file_path = $filepath;
			$this->namespace = $namespace;
		}

		/**
		 * Destructor
		 */

		public function __destruct()
		{

			unset($this->objects);
		}

		/**
		 * @return bool
		 */

		public function isEmpty()
		{

			return (empty($this->objects));
		}

		/**
		 * @param bool $overwrite
		 *
		 * @return \stdClass
		 */

		public function createAll($overwrite = true)
		{

			Debug::message('Creating classes in directory');

			$files = $this->crawl();

			if (empty($files))
				throw new \Error('No files found');

			if ($overwrite)
				if (empty($this->objects) == false)
					$this->objects = new \stdClass();

			if ($this->check($files) == false)
				throw new \Error('Either one or more classes do not exist in namespace ' . $this->namespace . ' : ' . print_r($files));

			foreach ($files as $file)
			{

				if (strtolower($file) == Application::globals()->FRAMEWORK_BASECLASS )
					continue;

				$namespace = $this->build($file);

				Debug::message('Working with class: ' . $namespace);

				$class = new $namespace;

				$file = strtolower($file);

				if (isset($this->objects->$file))
					if ($this->objects->$file === $class)
						continue;

				$this->objects->$file = $class;
			}

			Debug::message('Finished creating classes');

			return $this->objects;
		}

		/**
		 * @param $classname
		 *
		 * @return mixed
		 * @throws \Error
		 */

		public function createSingular($classname)
		{

			if (class_exists($this->namespace . $classname) == false)
				throw new \Error('Class does not exist');

			$namespace = $this->build($classname);
			$classname = strtolower($classname);

			$this->objects->$classname = new $namespace;

			return $this->objects->$classname;
		}

		/**
		 * @param bool $array
		 *
		 * @return array|\stdClass
		 */

		public function getAll($array = false)
		{

			if (empty($this->objects))
				return null;

			if ($array)
				return Format::toArray($this->objects);

			return $this->objects;
		}

		/**
		 * @param $classname
		 *
		 * @return mixed
		 */

		public function get($classname)
		{

			$classname = strtolower($classname);

			return $this->objects->$classname;
		}

		/**
		 * @param $classname
		 *
		 * @return bool
		 */

		public function existsInDir($classname)
		{

			$files = $this->crawl();

			foreach ($files as $file)
			{

				if (strtolower($file) == strtolower($classname))
					return true;
			}

			return false;
		}

		/**
		 * @param $classname
		 */

		public function remove($classname)
		{

			unset($this->objects->$classname);
		}

		/**
		 * @param string $classname
		 *
		 * @return bool
		 */

		public function exist(string $classname)
		{

			$classname = strtolower($classname);

			return (isset($this->objects->$classname));
		}

		/**
		 * @return array
		 */

		public function crawl()
		{

			$files = FileSystem::getFilesInDirectory( $this->file_path );
			$return = [];

			foreach( $files as $file )
				$return[] = FileSystem::getFileName( $file );

			return( $return );
		}

		/**
		 * @param array $classnames
		 *
		 * @return bool
		 * @throws \Error
		 */

		private function check(array $classnames)
		{

			foreach ($classnames as $class)
			{

				if (is_string($class) == false)
					throw new \Error('Type Error');

				if (class_exists($this->namespace . $class) == false)
					return false;
			}

			return true;
		}

		/**
		 * @param $classname
		 *
		 * @return string
		 */

		private function build($classname)
		{

			return ($this->namespace . $classname);
		}

		/**
		 * @param $filename
		 *
		 * @return string
		 */

		private function trim($filename)
		{

			$exploded = explode("/", $filename);#
			$file = end($exploded);
			$filename = explode('.', $file);

			return ($filename[0]);
		}
	}
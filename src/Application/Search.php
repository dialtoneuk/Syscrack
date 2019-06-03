<?php
	/**
	 * Created by PhpStorm.
	 * User: newsy
	 * Date: 05/05/2019
	 * Time: 03:16
	 */

	namespace Framework\Application;

	use Framework\Application\Utilities\FileSystem;

	class Search
	{

		/**
		 * @var array
		 */

		protected $cache = array();

		/**
		 * @param $database
		 *
		 * @return array|mixed
		 */

		public function read($database)
		{

			if (isset($this->cache[$database]) && empty($this->cache[$database]) == false)
				return ($this->cache[$database]);

			$cache = FileSystem::read($this->path($database));

			if (empty($cache))
				return [];
			else
				return (unserialize($cache));
		}

		/**
		 * @param $database
		 * @param array $terms
		 *
		 * @return array
		 */

		public function search($database, array $terms)
		{

			$result = $this->read($database);

			if (is_array($result) == false)
				throw new \Error("Result is not an array " . $database);

			$grabbed = [];

			foreach ($result as $index => $array)
				foreach ($array as $item => $value)
					foreach ($terms as $key => $term)
						if ($item === $key)
							if (strstr($value, $term) !== false)
								$grabbed[] = $result[$item];

			return ($grabbed);
		}

		/**
		 * @param $database
		 * @param array $array
		 */

		public function add($database, array $array)
		{

			$result = $this->read($database);

			if (is_array($result) == false)
				throw new \Error("Result is not an array " . $database);

			array_push($result, $array);

			if (count( $result ) < Settings::setting("search_entry_max"))
				throw new \Error("Search entry hit max");


			$this->cache[$database] = $result;
			$this->write($database, serialize($result));
		}

		/**
		 * @param $database
		 *
		 * @return bool
		 */

		public function exists($database)
		{

			return (FileSystem::exists($this->path($database)));
		}

		/**
		 * @param null $database
		 *
		 * @return string
		 */

		public function path($database = null)
		{

			if ($database !== null)
				if (FileSystem::hasFileExtension($database) == false)
					$database = $database . Settings::setting("search_database_extension");

			return (FileSystem::separate(Settings::setting("search_database_root"), $database));
		}

		/**
		 * @param $database
		 * @param $data
		 */

		private function write($database, $data)
		{

			FileSystem::write($this->path($database), $data);
		}
	}
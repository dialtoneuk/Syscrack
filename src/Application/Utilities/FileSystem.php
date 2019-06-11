<?php
	declare(strict_types=1);

	namespace Framework\Application\Utilities;

	/**
	 * Lewis Lancaster 2016
	 *
	 * Class FileSystem
	 *
	 * @package Framework\Application\Utilities
	 */

	use Framework\Application;
	use Framework\Application\Settings;
	use Framework\Application\UtilitiesV2\Debug;


	/**
	 * Class FileSystem
	 * @package Framework\Application\Utilities
	 */
	class FileSystem
	{

		/**
		 * Reads a file
		 *
		 * @param $file
		 *
		 * @return string
		 */

		public static function read($file)
		{

			if (is_dir(self::getFilePath($file)))
			{

				throw new \Error();
			}

			if (self::hasFileExtension($file) == false)
			{

				$file = $file . Settings::setting('filesystem_default_extension');
			}

			if (file_exists(self::getFilePath($file)) == false)
			{

				throw new \Error($file . ' does not exist');
			}

			$file = file_get_contents(self::getFilePath($file));

			if (empty($file))
			{

				throw new \Error();
			}

			return $file;
		}

		/**
		 * @param mixed ...$paths
		 *
		 * @return string
		 */

		public static function separate(...$paths)
		{

			$result = "";

			foreach ($paths as $value)
				if ($value !== null && is_string( $value ))
					if (substr($value, -1) !== "\/" && self::hasFileExtension($value) == false)
						$result = $result . $value . DIRECTORY_SEPARATOR;
					else
						$result = $result . $value;
				elseif( is_array( $value ) )
					continue;
				else
					$result = $result . $value;

			return ((string)$result);
		}

		/**
		 * Reads Json
		 *
		 * @param $file
		 *
		 * @return mixed
		 */

		public static function readJson($file)
		{

			if (is_dir($file))
			{

				throw new \Error();
			}

			if (self::hasFileExtension($file) == false)
			{

				$file = $file . '.json';
			}

			if (self::exists($file) == false)
			{

				if (Debug::isEnabled())
					Debug::echo("file does not exist: " . self::getFilePath($file));

				return null;
			}

			return json_decode(self::read($file), true);
		}

		/**
		 * Writes Json
		 *
		 * @param $file
		 *
		 * @param $array
		 */

		public static function writeJson($file, $array=null )
		{

			if( is_object( $array ) == false && is_array( $array ) == false )
				throw new \Error("Invalid type given not an object or an array");

			if (is_dir(self::getFilePath($file)))
			{

				throw new \Error();
			}

			if (self::hasFileExtension($file) == false)
			{

				$file = $file . '.json';
			}

			self::write($file, json_encode($array, JSON_PRETTY_PRINT));
		}

		/**
		 * Writes a file
		 *
		 * @param $file
		 *
		 * @param $data
		 * @param bool $permission
		 * @param null $access
		 */

		public static function write($file, $data, $permission = true, $access = null)
		{

			if (is_dir(self::getFilePath($file)))
			{

				throw new \Error("file is dir: " . $file);
			}

			if (self::hasFileExtension($file) == false)
			{

				$file = $file . Settings::setting('filesystem_default_extension');
			}

			$directories = self::getDirectoriesFromPath($file);

			if (self::directoryExists($directories) == false)
			{

				throw new \Error('Directory does not exist: ' . self::getFilePath($directories));
			}

			if (is_string($data) == false)
			{

				$data = (string)$data;
			}

			file_put_contents(self::getFilePath($file), $data);

			if( $permission )
				@chown( self::getFilePath( $file ), Application::globals()->FILESYSTEM_DEFAULT_PERM );
		}

		/**
		 * Returns true if the file exists
		 *
		 * @param $file
		 *
		 * @return bool
		 */

		public static function exists($file)
		{

			if (is_dir(self::getFilePath($file)))
			{

				if( file_exists( self::getFilePath( $file ) ) == false )
					return false;

				return true;
			}

			if (self::hasFileExtension($file) == false)
			{

				$file = $file . Settings::setting('filesystem_default_extension');
			}

			if (file_exists(self::getFilePath($file)) == false)
			{

				return false;
			}

			return true;
		}

		/**
		 * Checks if a directory exists
		 *
		 * @param $path
		 *
		 * @return bool
		 */

		public static function directoryExists($path)
		{

			if (is_dir(self::getFilePath($path)) == false)
			{

				return false;
			}

			if (file_exists(self::getFilePath($path)) == false)
			{

				return false;
			}

			return true;
		}

		/**
		 * Appends a files
		 *
		 * @param $file
		 *
		 * @param $data
		 */

		public static function append($file, $data)
		{

			if (is_dir(self::getFilePath($file)))
			{

				throw new \Error();
			}

			if (self::hasFileExtension($file) == false)
			{

				$file = $file . Settings::setting('filesystem_default_extension');
			}

			if (file_exists(self::getFilePath($file)) == false)
			{

				throw new \Error();
			}

			$old_file = file_get_contents(self::getFilePath($file));

			if (empty($old_File))
			{

				$old_file = "";
			}

			file_put_contents(self::getFilePath($file), self::addNewLine($old_file, $data));
		}


		/**
		 * Gets the files in a directory
		 *
		 * @param $path
		 *
		 * @param string $suffix
		 *
		 * @return array|null
		 */

		public static function getFilesInDirectory($path, $suffix = 'php')
		{

			Debug::message("Getting all files with ." . $suffix . " in path " . $path );

			if (is_dir(self::getFilePath($path)) == false)
			{

				throw new \Error();
			}

			if (self::directoryExists($path) == false)
			{

				throw new \Error();
			}

			Debug::message("glob: " . self::getFilePath($path) . "*.{$suffix}" );

			$files = glob(self::getFilePath($path) . "*.{$suffix}");

			if (empty($files))
			{

				return null;
			}

			return $files;
		}

		/**
		 * @param $path
		 *
		 * @return array|false|null
		 */

		public static function getDirectories($path)
		{

			if (substr($path, -1) !== Settings::setting("filesystem_separator"))
				$path = $path . Settings::setting("filesystem_separator");

			if (self::directoryExists($path) == false)
			{

				throw new \Error();
			}

			$files = glob(self::getFilePath($path) . "*", GLOB_ONLYDIR);

			if (empty($files))
			{

				return null;
			}

			foreach ($files as $key => $file)
				$files[$key] = str_replace(self::getFilePath($path), "", $file);
			return $files;
		}

		/**
		 * Creates a directory
		 *
		 * @param $path
		 */

		public static function createDirectory($path)
		{

			if (substr($path, -1) == '/')
			{

				$path = substr($path, 0, -1);
			}

			mkdir(self::getFilePath($path));
		}

		/**
		 * Sets the permissions of a file
		 *
		 * @param $file
		 *
		 * @param null $access
		 */

		public static function setPermission($file, $access = null)
		{

			if ($access == null)
			{

				$access = Settings::setting('filesystem_default_access');
			}

			chmod(self::getFilePath($file), $access);
		}

		/**
		 * Deletes a file
		 *
		 * @param $file
		 */

		public static function delete($file)
		{

			if (self::hasFileExtension($file) == false)
			{

				$file = $file . Settings::setting('filesystem_default_extension');
			}

			if (file_exists(self::getFilePath($file)) == false)
			{
				Debug::message("filesystem: attempted to delete invalid file " . self::getFilePath($file) );
				return;
			}

			unlink(self::getFilePath($file));
		}

		/**
		 * Gets the file path
		 *
		 * @param $file
		 *
		 * @return string
		 */

		public static function getFilePath($file)
		{

			if( is_string( $file ) == false )
				$file = array_pop( $file );

			if( substr( $file, 0, 1 ) == DIRECTORY_SEPARATOR )
				$file = substr( $file, 1 );

			return sprintf('%s' . DIRECTORY_SEPARATOR . '%s', self::getRoot(), $file);
		}

		/**
		 * @param $dir
		 */

		static function rrmdir( $dir )
		{

			if (is_dir($dir)) {
				$objects = scandir($dir);
				foreach ($objects as $object) {
					if ($object != "." && $object != "..") {
						if (is_dir($dir."/".$object))
							self::rrmdir($dir."/".$object);
						else
							unlink($dir."/".$object);
					}
				}
				rmdir($dir);
			}
		}

		/**
		 * @param $dir
		 */

		public static function nukeDirectory( $dir )
		{

			if( substr( $dir, -1 ) !== "/" && substr( $dir, -1 ) !== "\\" )
				$dir .= DIRECTORY_SEPARATOR;

			self::rrmdir( self::getFilePath( $dir ) . DIRECTORY_SEPARATOR );
		}

		/**
		 * Gets the directories of a path
		 *
		 * @param $file
		 *
		 * @return string
		 */

		public static function getDirectoriesFromPath($file)
		{

			$path = explode(Settings::setting('filesystem_separator'), $file);

			if (empty($path))
			{

				throw new \Error();
			}

			array_pop($path);

			return implode(Settings::setting('filesystem_separator'), $path);
		}

		/**
		 * Removes the file extension
		 *
		 * @param $file
		 *
		 * @return mixed
		 */

		public static function removeFileExtension($file)
		{

			$file = explode('.', $file);

			if (empty($file))
			{

				throw new \Error();
			}

			return reset($file);
		}

		/**
		 * Gets the file name
		 *
		 * @param $file
		 *
		 * @return mixed
		 */

		public static function getFileName($file)
		{

			if (explode('.', $file) != null)
			{

				$file = self::removeFileExtension($file);
			}

			$file = explode(Settings::setting('filesystem_separator'), $file);

			if (empty($file))
			{

				throw new \Error();
			}

			return end($file);
		}

		/**
		 * Returns true if we have a file extension
		 *
		 * @param $file
		 *
		 * @return bool
		 */

		public static function hasFileExtension($file)
		{

			if (count(explode(".", $file)) === 1)
				return false;

			return true;
		}

		/**
		 * Adds a new line to the end of each string to help the handle
		 *
		 * @param $blob
		 *
		 * @param $data
		 *
		 * @return string
		 */

		private static function addNewLine($blob, $data)
		{

			return sprintf("%s\n%s", $blob, $data);
		}

		/**
		 * Gets the root of this application
		 *
		 * @return mixed
		 */

		private static function getRoot()
		{

			return SYSCRACK_ROOT;
		}

	}
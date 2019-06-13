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
		 * @param $filepath
		 *
		 * @return string
		 */

		public static function read($filepath)
		{

			self::convertSeparators( $filepath );

			if (is_dir(self::getFilePath($filepath)))
				throw new \Error();

			if (self::hasFileExtension($filepath) == false)
				$filepath = $filepath . Application::globals()->GLOB_EXTENSION;

			if (file_exists(self::getFilePath($filepath)) == false)
				throw new \Error($filepath . ' does not exist');

			$filepath = file_get_contents(self::getFilePath($filepath));

			if (empty($filepath))
				throw new \Error();

			return $filepath;
		}

		/**
		 * @param mixed ...$paths
		 *
		 * @return string
		 */

		public static function separate(...$paths): string
		{

			$result = "";

			foreach ($paths as $value)
				if ($value !== null && ( is_string( $value ) || is_int( $value ) ) )
					if (substr((string)$value, -1) !== "\/" && self::hasFileExtension((string)$value) == false)
						$result = $result . $value . DIRECTORY_SEPARATOR;
					else
						$result = $result . $value;
				elseif( is_array( $value ) )
					continue;
				else
					$result = $result . $value;

			return ($result);
		}

		/**
		 * Reads Json
		 *
		 * @param $filepath
		 *
		 * @return mixed
		 */

		public static function readJson($filepath)
		{

			self::convertSeparators( $filepath );

			if (is_dir($filepath))
				throw new \Error();

			if (self::hasFileExtension($filepath) == false)
				$filepath = $filepath . '.json';

			if (self::exists($filepath) == false)
			{

				if (Debug::isEnabled())
					Debug::echo("file does not exist: " . self::getFilePath($filepath));

				return null;
			}

			return json_decode(self::read($filepath), true);
		}

		/**
		 * Writes Json
		 *
		 * @param $filepath
		 *
		 * @param $array
		 */

		public static function writeJson($filepath, $array=null )
		{

			self::convertSeparators( $filepath );

			if( is_object( $array ) == false && is_array( $array ) == false )
				throw new \Error("Invalid type given not an object or an array");

			if (is_dir(self::getFilePath($filepath)))
				throw new \Error();

			if (self::hasFileExtension($filepath) == false)
				$filepath = $filepath . '.json';

			self::write($filepath, json_encode($array, JSON_PRETTY_PRINT));
		}

		/**
		 * Writes a file
		 *
		 * @param $filepath
		 *
		 * @param $data
		 * @param bool $permission
		 * @param int $access
		 */

		public static function write($filepath, $data, $permission = true, int $access = null)
		{

			self::convertSeparators( $filepath );

			if (is_dir(self::getFilePath($filepath)))
				throw new \Error("file is dir: " . $filepath);

			if (self::hasFileExtension($filepath) == false)
				$filepath = $filepath . "." . Application::globals()->GLOB_EXTENSION;

			$directories = self::getDirectoriesFromPath($filepath);

			if (self::directoryExists($directories) == false)
				throw new \Error('Directory does not exist: ' . self::getFilePath($directories));

			if (is_string($data) == false)
				$data = (string)$data;

			file_put_contents(self::getFilePath($filepath), $data);

			if( $permission )
				if( $access )
					@chown( self::getFilePath( $filepath ), $access);
				else
					@chown( self::getFilePath( $filepath ), Application::globals()->FILESYSTEM_DEFAULT_PERM );
		}

		/**
		 * Returns true if the file exists
		 *
		 * @param $filepath
		 *
		 * @return bool
		 */

		public static function exists($filepath)
		{

			self::convertSeparators( $filepath );

			if (is_dir(self::getFilePath($filepath)))
			{

				if( file_exists( self::getFilePath( $filepath ) ) == false )
					return false;

				return true;
			}

			if (self::hasFileExtension($filepath) == false)
				$filepath = $filepath . "." . Application::globals()->GLOB_EXTENSION;

			if (file_exists(self::getFilePath($filepath)) == false)
				return false;

			return true;
		}

		/**
		 * Checks if a directory exists
		 *
		 * @param $dir
		 *
		 * @return bool
		 */

		public static function directoryExists($dir)
		{

			self::convertSeparators( $dir);

			if (is_dir(self::getFilePath($dir)) == false)
				return false;

			if (file_exists(self::getFilePath($dir)) == false)
				return false;

			return true;
		}

		/**
		 * Appends a files
		 *
		 * @param $filepath
		 *
		 * @param $data
		 */

		public static function append($filepath, $data)
		{

			self::convertSeparators( $filepath );

			if (is_dir(self::getFilePath($filepath)))
				throw new \Error();

			if (self::hasFileExtension($filepath) == false)
				$filepath = $filepath . Application::globals()->GLOB_EXTENSION;

			if (file_exists(self::getFilePath($filepath)) == false)
				throw new \Error();

			$old_file = file_get_contents(self::getFilePath($filepath));

			if (empty($old_File))
				$old_file = "";

			file_put_contents(self::getFilePath($filepath), self::addNewLine($old_file, $data));
		}


		/**
		 * Gets the files in a directory
		 *
		 * @param $filepath
		 *
		 * @param string $suffix
		 *
		 * @return array|null
		 */

		public static function getFilesInDirectory($filepath, $suffix = 'php')
		{

			self::convertSeparators( $filepath );

			Debug::message("Getting all files with ." . $suffix . " in path " . $filepath );

			if (is_dir(self::getFilePath($filepath)) == false)
				throw new \Error();

			if (self::directoryExists($filepath) == false)
				throw new \Error();

			Debug::message("glob: " . self::getFilePath($filepath) . "*.{$suffix}" );
			$filepaths = glob(self::getFilePath($filepath) . "*.{$suffix}");

			if (empty($filepaths))
				return null;

			return $filepaths;
		}

		/**
		 * @param $dir
		 *
		 * @return array|false|null
		 */

		public static function getDirectories($dir)
		{

			self::convertSeparators( $dir );

			if (substr($dir, -1) !== DIRECTORY_SEPARATOR)
				$dir = $dir . DIRECTORY_SEPARATOR;

			if (self::directoryExists($dir) == false)
				throw new \Error();

			$filepaths = glob(self::getFilePath($dir) . "*", GLOB_ONLYDIR);

			if (empty($filepaths))
				return null;

			foreach ($filepaths as $key => $filepath)
				$filepaths[$key] = str_replace(self::getFilePath($dir), "", $filepath);
			return $filepaths;
		}

		/**
		 * Creates a directory
		 *
		 * @param $dir
		 */

		public static function createDirectory($dir)
		{

			self::convertSeparators( $dir );

			if (substr($dir, -1) == DIRECTORY_SEPARATOR)
				$dir = substr($dir, 0, -1);

			mkdir( self::getFilePath($dir) );
		}

		/**
		 * Sets the permissions of a file
		 *
		 * @param $filepath
		 *
		 * @param null $access
		 */

		public static function setPermission($filepath, $access = null)
		{

			self::convertSeparators( $filepath );

			if ($access == null)
				$access = Application::globals()->CHMOD_PERM;

			chmod(self::getFilePath($filepath), $access);
		}

		/**
		 * Deletes a file
		 *
		 * @param $filepath
		 */

		public static function delete($filepath)
		{

			self::convertSeparators( $filepath );

			if (self::hasFileExtension($filepath) == false)
				$filepath = $filepath . Application::globals()->GLOB_EXTENSION;

			if (file_exists( self::getFilePath($filepath) ) == false)
				return;

			unlink(self::getFilePath($filepath));
		}

		/**
		 * Gets the file path
		 *
		 * @param $filepath
		 *
		 * @return string
		 */

		public static function getFilePath($filepath)
		{

			self::convertSeparators( $filepath );

			if( is_string( $filepath ) == false )
				$filepath = array_pop( $filepath );

			if( substr( $filepath, 0, 1 ) == DIRECTORY_SEPARATOR )
				$filepath = substr( $filepath, 1 );
			$root = self::getRoot();

			if( substr( $root, -1 ) == "/" || substr( $root, -1 ) == "\\" )
				return self::getRoot() . $filepath;
			else
				return sprintf('%s' . DIRECTORY_SEPARATOR . '%s', self::getRoot(), $filepath);
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

			self::convertSeparators( $dir );

			if( substr( $dir, -1 ) !== "/" && substr( $dir, -1 ) !== "\\" )
				$dir .= DIRECTORY_SEPARATOR;

			self::rrmdir( self::getFilePath( $dir ) . DIRECTORY_SEPARATOR );
		}

		/**
		 * Gets the directories of a path
		 *
		 * @param $filepath
		 *
		 * @return string
		 */

		public static function getDirectoriesFromPath($filepath)
		{

			self::convertSeparators( $filepath );
			$path = explode(DIRECTORY_SEPARATOR, $filepath);

			if (empty($path))
				throw new \Error();

			array_pop($path);
			return implode(DIRECTORY_SEPARATOR, $path);
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
				throw new \Error();
			
			return reset($file);
		}

		/**
		 * Gets the file name
		 *
		 * @param $filepath
		 *
		 * @return mixed
		 */

		public static function getFileName($filepath)
		{

			self::convertSeparators( $filepath );
			
			if (explode('.', $filepath) != null)
				$filepath = self::removeFileExtension($filepath);

			$filepath = explode( DIRECTORY_SEPARATOR, $filepath);

			if (empty($filepath))
				throw new \Error();

			return end($filepath);
		}

		/**
		 * @param $filepath
		 *
		 * @return mixed
		 */

		public static function convertSeparators( &$filepath )
		{

			$filepath = str_replace("\/", DIRECTORY_SEPARATOR, $filepath );
			$filepath = str_replace("/", DIRECTORY_SEPARATOR, $filepath );
			$filepath = str_replace("\\", DIRECTORY_SEPARATOR, $filepath );
			return( $filepath );
		}

		/**
		 * Returns true if we have a file extension
		 *
		 * @param $filepath
		 *
		 * @return bool
		 */

		public static function hasFileExtension($filepath)
		{

			if (count(explode(".", $filepath)) === 1)
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

			$root = SYSCRACK_ROOT;
			self::convertSeparators( $root );
			return $root;
		}

	}
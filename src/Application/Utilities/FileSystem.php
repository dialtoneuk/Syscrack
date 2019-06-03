<?php

	namespace Framework\Application\Utilities;

	/**
	 * Lewis Lancaster 2016
	 *
	 * Class FileSystem
	 *
	 * @package Framework\Application\Utilities
	 */

	use Framework\Application\Settings;
	use Framework\Application\UtilitiesV2\Debug;
	use Framework\Exceptions\ApplicationException;

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

				throw new ApplicationException();
			}

			if (self::hasFileExtension($file) == false)
			{

				$file = $file . Settings::setting('filesystem_default_extension');
			}

			if (file_exists(self::getFilePath($file)) == false)
			{

				throw new ApplicationException($file . ' does not exist');
			}

			$file = file_get_contents(self::getFilePath($file));

			if (empty($file))
			{

				throw new ApplicationException();
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

			return ($result);
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

				throw new ApplicationException();
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
				throw new ApplicationException("Invalid type given not an object or an array");

			if (is_dir(self::getFilePath($file)))
			{

				throw new ApplicationException();
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
		 */

		public static function write($file, $data, $permission = true, $access = null)
		{

			if (is_dir(self::getFilePath($file)))
			{

				throw new ApplicationException("file is dir: " . $file);
			}

			if (self::hasFileExtension($file) == false)
			{

				$file = $file . Settings::setting('filesystem_default_extension');
			}

			$directories = self::getDirectoriesFromPath($file);

			if (self::directoryExists($directories) == false)
			{

				throw new ApplicationException('Directory does not exist: ' . self::getFilePath($directories));
			}

			if (is_string($data) == false)
			{

				$data = (string)$data;
			}

			file_put_contents(self::getFilePath($file), $data);
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

				throw new ApplicationException();
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

				throw new ApplicationException();
			}

			if (self::hasFileExtension($file) == false)
			{

				$file = $file . Settings::setting('filesystem_default_extension');
			}

			if (file_exists(self::getFilePath($file)) == false)
			{

				throw new ApplicationException();
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

			Debug::message("Getting all " . $suffix . "in path " . $path );

			if (is_dir(self::getFilePath($path)) == false)
			{

				throw new ApplicationException();
			}

			if (self::directoryExists($path) == false)
			{

				throw new ApplicationException();
			}

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

				throw new ApplicationException();
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

				throw new ApplicationException();
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

			return sprintf('%s' . Settings::setting('filesystem_separator') . '%s', self::getRoot(), $file);
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

				throw new ApplicationException();
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

				throw new ApplicationException();
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

				throw new ApplicationException();
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

		/**
		 * Stitches the pattern to the path
		 *
		 * @param $path
		 *
		 * @param $pattern
		 *
		 * @return string
		 */

		private function stitchPattern($path, $pattern)
		{

			return sprintf("%s" . Settings::setting('filesystem_separator') . "%s", $path, $pattern);
		}
	}
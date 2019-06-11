<?php
	declare(strict_types=1);

	namespace Framework\Application\UtilitiesV2;

	/**
	 * Created by PhpStorm.
	 * User: lewis
	 * Date: 29/08/2018
	 * Time: 21:46
	 */

	use Delight\FileUpload\FileUpload as Delight;
	use Delight\FileUpload\Throwable\Error;
	use Delight\FileUpload\Throwable\FileTooLargeException;
	use Delight\FileUpload\Throwable\InputNotFoundException;
	use Delight\FileUpload\Throwable\InvalidExtensionException;
	use Delight\FileUpload\Throwable\InvalidFilenameException;
	use Delight\FileUpload\Throwable\UploadCancelledException;

	/**
	 * Class FileUpload
	 * @package Framework\Application\UtilitiesV2
	 */
	class FileUpload
	{

		/**
		 * @var \Exception|null
		 */

		protected static $last_error = null;

		/**
		 * @var Delight
		 */

		protected $delight;

		/**
		 * FileUpload constructor.
		 *
		 * @param string $temporary_directory
		 * @param string $upload_key
		 */

		public function __construct($temporary_directory, $upload_key)
		{

			if (self::hasLastError())
				self::setLastError();

			try
			{
				$this->delight = new Delight();
			} catch (Error $e)
			{
			}
			$this->delight->withTargetDirectory($temporary_directory);
			$this->delight->from($upload_key);
		}

		/**
		 * @param array $extensions
		 */

		public function setAllowedExtensions(array $extensions)
		{

			$this->delight->withAllowedExtensions($extensions);
		}

		/**
		 * @param $file_name
		 *
		 * @return bool|\Delight\FileUpload\File
		 */

		public function save($file_name)
		{

			if (self::hasLastError())
				self::setLastError();

			$this->delight->withTargetFilename($file_name);
			$result = null;

			try
			{
				$result = $this->delight->save();
			} catch (Error $e)
			{
			} catch (FileTooLargeException $e)
			{
			} catch (InputNotFoundException $e)
			{
			} catch (InvalidExtensionException $e)
			{
			} catch (InvalidFilenameException $e)
			{
			} catch (UploadCancelledException $e)
			{
			}

			if (self::hasLastError())
				return (false);

			return ($result);
		}

		/**
		 * Sets the max file size in megabytes
		 *
		 * @param $file_size
		 */

		public function setMaxFileSize($file_size)
		{

			$this->delight->withMaximumSizeInMegabytes($file_size);
		}

		/**
		 * @param \Exception|null $error
		 */
		private static function setLastError(\Exception $error = null)
		{

			self::$last_error = $error;
		}

		/**
		 * @return \Exception|string
		 */

		public static function getLastError()
		{

			return (self::$last_error);
		}

		/**
		 * @return bool
		 */

		public static function hasLastError()
		{

			return (self::$last_error === null);
		}
	}
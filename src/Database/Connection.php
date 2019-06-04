<?php

	namespace Framework\Database;

	/**
	 * Lewis Lancaster 2016
	 *
	 * Class Connection
	 *
	 * @package Framework\Database
	 */

	use Framework\Application;
	use Framework\Application\Settings;
	use Framework\Application\Utilities\FileSystem;
	use Framework\Application\UtilitiesV2\OpenSSL;


	class Connection
	{

		/**
		 * @var array
		 */

		protected $connection;

		/**
		 * @var OpenSSL
		 */

		protected static $openssl;

		/**
		 * Connection constructor.
		 *
		 * @param bool $autoload
		 */

		public function __construct($autoload = true)
		{

			if( Application::globals()->DATABASE_ENCRYPTION )
				self::$openssl = new OpenSSL();

			if ($autoload)
				$this->connection = @$this->readConnectionFile();

			if (empty($this->connection))
				throw new \Error();

		}

		/**
		 * Reads the connection file of the server
		 *
		 * @param string $file
		 *
		 * @return array
		 */

		public function readConnectionFile($file = null)
		{

			if ($file == null)
				$file = Settings::setting('database_connection_file');

			if (file_exists(FileSystem::getFilePath($file)) == false)
				throw new \Error();


			$json = FileSystem::read($file);

			if (empty($json))
				throw new \Error();

			if( Application::globals()->DATABASE_ENCRYPTION == true )
			{

				$data = json_decode( $json );

				if( json_last_error() !== JSON_ERROR_NONE )
					throw new \Error("Json error in connection file");

				if( isset( $data["info"] ) == false )
					throw new \Error("Invalid connection file");

				return self::$openssl->decrypt( $data, $data["info"]["key"], $data["info"]["iv"] );
			}

			return json_decode($json, true);
		}
	}
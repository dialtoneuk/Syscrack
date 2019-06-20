<?php
	declare(strict_types=1);
	/**
	 * Created by PhpStorm.
	 * User: newsy
	 * Date: 20/06/2019
	 * Time: 00:24
	 */

	namespace Framework\Syscrack\Game;

	use Framework\Application;
	use Framework\Application\Utilities\FileSystem;
	use Framework\Application\UtilitiesV2\Container;
	use Framework\Application\UtilitiesV2\Format;

	/**
	 * Class ModLoader
	 * @package Framework\Syscrack\Game
	 */
	class ModLoader
	{

		/**
		 * @var string
		 */

		private $mods_filepath;

		/**
		 * @var array
		 */

		private static $mods;

		/**
		 * @var array
		 */

		private static $loaded_mods;

		/**
		 * @var \Error
		 */

		private static $error;

		/**
		 * ModLoader constructor.
		 *
		 * @param string $mods_filepath
		 * @param bool $read
		 */

		public function __construct( $mods_filepath = "", $read=true )
		{

			if( Application::globals()->MODS_ENABLED == false )
			{

				Application\UtilitiesV2\Debug::message("WARNING! Mods disabled");
				return false;
			}


			if( $mods_filepath == "" )
				$mods_filepath = Application::globals()->MODS_FILEPATH;

			$this->mods_filepath = $mods_filepath;

			if( $read )
				if( isset( self::$mods ) == false )
					self::$mods = $this->read();
		}

		/**
		 * Processes the mods and adds the loaded mods to the system
		 */

		public function process()
		{

			foreach( self::$mods as $mod=>$array )
				if( self::enabled( $mod ) == false )
				{

					if( Application\UtilitiesV2\Debug::isCMD() )
						Application\UtilitiesV2\Debug::message($mod . " is disabled");
				}
				else
				{

					if( isset( $array["info"]["requirements"] ) )
					{

						if( $this->requirements( $array["info"]["requirements"] ) == false )
							throw new \Error("Failed requirements: " . self::$error->getMessage() );
					}

					self::$loaded_mods[ $mod ] = [
						'classes'   => @$this->classes( $mod ),
						'includes'  => @self::$mods[ $mod ]["files"]
					];
				}
		}

		/**
		 * @param array $requirements
		 *
		 * @return bool
		 */

		public function requirements( array $requirements )
		{

			try
			{

				foreach( $requirements as $requirement=>$version )
					if( self::exists( $requirement ) == false )
						throw new \Error("Mod requires " . $requirement . " but it is not installed");
					else
					{


						if( $version !== "" )
							if( substr( $version, -1 ) == "+" )
							{
								$version = str_replace("+", "", $version );

								if( is_numeric( $version ) == false )
									throw new \Error("Version must be numeric");

								Format::cast("float", $version );

								if( isset( self::$mods[ $requirement ]["info"]["version"] ) == false )
									throw new \Error($requirement . " is broke and does not have a version");
								elseif( (float)self::$mods[ $requirement ]["info"]["version"] > $version )
									throw new \Error($requirement . " is too old. Mod requires versions higher than " . $version . " and this mod is version " . (float)self::$mods[ $requirement]["info"]["version"] );
							}
							else
							{

								if( is_numeric( $version ) == false )
									throw new \Error("Version must be numeric");

								Format::cast("float", $version );

								if( isset( self::$mods[ $requirement ]["version"] ) == false )
									throw new \Error($requirement . " is broke and does not have a version");
								elseif( self::$mods[ $requirement]["version"] == $version )
									throw new \Error($requirement . " is too old. The mod you are trying to load requires exact version " . $version . " and this mod is version " . self::$mods[ $requirement]["version"] );
							}
					}
			}
			catch ( \Error $error )
			{

				self::$error = $error;
				return( false );
			}

			return( true );
		}

		/**
		 * @param $mod
		 */

		public function classes( $mod )
		{

			$results = [];

			if( empty( self::$mods ) )
				throw new \Error("Mods not loaded");

			$files = FileSystem::strip( self::$mods[ $mod ]["files"], FileSystem::separate("mods", $mod,"src") );

			foreach( $files as $file )
			{

				$data = explode(DIRECTORY_SEPARATOR, $file );

				if( count( $data ) === 0 )
					throw new \Error("Unexpected explode output on mod: " . $mod );
				else
				{

					if( FileSystem::hasFileExtension( last( $data ) ) == false )
						throw new \Error("Unable to get PHP class: " . $file );
					else
						$classname = FileSystem::removeFileExtension( last( $data ) );

					array_pop( $data );
					$namespace = implode( "\\", $data );

					if( FileSystem::hasFileExtension( $classname ) )
						throw new \Error("Failed to remove extension this is probably due to double dotting");

					$results[$namespace][] = $classname;
				}
			}

			return( $results );
		}

		/**
		 * @param $mod
		 * @param bool $save
		 */

		public function disable( $mod, $save = true  )
		{

			self::$mods[ $mod ]["info"]["disabled"] = true;

			if( $save )
				$this->write( $mod );
		}

		/**
		 * @param $mod
		 * @param bool $save
		 */

		public function enable( $mod, $save = true  )
		{

			self::$mods[ $mod ]["info"]["disabled"] = false;

			if( $save )
				$this->write( $mod );
		}

		/**
		 * @return array
		 */

		public function read(): array
		{

			$results = [];

			if( FileSystem::directoryExists( $this->mods_filepath ) == false )
				throw new \Error("Mods filepath does not exist: " . FileSystem::getFilePath( $this->mods_filepath ) );

			$mods = FileSystem::getDirectories( $this->mods_filepath );

			foreach( $mods as $mod )
				if( FileSystem::exists( FileSystem::separate( $this->mods_filepath, $mod, "info.json") ) == false )
					throw new \Error("Folder " . $mod . " does not have a info.json file");
				else
				{

					$results[ $mod ] = [
						"info" => FileSystem::readJson( FileSystem::separate( $this->mods_filepath, $mod, "info.json" ) ),
						"files" =>  $this->getFiles( $mod )
					];

					if( json_last_error() !== JSON_ERROR_NONE )
						throw new \Error("Error occured while parsing some json: " . json_last_error_msg() );
				}

			return( $results );
		}

		/**
		 * @param $mod
		 */

		public function write( $mod )
		{

			FileSystem::writeJson( FileSystem::separate( $this->mods_filepath, $mod, "info.json" ), self::$mods[ $mod ]["info"] );
		}

		/**
		 * @return bool
		 */

		public static function loaded()
		{

			return( empty( self::$loaded_mods ) == false );
		}

		/**
		 * @param $mod
		 *
		 * @return mixed
		 */

		public static function modClasses( $mod )
		{

			return( self::$loaded_mods[ $mod ]["classes"] );
		}

		/**
		 * @param $type
		 */

		public static function factoryClasses( $type )
		{

			$results = [];

			foreach( self::$loaded_mods as $mod=>$array )
				foreach( $array["classes"] as $key=>$include )
					if( strtolower( $key ) == strtolower( $type ) )
						foreach( $include as $file )
							$results[] = ModLoader::getNamespace( $mod ) . $key . "\\" . $file;

			return( $results );
		}

		/**
		 * @param $mod
		 *
		 * @return mixed
		 */

		public static function include()
		{

			foreach( self::$loaded_mods as $mod=>$array )
				if( isset( $array["includes"] ) )
				{

					Application\UtilitiesV2\Debug::message("processing " . $mod );

					try
					{

						foreach( $array["includes"] as $key=>$file )
							if( is_array( $file ) == false || empty( $file ) )
								continue;
							else
								foreach( $file as $include )
								{

									Application\UtilitiesV2\Debug::message("including " . $key);
									include_once $include;
								}
					}
					catch ( \Error $error  )
					{

						Container::get('application')->getErrorHandler()->handleError( $error, 'mod_error');
					}
					catch ( \RuntimeException $error )
					{

						Container::get('application')->getErrorHandler()->handleError( $error, 'mod_error');
					}
					catch ( \Exception $error )
					{

						Container::get('application')->getErrorHandler()->handleError( $error, 'mod_error');
					}
					catch ( \ErrorException $error )
					{

						Container::get('application')->getErrorHandler()->handleError( $error, 'mod_error');
					}
				}
		}
		/**
		 * @param $mod
		 *
		 * @return mixed
		 */

		public static function get( $mod )
		{

			return( self::$mods[ $mod ] );
		}

		/**
		 * @param $mod
		 *
		 * @return bool
		 */

		public static function exists( $mod )
		{

			return( isset( self::$mods[ $mod ] ) );
		}

		/**
		 * @param $mod
		 *
		 * @return mixed
		 */

		public static function enabled( $mod )
		{

			if( isset( self::$mods[ $mod ]["enabled"] ) == false )
				return true;

			return( self::$mods[ $mod ]["enabled"] );
		}

		/**
		 * @return bool
		 */

		public static function hasInitialized()
		{

			return( empty( self::$mods ) == false );
		}

		/**
		 * @return array
		 */

		public static function mods()
		{

			return( self::$mods );
		}

		/**
		 * @param $mod
		 *
		 * @return string
		 */

		private function getNamespace( $mod )
		{

			return( Application::globals()->MOD_NAMESPACE . ucfirst( $mod ) . "\\" );
		}

		/**
		 * @param $mod
		 *
		 * @return array
		 */

		private function getFiles( $mod ): array
		{

			$results = [];

			if( FileSystem::directoryExists( FileSystem::separate( $this->mods_filepath, $mod, "src") ) == false )
				throw new \Error(FileSystem::separate( $this->mods_filepath, $mod, "src") . " does not exist" );

			$dirs = FileSystem::getDirectories( FileSystem::separate( $this->mods_filepath, $mod, "src") );

			foreach( $dirs as $dir )
				$results[ $dir ] = FileSystem::getFilesInDirectory( FileSystem::separate( $this->mods_filepath, $mod, "src", $dir ) );

			$results[ $mod ] = FileSystem::getFilesInDirectory(  FileSystem::separate( $this->mods_filepath, $mod, "src") );

			return( $results );
		}
	}
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
		 * @param $mod
		 */

		public function classes( $mod )
		{

			if( empty( self::$mods ) )
				throw new \Error("Mods not loaded");
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
<?php
	/**
	 * Created by PhpStorm.
	 * User: newsy
	 * Date: 20/06/2019
	 * Time: 20:30
	 */

	namespace Framework\Application\UtilitiesV2;


	use Framework\Application;
	use Framework\Application\Utilities\FileSystem;
	use Framework\Syscrack\Game\ModLoader;

	/**
	 * Class Moddable
	 * @package Framework\Application\UtilitiesV2
	 */

	class Moddable
	{

		public $base_filepath;
		public $base_namespace;
		public $mod_type;
		public static $error;
		public static $mods;
		public static $cache;

		/**
		 * Moddable constructor.
		 *
		 * @param $base_filepath
		 * @param $base_namespace
		 * @param string $mod_type
		 */

		public function __construct( $base_filepath, $base_namespace, $mod_type="views" )
		{

			if( FileSystem::directoryExists( $base_filepath ) == false )
				throw new \Error("Base filepath does not exist");

			$this->base_filepath = $base_filepath;
			$this->base_namespace = $base_namespace;
			$this->mod_type = $mod_type;
		}

		/**
		 * @param $classes
		 * @param callable $function
		 *
		 * @return bool
		 */

		public function iterate( $classes, callable $function )
		{

			try
			{

				foreach( $classes as $class )
				{

					if( Moddable::modded( $class ) )
						Moddable::set( $class, $this->mod_type );

					if( call_user_func( $function, $class ) === false )
					{

						ModLoader::clear( ModLoader::$last["type"] );
						return( false );
					}
				}

			}
			catch ( \Error $error )
			{

				self::$error = $error;
				return( false );
			}
			finally
			{

				return true;
			}
		}

		/**
		 * @param $class
		 * @param string $namespace
		 *
		 * @return bool
		 */

		public function exist( $class )
		{

			foreach( $this->classes() as $classname )
			{

				$explode = explode("\\", $classname );

				if( empty( $explode ) || count( $explode ) === 1 )
					throw new \Error("Invalid classname: " . $classname );

				if( strtolower( last( $explode ) ) == strtolower( $class ) )
					return true;
			}

			return false;
		}

		/**
		 * @param $class
		 * @param callable $function
		 *
		 * @return bool
		 */

		public function execute( $class, callable $function )
		{

			try
			{

				foreach( $this->classes() as $classname )
				{

					$explode = explode("\\", $classname );

					if( empty( $explode ) || count( $explode ) === 1 )
						throw new \Error("Invalid classname: " . $classname );

					if( strtolower( last( $explode ) ) == strtolower( $class ) )
					{

						if( Moddable::modded($classname ) )
							Moddable::set( $classname, $this->mod_type );

						if( call_user_func( $function, $classname ) == false )
						{

							ModLoader::clear( ModLoader::$last["type"] );
							return( false );
						}
						else
							break;
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
		 * @return array
		 */

		public function classes( $refresh=false )
		{

			if( empty( self::$cache ) == false && $refresh === false )
				return( self::$cache );

			$results = $this->crawl();

			if( empty( $results ) )
				return [];

			foreach( $results as $key=>$result )
				$results[ $key ] = $this->base_namespace . $result;

			$results = ( array_merge( $this->mods(), $results ) );
			self::$cache = $results;

			return( $results );
		}

		/**
		 * @param string $filepath
		 *
		 * @return array|null
		 */

		public function crawl( $filepath = "" )
		{

			if( $filepath == "" )
				$filepath = $this->base_filepath;

			$files = FileSystem::getFilesInDirectory( $filepath );

			if( empty( $files ) == false )
				foreach( $files as $key=>$value )
					$files[ $key ] = FileSystem::getFileName( $value );

			return( $files );
		}

		/**
		 * @return array
		 */

		public function mods()
		{
			if( Application::globals()->MODS_ENABLED == false )
				return [];

			if( empty( self::$mods ) == false )
				return( self::$mods );

			$mods = ( ModLoader::factoryClasses( $this->mod_type ) );
			self::$mods = $mods;

			return( $mods );
		}

		/**
		 * @param $class
		 *
		 * @return bool
		 */

		public static function modded( $class )
		{



			if( empty( self::$mods ) )
				return false;
			else
				foreach( self::$mods as $mod )
					if( strtolower( $class ) == strtolower( $mod ) )
						return true;

			return( false );
		}

		/**
		 * @param $class
		 */

		public static function set( $class, $type )
		{


			$mod = ModLoader::modFromNamespace( $class );
			ModLoader::current($type, $class, $mod);
			ModLoader::last([
				"type"  => $type,
				"mod"   => $mod,
				"class" => $class
			]);
		}
	}
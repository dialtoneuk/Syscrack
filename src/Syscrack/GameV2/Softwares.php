<?php
	/**
	 * Created by PhpStorm.
	 * User: newsy
	 * Date: 20/06/2019
	 * Time: 20:30
	 */

	namespace Framework\Syscrack\GameV2;


	use Framework\Application;
	use Framework\Application\UtilitiesV2\Moddable;

	class Softwares extends Moddable
	{

		/**
		 * @var array
		 */

		protected static $startups;

		/**
		 * @var array
		 */

		protected static $configurations;

		/**
		 * Software constructor.
		 *
		 * @param string $base_filepath
		 * @param string $base_namespace
		 * @param string $mod_type
		 */

		public function __construct( $base_filepath="", $base_namespace="", $mod_type="softwares"  )
		{

			if( $base_filepath == "" )
				$base_filepath = Application::globals()->SOFTWARE_FILEPATH;

			if( $base_namespace == "" )
				$base_namespace = Application::globals()->SOFTWARE_NAMESPACE;

			parent::__construct( $base_filepath, $base_namespace, $mod_type );
		}

		/**
		 * @param $class
		 *
		 * @return bool|void
		 */

		public function configuration( $class )
		{

			if( isset( self::$configurations[ $class ] ) )
				return( self::$configurations[ $class ] );

			$results = $this->execute( $class, 'configuration');

			if( empty( $results ) || is_array( $results ) == null )
				throw new \Error("Configuration of software invalid: " . $class );

			if( isset( self::$configurations ) == false )
				self::$configurations = [
					$class => $results
				];
			else
				self::$configurations[ $class ] = $results;

			return( $results );
		}

		/**
		 * @param $uniquename
		 *
		 * @return bool
		 */

		public function class( $uniquename )
		{

			return( $this->iterate( $this->classes(), function( $class )
			{

				if( class_exists( $class ) == false )
					throw new \Error();

				$results = $this->configuration( $class );

				if( isset( $results["uniquename"] ) )
					if( strtolower( $results["uniquename"] ) == $uniquename )
						return( $class );
			}) );
		}

		/**
		 * @param $class
		 * @param string $method
		 * @param array $parameters
		 *
		 * @return bool|void
		 */

		public function execute( $class, $method="onexecuted", array $parameters = [] )
		{

			parent::execute( $class, function( $class ) use ( $parameters, $method )
			{

				if( class_exists( $class ) == false )
					throw new \Error("Class does not exist: " . $class );

				if( isset( self::$startups[ $class ] ) == false )
					$this->startup( $class );

				$class = new $class;
				return( call_user_func_array( array( $class, $method ), $parameters ) );
			});
		}

		/**
		 * @param $class
		 */

		public function startup( $class )
		{

			parent::execute( $class, function( $class ) use ( $parameters )
			{

				if( class_exists( $class ) == false )
					throw new \Error("Class does not exist: " . $class );

				Application\UtilitiesV2\Debug::message("calling " . $class . " startup");
				forward_static_call( $class . "::setup");
				Application\UtilitiesV2\Debug::message("successful " . $class . " startup");

				if( isset( self::$startups ) == false )
					self::$startups = [];

				self::$startups[ $class ] = true;
			});
		}

		/**
		 * @param $class
		 *
		 * @return bool
		 */

		public static function hasDoneStartup( $class )
		{

			return( isset( self::$startups[ $class ] ) );
		}
	}
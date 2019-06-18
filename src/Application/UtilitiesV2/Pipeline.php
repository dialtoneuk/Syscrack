<?php
	declare(strict_types=1); //Created at 2019-06-18 04:18:47 by 16904

	namespace Framework\Application\UtilitiesV2;

	use Framework\Application;
	use Framework\Application\UtilitiesV2\Collection;
	use Framework\Application\UtilitiesV2\Convention;
	use Framework\Application\UtilitiesV2\Conventions\PipelineData;
	use Framework\Application\Utilities\FileSystem;

	/**
	 * Class Pipeline
	 * @package Framework\Application\Syscrack\
	 */
	class Pipeline extends Collection
	{

		/**
		 * @var null
		 */

		protected static $pipeline = null;

		/**
		 * @var int
		 */

		protected static $time = 0;

	    /**
	     * Pipeline constructor.
	     * @param $filepath
	     * @param $namespace
	     *
	     * @return bool
	     */

	    public function __construct( $filepath=null , $namespace=null, bool $auto_create = true, bool $cache = true )
	    {

	    	self::$time = time();

	        if( $filepath == null )
	            $filepath = Application::globals()->PIPELINE_FILEPATH;

		    if( $namespace == null )
			    $namespace = Application::globals()->PIPELINE_NAMESPACE;

		    if( $cache )
		    	$this->cache();

	        parent::__construct( $filepath , $namespace, $auto_create);
	    }

		/**
		 * @return array
		 */

	    public function process( bool $save = true ): array
	    {

	    	$results = [];

	    	if( empty( self::$pipeline ) )
	    		$pipeline = $this->cache();

	    	$pipeline = self::$pipeline;

	    	if( empty( $pipeline ) )
		    {

		    	if( Debug::isCMD() )
		    		Debug::msg("No pipeline jobs available");
		    }

		    foreach( $pipeline as $key=>$item )
		    {


			    if( $this->exist( @$item["classname"] ) == false )
				    throw new \Error("Class: " . @$item["classname"] . " does not exist");
			    else
			    {

				    /**
				     * @var $class Application\UtilitiesV2\Interfaces\Job
				     */
				    $class = $this->get( @$item["classname"] );

				    if( isset( $item["lastexecuted"] ) == false )
					    $lastexecuted = self::$time;
				    else
					    $lastexecuted = $item["lastexecuted"];

				    if( isset( $item["data"] ) == false )
					    $data = [];
				    else
					    $data = $item["data"];

				    if( isset( $data["classname"] ) == false )
					    $data["classname"] = $item["classname"];

				    $frequency = $class->frequency();

				    if( isset( $item["nextexecution"] ) == false )
				    	$nextexecution = $lastexecuted + $frequency;
				    else
					    $nextexecution = $item["nextexecution"];

				    if( ( self::$time > $nextexecution ) )
				    {

					    $results[ $item["classname"] ] = $class->execute( $data );
					    self::$pipeline[ $key ]["lastexecuted"] =  self::$time;
					    self::$pipeline[ $key ]["nextexecution"] = self::$pipeline[ $key ]["lastexecuted"] + $frequency;
				    }
				    else
					    $results[ $item["classname"] ] = false;
			    }
		    }

		    if( $save )
		    	$this->save( self::$pipeline );

		    return( $results );
	    }

		/**
		 * Caches the current pipeline data
		 */

	    public function cache()
	    {

			self::$pipeline = $this->read();
	    }

		/**
		 * @return array
		 */

	    public function read(): array
	    {

			if( FileSystem::exists( Application::globals()->PIPELINE_LOCATION . ".json" ) == false )
				return [];

			return( FileSystem::readJson( Application::globals()->PIPELINE_LOCATION ) );
	    }

		/**
		 * @param $data
		 */

	    public function save( $data )
	    {

		    FileSystem::writeJson( Application::globals()->PIPELINE_LOCATION, $data );
	    }

	    /**
	     * Returns a new convention class.
	     * Remember to change the return type to the correct convention.
	     *
	     * @return array $values
	     * @return Convention
	     */

	    public static function dataInstance( $values )
	    {

	        return( new PipelineData( $values ) );
	    }
	}
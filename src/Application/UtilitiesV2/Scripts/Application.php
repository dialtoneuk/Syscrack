<?php
	declare(strict_types=1);

	namespace Framework\Application\UtilitiesV2\Scripts;

	/**
	 * Class Global
	 *
	 * Automatically created at: 2019-06-10 23:01:39
	 * @package Framework\Application\UtilitiesV2\Scripts
	 */

	use Framework\Application\Utilities\FileSystem;
	use Framework\Application\UtilitiesV2\Container;
	use Framework\Application\UtilitiesV2\Debug;
	use Framework\Application\UtilitiesV2\FileOperator;
	use Framework\Application\UtilitiesV2\TokenReader;

	/**
	 * Class Application
	 * @package Framework\Application\UtilitiesV2\Scripts
	 */
	class Application extends Base
	{

		/**
		 * @var array
		 */

		protected static $arguments = [];

		/**
		 * @var TokenReader
		 */

		protected static $tokenreader;

		/**
		 * Application constructor.
		 */

		public function __construct()
		{

			if( isset( self::$tokenreader ) == false )
				self::$tokenreader = new TokenReader();

			parent::__construct();
		}

		/**
	     * The logic of your script goes in this function.
	     *
	     * @param $arguments
	     * @return bool
	     */

	    public function execute($arguments)
	    {

	    	if( Container::exist("application") == false )
	    		throw new \Error("Application does not exist which seems quite impossible!");

	    	self::$arguments = $arguments;

	    	switch ( $arguments["action"] )
		    {

			    case "stems":
				    if( $this->updateStems() == false )
					    return false;
				    break;
			    case "global"||"globals":
			    	if( $this->global() == false )
			    		return false;
			    	break;
			    default:
			    	throw new \Error("Unknown action: " . $arguments["action"] );
		    }

		    Debug::echo("\nActions complete! It is suggested you run refresh!\n");
		    return( true );
	    }

		/**
		 * @return bool
		 */

	    private function global()
	    {

		    $globals = \Framework\Application::globals()->all();

	    	if( isset( self::$arguments["global"] ) == false )
				Debug::echo( $globals );
	    	else
		    {

		    	if( isset( $globals[ strtoupper( self::$arguments["global"] ) ] ) == false )
		    	    if( strtolower( Debug::getLine("Global: " . self::$arguments["global"] . " does not exist, create? Y/N") ) == "y" )
		    	    	$this->create( self::$arguments["global"], null );
		    	    else
		    	    	throw new \Error("Invalid global: " . self::$arguments["global"] );
		    	elseif( isset( self::$arguments["value"] ) == false )
		    		Debug::echo( $globals[ strtoupper( self::$arguments["global"] ) ] );
		    	else
			    {

			    	$file = $this->read();

			    	if( $file=== false )
			    		throw new \Error("Unable to read global file in resources");
			    	else
				    {

				    	foreach( $file as $key=>$global )
				    		if( strpos( $global, '"' . strtoupper( self::$arguments["global"] ) . '"' ) )
						    {
						    	unset( $file[ $key ] );
						    	$this->create( self::$arguments["global"], self::$arguments["value"] , $file );
						    }
				    }
			    }
		    }

		    if( Debug::getLine("Would you like to update your IDE stems? Y/N") == strtolower( "y" ) )
		    	return( $this->updateStems() );

		    return true;
	    }

		/**
		 * Updates the stems for the IDE
		 */

	    private function updateStems()
	    {

	    	$globals = \Framework\Application::globals()->all();
	    	$string = "";

	    	foreach( $globals as $key=>$global )
		    	$string .= "	 * @property " . gettype( $global) . " " . strtoupper( $key ) . " " . $global . PHP_EOL;

			$result = self::$tokenreader->parse( self::getFileDataInstance(), TokenReader::dataInstance(["values" => [
				'globals' => $string,
				'classname'   => 'Globals',
				'namespace' => 'Framework\\Application\\UtilitiesV2'
			]]), FileSystem::separate("src","Application","UtilitiesV2","Globals.php"));

			Debug::message("Updated IDE stems");

			if( empty( $result ) == false )
				return true;

			return false;
	    }

		/**
		 * @param $global
		 * @param $value
		 * @param array $globals
		 */

	    private function create( $global, $value=null, array $globals=[]  )
	    {

	    	if( $value == null )
	    		if( isset( self::$arguments["value"] ) == false )
	    			$value = true;
	    		else
	    			$value = self::$arguments["value"];

	    	if( empty( $globals ))
		        $globals = $this->read();

		    if( $globals === false )
			    throw new \Error("Unable to read global file in resources");
		    else
		    {

		    	if( strpos( $value, ".") !== false )
		    		throw new \Error("You cannot have dots (.) in your global, sorry.");

		    	if( is_numeric( $value ) && is_string( $value ) )
				    $value = (int)$value;

			    array_pop( $globals );
			    array_push( $globals, '    ["' . addslashes( strtoupper( $global ) ) . '","' . addslashes( (string)$value ) . '"],'  );
			    array_push( $globals, PHP_EOL . '] );'  );
			    Debug::echo("Wrote '" . $global. '" with value ' . $value );
			    $this->write( $globals );
		    }
	    }

		/**
		 * @param array $data
		 */

	    private function write( array $data )
	    {

	    	FileSystem::write(FileSystem::separate("resources","includes","globals.module"),  join('', $data ) );
	    }

		/**
		 * @return array|bool
		 */

	    private function read()
	    {

	    	return( file( FileSystem::getFilePath( FileSystem::separate("resources","includes","globals.module") ) ) );
	    }

	    /**
	     * The help index can either be a string or an array containing a set of strings. You can only put strings in
	     * there.
	     *
	     * @return array
	     */

	    public function help()
	    {
	        return([
	            "arguments" => $this->requiredArguments(),
	            "help" => "Hello World"
	        ]);
	    }

	    /**
	     * Example:
	     *  [
	     *      "file"
	     *      "name"
	     *  ]
	     *
	     *  View from the console:
	     *      Global file=myfile.module name=no_space
	     *
	     * @return array|null
	     */

	    public function requiredArguments()
	    {

	        return([
	        	'action'
	        ]);
	    }

		/**
		 * @return \Framework\Application\UtilitiesV2\Conventions\FileData
		 */

	    private static function getFileDataInstance()
	    {

		    return( FileOperator::pathDataInstance( FileSystem::separate("resources","templates","template_globals.module") ) );
	    }
	}
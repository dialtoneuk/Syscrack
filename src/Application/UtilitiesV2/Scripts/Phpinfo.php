<?php
	declare(strict_types=1);

	namespace Framework\Application\UtilitiesV2\Scripts;

	/**
	 * Class Phpinfo
	 *
	 * Automatically created at: 2019-05-30 00:49:26
	 * @package Framework\Application\UtilitiesV2\Scripts
	 */

	use Framework\Application\UtilitiesV2\Debug;

	/**
	 * Class Phpinfo
	 * @package Framework\Application\UtilitiesV2\Scripts
	 */
	class Phpinfo extends Base
	{

	    /**
	     * The logic of your script goes in this function.
	     *
	     * @param $arguments
	     * @return bool
	     */

	    public function execute($arguments)
	    {

	    	if( empty( $arguments ) )
	    		$index = INFO_ALL;
	    	else
	    		$index = @$arguments["index"];

	    	ob_start();
	    	phpinfo( $index );
			$contents = ob_get_contents();
	    	ob_end_flush();

	    	Debug::echo( $contents );

	        return parent::execute($arguments); // TODO: Change the autogenerated stub
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
	     *      Phpinfo file=myfile.php name=no_space
	     *
	     * @return array|null
	     */

	    public function requiredArguments()
	    {

	        return parent::requiredArguments();
	    }
	}
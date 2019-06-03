<?php
namespace Framework\Application\UtilitiesV2\Scripts;

/**
 * Class Reinitialize
 *
 * Automatically created at: 2019-05-15 02:15:24
 */

use Framework\Application\UtilitiesV2\Container;
use Framework\Application\UtilitiesV2\Debug;

class Reinitialize extends Base
{

    /**
     * The logic of your script goes in this function.
     *
     * @param $arguments
     * @return bool
     */

    public function execute($arguments)
    {

	    if( Container::exist('instance') == false )
		    throw new \Error("Instance global class does not exist");

	    system( "php -f execute.php");

	    Debug::echo("Control back with [" . @Debug::$session . "]" );
	    Container::get('instance')->exit();

	    return parent::execute( $arguments );
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
     *      reload file=myfile.php name=no_space
     *
     * @return array|null
     */

    public function requiredArguments()
    {

        return parent::requiredArguments();
    }
}
<?php
namespace Framework\Application\UtilitiesV2\Scripts;

/**
 * Class Reload
 *
 * Automatically created at: 2019-05-15 02:15:24
 */

use Framework\Application\UtilitiesV2\Container;
use Framework\Application\Settings;
use Framework\Application\UtilitiesV2\Debug;

class Reload extends Base
{

    /**
     * The logic of your script goes in this function.
     *
     * @param $arguments
     * @return bool
     */

    public function execute($arguments)
    {

	    if( Container::exist('scripts') == false )
		    throw new \Error("Scripts global class does not exist");

	    if( Container::exist('instance') == false )
		    throw new \Error("Instance global class does not exist");

	    /**
	     * @var @scripts Scripts
	     */
	    $scripts = Container::get('scripts');

	    /**
	     * @var $instance Instance
	     */
	    $instance = Container::get('instance');

	    system( "php -f execute.php");

	    Debug::echo("INSTANCE STILL ACTIVE! Self terminating instance [" . @Debug::$session . "]" );

	    $instance->exit();

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
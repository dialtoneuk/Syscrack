<?php
namespace Framework\Application\UtilitiesV2\Conventions;

/**
 * Class InputData
 *
 * Automatically created at: 2019-05-15 01:23:42
 */

use Framework\Application\UtilitiesV2\Convention;

/**
 * Class InputData
 * @package Framework\Application\UtilitiesV2\Conventions
 *
 * @property string name
 * @property string type
 * @property string value
 * @property string placeholder
 */

class InputData extends Convention
{

    /**
     * The syntax for requirements is as follows
     *
     *  "key" => "type"
     *
     * so for instance
     *
     *  "settings"  => "array"  : Specifies that this should be an array
     *  "filename"  => "string" : Specifies that this should be a string
     *  "admin"     => "bool"   : Specifies that this should be a bool
     *  "admin"     => "int"    : Specifies that this should be a number
     *  "dynamic"   => null     : Specifies that it is a "dynamic" field, thus may or may not have a value
     * @var array
     */

    protected $requirements = [
    	"name"          => "string",
	    "type"          => null,
	    "value"         => null,
	    "placeholder"   => null,
	    "class"         => null,
	    "id"            => null
    ];
}
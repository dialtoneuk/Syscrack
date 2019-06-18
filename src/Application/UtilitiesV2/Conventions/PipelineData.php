<?php
	declare(strict_types=1); //Created at 2019-06-18 04:21:38 by 16904

	namespace Framework\Application\UtilitiesV2\Conventions;

	use Framework\Application\UtilitiesV2\Convention;

	/**
	 * Class PipelineData
	 * @package Framework\Application\UtilitiesV2\Conventions
	 *
	 * @property string class
	 * @property int frequency
	 * @property int lastexecuted
	 */
	class PipelineData extends Convention
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
	     * %var array
	     */

	    protected $requirements = [
	    	"class" => "string",
		    "frequency" => "int",
		    "lastexecuted" => "int"
	    ];
	}
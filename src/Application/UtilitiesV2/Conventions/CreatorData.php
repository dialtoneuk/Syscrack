<?php

	namespace Framework\Application\UtilitiesV2\Conventions;

	/**
	 * Class CreatorData
	 *
	 * Automatically created at: 2019-05-05 17:35:56
	 */

	use Framework\Application\UtilitiesV2\Convention;

	/**
	 * Class CreatorData
	 * @package Framework\Application\UtilitiesV2\Conventions
	 * @property int userid
	 * @property string ipaddress
	 * @property string type
	 * @property array hardware
	 * @property array software;
	 * @property array custom  ;
	 */
	class CreatorData extends Convention
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
			"userid" => "int",
			"ipaddress" => "string",
			"type" => "string",
			"hardware" => "array",
			"software" => "array",
		];
	}
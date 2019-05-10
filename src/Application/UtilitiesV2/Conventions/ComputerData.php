<?php

	namespace Framework\Application\UtilitiesV2\Conventions;

	/**
	 * Class Meta
	 *
	 * Automatically created at: 2019-05-05 14:39:26
	 */

	use Framework\Application\UtilitiesV2\Convention;

	/**
	 * Class ComputerData
	 * @package Framework\Application\UtilitiesV2\Conventions
	 * @property string name
	 * @property string type
	 * @property array info
	 * @property array software
	 * @property array hardware
	 * @property array custom
	 */
	class ComputerData extends Convention
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
		 *  "dynamic"   => "dynamic": Specifies that it is a "dynamic" field, thus may or may not have a value
		 * @var array
		 */

		protected $requirements = [
			"name" => "string",
			"type" => "string",
			"info" => "array",
			"software" => "array",
			"hardware" => "array",
			"custom" => "array"
		];
	}
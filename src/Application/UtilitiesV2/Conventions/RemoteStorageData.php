<?php

	namespace Framework\Application\UtilitiesV2\Conventions;


	/**
	 * Created by PhpStorm.
	 * User: lewis
	 * Date: 31/08/2018
	 * Time: 14:38
	 */

	use Framework\Application\UtilitiesV2\Convention;

	/**
	 * Class StorageData
	 * @package Framework\Application\UtilitiesV2\Conventions
	 * @property array settings   ;
	 * @property array credentials;
	 */
	class RemoteStorageData extends Convention
	{

		protected $requirements = [
			"settings" => "array",
			"credentials" => "array"
		];
	}
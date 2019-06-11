<?php
	declare(strict_types=1);

	namespace Framework\Application\UtilitiesV2\Conventions;


	/**
	 * Created by PhpStorm.
	 * User: lewis
	 * Date: 31/08/2018
	 * Time: 01:16
	 */

	use Framework\Application\UtilitiesV2\Convention;

	/**
	 * Class UploadData
	 * @package Framework\Application\UtilitiesV2\Conventions
	 *
	 * @property string filename
	 * @property array settings
	 * @property array form
	 */
	class UploadData extends Convention
	{

		/**
		 * @var array
		 */

		protected $requirements = [
			"filename" => "string",
			"settings" => "array",
			"form" => "array"
		];
	}
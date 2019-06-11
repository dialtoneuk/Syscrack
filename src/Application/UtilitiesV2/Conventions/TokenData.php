<?php
	declare(strict_types=1);

	namespace Framework\Application\UtilitiesV2\Conventions;

	/**
	 * Created by PhpStorm.
	 * User: lewis
	 * Date: 31/08/2018
	 * Time: 21:44
	 */

	use Framework\Application\UtilitiesV2\Convention;
	use Framework\Application\UtilitiesV2\Format;

	/**
	 * Class TokenData
	 * @package Framework\Application\UtilitiesV2\Conventions
	 * @property array values
	 */
	class TokenData extends Convention
	{

		/**
		 * @var array
		 */

		protected $requirements = [
			"values" => "array"
		];

		/**
		 * TokenData constructor.
		 *
		 * @param array $array
		 */

		public function __construct(array $array)
		{

			if (isset($array["values"]["time"]) == false)
				$array["values"]["time"] = Format::timestamp(time());

			if( isset( $array["values"]["pid"] ) == false )
				$array["values"]["pid"] = getmypid();

			parent::__construct($array);
		}
	}
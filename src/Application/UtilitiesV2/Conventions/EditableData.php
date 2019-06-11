<?php
	declare(strict_types=1);

	namespace Framework\Application\UtilitiesV2\Conventions;

	/**
	 * Created by PhpStorm.
	 * User: lewis
	 * Date: 31/08/2018
	 * Time: 14:35
	 */

	use Framework\Application\UtilitiesV2\Convention;

	/**
	 * Class EditableData
	 * @package Framework\Application\UtilitiesV2\Conventions
	 */
	class EditableData extends Convention
	{

		/**
		 * @param $index
		 * @param $value
		 */

		public function __set( $index, $value )
		{

			$this->array[ $index ] = $value;
		}
	}
<?php

	namespace Framework\Application\UtilitiesV2\Conventions;

	/**
	 * Created by PhpStorm.
	 * User: lewis
	 * Date: 31/08/2018
	 * Time: 14:35
	 */

	use Framework\Application\UtilitiesV2\Convention;

	class AmbiguousData extends Convention
	{

		//Blank requirements
		protected $requirements = [];


		/**
		 * @param $index
		 * @param $value
		 */

		public function __set( $index, $value )
		{

			$this->array[ $index ] = $value;
		}
	}
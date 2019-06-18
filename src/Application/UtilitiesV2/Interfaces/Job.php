<?php
	/**
	 * Created by PhpStorm.
	 * User: newsy
	 * Date: 18/06/2019
	 * Time: 03:28
	 */

	namespace Framework\Application\UtilitiesV2\Interfaces;


	interface Job
	{

		/**
		 * @param array $data
		 *
		 * @return bool
		 */

		public function execute( array $data ): bool;

		/**
		 * @return int
		 */

		public function frequency(): int;
	}
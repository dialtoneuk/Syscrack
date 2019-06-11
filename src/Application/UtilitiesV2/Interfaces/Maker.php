<?php
	declare(strict_types=1);
	/**
	 * Created by PhpStorm.
	 * User: lewis
	 * Date: 31/08/2018
	 * Time: 21:43
	 */

	namespace Framework\Application\UtilitiesV2\Interfaces;


	use Framework\Application\UtilitiesV2\Conventions\FileData;
	use Framework\Application\UtilitiesV2\Conventions\TokenData;

	/**
	 * Interface Maker
	 * @package Framework\Application\UtilitiesV2\Interfaces
	 */
	interface Maker
	{

		/**
		 * @param FileData|null $template
		 */
		public function before(FileData $template = null): void;

		/**
		 * @return array
		 */
		public function requiredTokens(): array;

		/**
		 * @param TokenData $values
		 * @param $path
		 *
		 * @return FileData
		 */
		public function make(TokenData $values, $path): FileData;

		public function namespace(): string;

		/**
		 * @return string
		 */
		public function filepath(): string;
	}
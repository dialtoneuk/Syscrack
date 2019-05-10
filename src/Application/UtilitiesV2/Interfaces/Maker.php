<?php
	/**
	 * Created by PhpStorm.
	 * User: lewis
	 * Date: 31/08/2018
	 * Time: 21:43
	 */

	namespace Framework\Application\UtilitiesV2\Interfaces;


	use Framework\Application\UtilitiesV2\Conventions\FileData;
	use Framework\Application\UtilitiesV2\Conventions\TokenData;

	interface Maker
	{

		public function before(FileData $template = null): void;

		public function requiredTokens(): array;

		public function make(TokenData $values, $path): FileData;
	}
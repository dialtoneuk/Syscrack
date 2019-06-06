<?php

	namespace Framework\Application\UtilitiesV2\Makers;

	/**
	 * Class Computer
	 *
	 * Automatically created at: 2019-06-06 22:50:15
	 * @package Framework\Application\UtilitiesV2\Makers
	 */

	use Framework\Application\UtilitiesV2\Conventions\FileData;
	use Framework\Application\UtilitiesV2\FileOperator;

	class Computer extends Base
	{

		/**
		 * @param FileData|null $template
		 */

		public function before(FileData $template = null): void
		{

			if ($template == null)
				$template = FileOperator::pathDataInstance("resources/templates/template_" . strtolower("Computer" ) . ".module");

			parent::before($template);
		}

		/**
		 * @return string
		 */

		public function filepath(): string
		{

			return( parent::filepath() );
		}

		/**
		 * @return string
		 */

		public function namespace(): string
		{

			return( parent::namespace() );
		}
	}
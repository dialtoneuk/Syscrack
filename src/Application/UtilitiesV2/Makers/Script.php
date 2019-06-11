<?php
	declare(strict_types=1);

	namespace Framework\Application\UtilitiesV2\Makers;

	use Framework\Application\UtilitiesV2\Conventions\FileData;
	use Framework\Application\UtilitiesV2\FileOperator;
	use Framework\Application\UtilitiesV2\Format;
	use Framework\Application;

	/**
	 * Class Script
	 * @package Framework\Application\UtilitiesV2\Makers
	 */
	class Script extends Base
	{

		/**
		 * @param FileData|null $template
		 */

		public function before(FileData $template = null): void
		{

			if ($template == null)
				$template = FileOperator::pathDataInstance("resources/templates/template_script.module");

			parent::before($template);
		}

		/**
		 * @return string
		 */

		public function filepath(): string
		{

			return( Application::globals()->SCRIPTS_ROOT );
		}

		/**
		 * @return string
		 */

		public function namespace(): string
		{
			return( Format::rc( Application::globals()->SCRIPTS_ROOT ) );
		}
	}
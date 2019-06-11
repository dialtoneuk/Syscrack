<?php
	declare(strict_types=1);

	namespace Framework\Application\UtilitiesV2\Makers;

	use Framework\Application\UtilitiesV2\Conventions\FileData;
	use Framework\Application\UtilitiesV2\FileOperator;
	use Framework\Application\UtilitiesV2\Format;
	use Framework\Application;

	/**
	 * Class AutoExec
	 * @package Framework\Application\UtilitiesV2\Makers
	 */
	class AutoExec extends Base
	{

		/**
		 * @param FileData|null $template
		 */

		public function before(FileData $template = null): void
		{

			if ($template == null)
				$template = FileOperator::pathDataInstance("resources/templates/template_autoexec.module");

			parent::before($template);
		}

		/**
		 * @return string
		 */

		public function namespace(): string
		{
			return(  Application::globals()->SYSCRACK_NAMESPACE_ROOT . "Application\\UtilitiesV2\\AutoExecs");
		}

		/**
		 * @return string
		 */

		public function filepath(): string
		{
			return( Format::rc( Application::globals()->AUTOEXEC_NAMESPACE ) );
		}
	}
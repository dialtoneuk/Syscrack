<?php

	namespace Framework\Application\UtilitiesV2\Makers;

	/**
	 * Created by PhpStorm.
	 * User: lewis
	 * Date: 31/08/2018
	 * Time: 22:11
	 */

	use Framework\Application\UtilitiesV2\Conventions\FileData;
	use Framework\Application\UtilitiesV2\FileOperator;
	use Framework\Application;

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
			return("src/Application/UtilitiesV2/AutoExecs/");
		}
	}
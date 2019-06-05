<?php

	namespace Framework\Application\UtilitiesV2\Makers;

	/**
	 * Created by PhpStorm.
	 * User: lewis
	 * Date: 31/08/2018
	 * Time: 22:11
	 */

	use Framework\Application;
	use Framework\Application\Settings;
	use Framework\Application\UtilitiesV2\Conventions\FileData;
	use Framework\Application\UtilitiesV2\FileOperator;

	class Page extends Base
	{

		/**
		 * @param FileData|null $template
		 */

		public function before(FileData $template = null): void
		{

			if ($template == null)
				$template = FileOperator::pathDataInstance("resources/templates/template_page.module");

			parent::before($template);
		}

		/**
		 * @return string
		 */

		public function filepath(): string
		{
			return( Settings::setting('controller_page_folder') );
		}

		/**
		 * @return string
		 */

		public function namespace(): string
		{
			return( Application::globals()->SYSCRACK_NAMESPACE_ROOT . "Views\\Pages" );
		}
	}
<?php

	namespace Framework\Syscrack\Game\Softwares;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class Upload
	 *
	 * @package Framework\Syscrack\Game\Softwares
	 */

	use Framework\Syscrack\Game\BaseClasses\BaseSoftware;
	use Framework\Syscrack\Game\Tool;

	class Upload extends BaseSoftware
	{

		/**
		 * The configuration of this Structure
		 *
		 * @return array
		 */

		public function configuration()
		{

			return array(
				'uniquename' => 'upload',
				'extension' => '.up',
				'type' => 'upload',
				'installable' => true,
				'executable' => true,
				'localexecuteonly' => true,
			);
		}

		/**
		 * @param null $userid
		 * @param null $sofwareid
		 * @param null $computerid
		 *
		 * @return Tool
		 */

		public function tool($userid = null, $sofwareid = null, $computerid = null): Tool
		{

			$tool = new Tool("Upload", "warning");
			$tool->setAction('upload');
			$tool->addInput('softwareid', 'localsoftwares');
			$tool->isExternal();
			$tool->isConnected();
			$tool->icon = "arrow-up";

			return ($tool);
		}
	}
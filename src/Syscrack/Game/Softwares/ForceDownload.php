<?php

	namespace Framework\Syscrack\Game\Softwares;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class ForceDownload
	 *
	 * @package Framework\Syscrack\Game\Softwares
	 */

	use Framework\Syscrack\Game\Bases\BaseSoftware;
	use Framework\Syscrack\Game\Tool;

	class ForceDownload extends BaseSoftware
	{

		/**
		 * The configuration of this Structure
		 *
		 * @return array
		 */

		public function configuration()
		{

			return array(
				'uniquename' => 'forceupload',
				'extension' => '.admin',
				'type' => 'admin',
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

			$tool = new Tool("Force Download", "danger");
			$tool->admin();
			$tool->setAction('forcedownload');
			$tool->addInput("softwareid", "softwares");
			$tool->panel("danger");
			$tool->icon = "arrow-up";

			return ($tool);
		}
	}
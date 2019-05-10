<?php

	namespace Framework\Syscrack\Game\Softwares;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class Kernel
	 *
	 * @package Framework\Syscrack\Game\Softwares
	 */

	use Framework\Syscrack\Game\BaseClasses\BaseSoftware;
	use Framework\Syscrack\Game\Tool;

	class Kernel extends BaseSoftware
	{

		/**
		 * The configuration of this Structure
		 *
		 * @return array
		 */

		public function configuration()
		{

			return array(
				'uniquename' => 'kernel',
				'extension' => '.exe',
				'type' => 'kernel',
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

			$tool = new Tool("Execute", "success");
			$tool->setAction("execute");
			$tool->hasSoftwareInstalled('kernel');
			$tool->softwareAction();
			$tool->localAllowed();

			return ($tool);
		}
	}
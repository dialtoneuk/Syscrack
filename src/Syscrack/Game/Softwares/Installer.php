<?php
	declare(strict_types=1);

	namespace Framework\Syscrack\Game\Softwares;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class Installer
	 *
	 * @package Framework\Syscrack\Game\Softwares
	 */

	use Framework\Syscrack\Game\Bases\BaseSoftware;
	use Framework\Syscrack\Game\Tool;

	/**
	 * Class Installer
	 * @package Framework\Syscrack\Game\Softwares
	 */
	class Installer extends BaseSoftware
	{

		/**
		 * The configuration of this Structure
		 *
		 * @return array
		 */

		public function configuration()
		{

			return [
				'uniquename' => 'installer',
				'extension' => '.msi',
				'type' => 'installer',
				'installable' => true,
				'executable' => true,
				'localexecuteonly' => true,
			];
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

			$tool = new Tool("Install", "success");
			$tool->setAction("install");
			$tool->softwareAction();
			$tool->localAllowed();

			return ($tool);
		}
	}
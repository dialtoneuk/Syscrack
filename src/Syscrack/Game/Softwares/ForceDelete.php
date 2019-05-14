<?php

	namespace Framework\Syscrack\Game\Softwares;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class ForceDelete
	 *
	 * @package Framework\Syscrack\Game\Softwares
	 */

	use Framework\Syscrack\Game\Bases\BaseSoftware;
	use Framework\Syscrack\Game\Tool;

	class ForceDelete extends BaseSoftware
	{

		/**
		 * The configuration of this Structure
		 *
		 * @return array
		 */

		public function configuration()
		{

			return array(
				'uniquename' => 'forcedelete',
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

			$tool = new Tool("Force Delete", "danger");
			$tool->admin();
			$tool->addInput("softwareid", "softwares");
			$tool->setAction('forcedelete');
			$tool->panel("danger");
			$tool->icon = "fire";

			return ($tool);
		}
	}
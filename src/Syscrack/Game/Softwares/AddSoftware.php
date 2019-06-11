<?php
	declare(strict_types=1);

	namespace Framework\Syscrack\Game\Softwares;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class AddSoftware
	 *
	 * @package Framework\Syscrack\Game\Softwares
	 */

	use Framework\Syscrack\Game\Bases\BaseSoftware;
	use Framework\Syscrack\Game\Tool;

	/**
	 * Class AddSoftware
	 * @package Framework\Syscrack\Game\Softwares
	 */
	class AddSoftware extends BaseSoftware
	{

		/**
		 * The configuration of this Structure
		 *
		 * @return array
		 */

		public function configuration()
		{

			return [
				'uniquename' => 'addsoftware',
				'extension' => '.admin',
				'type' => 'admin',
				'installable' => true,
				'executable' => true,
				'localexecuteonly' => true
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

			$tool = new Tool("Add Software", "danger");
			$tool->admin();
			$tool->setAction('addsoftware');
			$tool->addInput("softwareid", "softwaretypes");
			$tool->panel("danger");
			$tool->icon = "star";

			return ($tool);
		}
	}
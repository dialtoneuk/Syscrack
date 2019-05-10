<?php

	namespace Framework\Syscrack\Game\Softwares;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class Shop
	 *
	 * @package Framework\Syscrack\Game\Softwares
	 */

	use Framework\Syscrack\Game\BaseClasses\BaseSoftware;
	use Framework\Syscrack\Game\Tool;

	class Shop extends BaseSoftware
	{

		/**
		 * The configuration of this Structure
		 *
		 * @return array
		 */

		public function configuration()
		{

			return array(
				'uniquename' => 'shop',
				'extension' => '.amz',
				'type' => 'shop',
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

			$tool = new Tool("Open Market", "info");
			$tool->setAction('market');
			$tool->isExternal();
			$tool->isComputerType('market');
			$tool->icon = "gbp";

			return ($tool);
		}
	}
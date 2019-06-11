<?php
	declare(strict_types=1);

	namespace Framework\Syscrack\Game\Softwares;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class Research
	 *
	 * @package Framework\Syscrack\Game\Softwares
	 */

	use Framework\Syscrack\Game\Bases\BaseSoftware;
	use Framework\Syscrack\Game\Tool;

	/**
	 * Class Research
	 * @package Framework\Syscrack\Game\Softwares
	 */
	class Research extends BaseSoftware
	{

		/**
		 * The configuration of this Structure
		 *
		 * @return array
		 */

		public function configuration()
		{

			return [
				'uniquename' => 'research',
				'extension' => '.rsch',
				'type' => 'research',
				'viewable' => false,
				'removable' => true,
				'installable' => true,
				'executable' => true,
				'localexecuteonly' => true,
				'keepdata' => false,
				'icon' => 'glyphicon-apple'
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

			$tool = new Tool("Research Centre", "success");
			$tool->hasSoftwareInstalled('research');
			$tool->setAction('researchcentre');
			$tool->localAllowed();
			$tool->panel("success");
			$tool->icon = 'apple';

			return ($tool);
		}
	}
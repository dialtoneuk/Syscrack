<?php
	declare(strict_types=1);

	namespace Framework\Syscrack\Game\Softwares;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class Cracker
	 *
	 * @package Framework\Syscrack\Game\Softwares
	 */

	use Framework\Syscrack\Game\Bases\BaseSoftware;
	use Framework\Syscrack\Game\Tool;

	/**
	 * Class Cracker
	 * @package Framework\Syscrack\Game\Softwares
	 */
	class Cracker extends BaseSoftware
	{

		/**
		 * The configuration of this Structure
		 *
		 * @return array
		 */

		public function configuration()
		{

			return [
				'uniquename' => 'cracker',
				'extension' => '.crc',
				'type' => 'cracker',
				'icon' => 'glyphicon-copyright-mark',
				'installable' => true,
				'executable' => true,
				'removable' => true
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

			$tool = new Tool('Hack');
			$tool->setAction('hack');
			$tool->preferenceType('cracker');
			$tool->hacked();
			$tool->hide();

			return ($tool);
		}
	}
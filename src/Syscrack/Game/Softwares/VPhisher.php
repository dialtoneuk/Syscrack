<?php

	namespace Framework\Syscrack\Game\Softwares;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class VPhisher
	 *
	 * @package Framework\Syscrack\Game\Softwares
	 */

	use Framework\Syscrack\Game\BaseClasses\BaseSoftware;


	class VPhisher extends BaseSoftware
	{

		/**
		 * The configuration of this Structure
		 *
		 * @return array
		 */

		public function configuration()
		{

			return array(
				'uniquename' => 'vphisher',
				'extension' => '.vphish',
				'type' => 'virus',
				'installable' => true,
				'uninstallable' => false,
				'executable' => false,
				'removable' => false
			);
		}
	}
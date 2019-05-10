<?php

	namespace Framework\Syscrack\Game\Softwares;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class VWarez
	 *
	 * @package Framework\Syscrack\Game\Softwares
	 */

	use Framework\Syscrack\Game\BaseClasses\BaseSoftware;


	class VWarez extends BaseSoftware
	{

		/**
		 * The configuration of this Structure
		 *
		 * @return array
		 */

		public function configuration()
		{

			return array(
				'uniquename' => 'vwarez',
				'extension' => '.vwarez',
				'type' => 'virus',
				'installable' => true,
				'uninstallable' => true,
				'executable' => false,
				'removable' => false
			);
		}
	}
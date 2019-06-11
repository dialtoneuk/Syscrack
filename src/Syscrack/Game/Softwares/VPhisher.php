<?php
	declare(strict_types=1);

	namespace Framework\Syscrack\Game\Softwares;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class VPhisher
	 *
	 * @package Framework\Syscrack\Game\Softwares
	 */

	use Framework\Syscrack\Game\Bases\BaseSoftware;


	/**
	 * Class VPhisher
	 * @package Framework\Syscrack\Game\Softwares
	 */
	class VPhisher extends BaseSoftware
	{

		/**
		 * The configuration of this Structure
		 *
		 * @return array
		 */

		public function configuration()
		{

			return [
				'uniquename' => 'vphisher',
				'extension' => '.vphish',
				'type' => 'virus',
				'installable' => true,
				'uninstallable' => false,
				'executable' => false,
				'removable' => false
			];
		}
	}
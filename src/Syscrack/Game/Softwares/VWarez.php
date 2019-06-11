<?php
	declare(strict_types=1);

	namespace Framework\Syscrack\Game\Softwares;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class VWarez
	 *
	 * @package Framework\Syscrack\Game\Softwares
	 */

	use Framework\Syscrack\Game\Bases\BaseSoftware;


	/**
	 * Class VWarez
	 * @package Framework\Syscrack\Game\Softwares
	 */
	class VWarez extends BaseSoftware
	{

		/**
		 * The configuration of this Structure
		 *
		 * @return array
		 */

		public function configuration()
		{

			return [
				'uniquename' => 'vwarez',
				'extension' => '.vwarez',
				'type' => 'virus',
				'installable' => true,
				'uninstallable' => true,
				'executable' => false,
				'removable' => false
			];
		}
	}
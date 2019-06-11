<?php
	declare(strict_types=1);

	namespace Framework\Syscrack\Game\Softwares;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class VDDoS
	 *
	 * @package Framework\Syscrack\Game\Softwares
	 */

	use Framework\Syscrack\Game\Bases\BaseSoftware;


	/**
	 * Class VDDoS
	 * @package Framework\Syscrack\Game\Softwares
	 */
	class VDDoS extends BaseSoftware
	{

		/**
		 * The configuration of this Structure
		 *
		 * @return array
		 */

		public function configuration()
		{

			return [
				'uniquename' => 'vddos',
				'extension' => '.vddos',
				'type' => 'ddos',
				'installable' => true,
				'uninstallable' => true,
				'executable' => false,
				'removable' => false
			];
		}
	}
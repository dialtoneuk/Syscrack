<?php
	declare(strict_types=1);

	namespace Framework\Syscrack\Game\Softwares;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class Watchdog
	 *
	 * @package Framework\Syscrack\Game\Softwares
	 */

	use Framework\Syscrack\Game\Bases\BaseSoftware;

	/**
	 * Class Watchdog
	 * @package Framework\Syscrack\Game\Softwares
	 */
	class Watchdog extends BaseSoftware
	{

		/**
		 * The configuration of this Structure
		 *
		 * @return array
		 */

		public function configuration()
		{

			return [
				'uniquename' => 'watchdog',
				'extension' => '.wch',
				'type' => 'watchdog',
				'installable' => true,
				'executable' => true,
				'logins' => true
			];
		}
	}
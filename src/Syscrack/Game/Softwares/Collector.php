<?php
	declare(strict_types=1);

	namespace Framework\Syscrack\Game\Softwares;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class Honeypot
	 *
	 * @package Framework\Syscrack\Game\Softwares
	 */

	use Framework\Syscrack\Game\Bases\BaseSoftware;

	/**
	 * Class Collector
	 * @package Framework\Syscrack\Game\Softwares
	 */
	class Collector extends BaseSoftware
	{

		/**
		 * The configuration of this Structure
		 *
		 * @return array
		 */

		public function configuration()
		{

			return [
				'uniquename' => 'collector',
				'extension' => '.col',
				'type' => 'collector',
				'installable' => true,
				'executable' => true,
				'localexecuteonly' => true,
			];
		}
	}
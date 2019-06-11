<?php
	declare(strict_types=1);

	namespace Framework\Syscrack\Game\Softwares;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class Breaker
	 *
	 * @package Framework\Syscrack\Game\Softwares
	 */

	use Framework\Syscrack\Game\Bases\BaseSoftware;

	/**
	 * Class Breaker
	 * @package Framework\Syscrack\Game\Softwares
	 */
	class Breaker extends BaseSoftware
	{

		/**
		 * The configuration of this Structure
		 *
		 * @return array
		 */

		public function configuration()
		{

			return [
				'uniquename' => 'breaker',
				'extension' => '.brk',
				'type' => 'breaker',
				'installable' => false,
				'executable' => false
			];
		}
	}
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
	 * Class Honeypot
	 * @package Framework\Syscrack\Game\Softwares
	 */
	class Honeypot extends BaseSoftware
	{

		/**
		 * The configuration of this Structure
		 *
		 * @return array
		 */

		public function configuration()
		{

			return [
				'uniquename' => 'honeypot',
				'extension' => '.hpot',
				'type' => 'honeypot',
				'installable' => false
			];
		}
	}
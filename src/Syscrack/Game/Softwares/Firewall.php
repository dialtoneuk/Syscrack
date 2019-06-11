<?php
	declare(strict_types=1);

	namespace Framework\Syscrack\Game\Softwares;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class Firewall
	 *
	 * @package Framework\Syscrack\Game\Softwares
	 */

	use Framework\Syscrack\Game\Bases\BaseSoftware;

	/**
	 * Class Firewall
	 * @package Framework\Syscrack\Game\Softwares
	 */
	class Firewall extends BaseSoftware
	{

		/**
		 * The configuration of this Structure
		 *
		 * @return array
		 */

		public function configuration()
		{

			return [
				'uniquename' => 'firewall',
				'extension' => '.fwall',
				'type' => 'firewall',
				'installable' => true,
				'executable' => false
			];
		}
	}
<?php
	declare(strict_types=1);

	namespace Framework\Syscrack\Game\Softwares;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class Nmap
	 *
	 * @package Framework\Syscrack\Game\Softwares
	 */

	use Framework\Syscrack\Game\Bases\BaseSoftware;


	/**
	 * Class Nmap
	 * @package Framework\Syscrack\Game\Softwares
	 */
	class Nmap extends BaseSoftware
	{

		/**
		 * The configuration of this Structure
		 *
		 * @return array
		 */

		public function configuration()
		{

			return [
				'uniquename' => 'nmap',
				'extension' => '.nmap',
				'type' => 'tool',
				'installable' => true
			];
		}
	}
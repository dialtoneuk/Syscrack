<?php

	namespace Framework\Syscrack\Game\Softwares;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class Nmap
	 *
	 * @package Framework\Syscrack\Game\Softwares
	 */

	use Framework\Syscrack\Game\BaseClasses\BaseSoftware;


	class Nmap extends BaseSoftware
	{

		/**
		 * The configuration of this Structure
		 *
		 * @return array
		 */

		public function configuration()
		{

			return array(
				'uniquename' => 'nmap',
				'extension' => '.nmap',
				'type' => 'tool',
				'installable' => true
			);
		}
	}
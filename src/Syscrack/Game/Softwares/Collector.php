<?php

	namespace Framework\Syscrack\Game\Softwares;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class Honeypot
	 *
	 * @package Framework\Syscrack\Game\Softwares
	 */

	use Framework\Syscrack\Game\BaseClasses\BaseSoftware;

	class Collector extends BaseSoftware
	{

		/**
		 * The configuration of this Structure
		 *
		 * @return array
		 */

		public function configuration()
		{

			return array(
				'uniquename' => 'collector',
				'extension' => '.col',
				'type' => 'collector',
				'installable' => true,
				'executable' => true,
				'localexecuteonly' => true,
			);
		}
	}
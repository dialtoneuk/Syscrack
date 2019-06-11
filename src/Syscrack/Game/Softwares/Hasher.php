<?php
	declare(strict_types=1);

	namespace Framework\Syscrack\Game\Softwares;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class Hasher
	 *
	 * @package Framework\Syscrack\Game\Softwares
	 */

	use Framework\Syscrack\Game\Bases\BaseSoftware;


	/**
	 * Class Hasher
	 * @package Framework\Syscrack\Game\Softwares
	 */
	class Hasher extends BaseSoftware
	{

		/**
		 * The configuration of this Structure
		 *
		 * @return array
		 */

		public function configuration()
		{

			return [
				'uniquename' => 'hasher',
				'extension' => '.hash',
				'type' => 'hasher',
				'icon' => 'glyphicon-lock',
				'installable' => true,
				'executable' => false,
			];
		}
	}
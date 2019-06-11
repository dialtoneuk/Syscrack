<?php
	declare(strict_types=1);

	namespace Framework\Syscrack\Game\Softwares;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class Text
	 *
	 * @package Framework\Syscrack\Game\Softwares
	 */

	use Framework\Syscrack\Game\Bases\BaseSoftware;


	/**
	 * Class Text
	 * @package Framework\Syscrack\Game\Softwares
	 */
	class Text extends BaseSoftware
	{

		/**
		 * The configuration of this Structure
		 *
		 * @return array
		 */

		public function configuration()
		{

			return [
				'uniquename' => 'text',
				'extension' => '.txt',
				'type' => 'text',
				'viewable' => true,
				'removable' => true,
				'installable' => false,
				'executable' => true,
				'keepdata' => true
			];
		}
	}
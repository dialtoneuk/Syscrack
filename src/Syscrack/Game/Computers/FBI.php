<?php
	declare(strict_types=1);

	namespace Framework\Syscrack\Game\Computers;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class FBI
	 *
	 * @package Framework\Syscrack\Game\Computers
	 */
	class FBI extends Npc
	{

		/**
		 * The configuration of this computer
		 *
		 * @return array
		 */

		public function configuration()
		{

			return [
				'installable' => true,
				'type' => 'fbi',
				'reloadable' => false,
			];
		}
	}
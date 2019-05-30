<?php

	namespace Framework\Syscrack\Game\Computers;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class Download
	 *
	 * @package Framework\Syscrack\Game\Computers
	 */
	class Download extends Npc
	{

		/**
		 * Npc constructor.
		 */

		public function __construct()
		{

			parent::__construct();
		}

		/**
		 * The configuration of this computer
		 *
		 * @return array
		 */

		public function configuration()
		{

			return array(
				'installable'   => false,
				'type'          => 'download',
				'reloadable'    => true
			);
		}
	}
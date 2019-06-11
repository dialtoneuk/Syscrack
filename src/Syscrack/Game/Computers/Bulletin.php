<?php
	declare(strict_types=1);
	/**
	 * Created by PhpStorm.
	 * User: newsy
	 * Date: 05/05/2019
	 * Time: 15:36
	 */

	namespace Framework\Syscrack\Game\Computers;


	use Framework\Syscrack\Game\Interfaces\Computer;

	/**
	 * Class Bulletin
	 * @package Framework\Syscrack\Game\Computers
	 */
	class Bulletin extends Npc implements Computer
	{

		/**
		 * The configuration of this computer
		 *
		 * @return array
		 */

		public function configuration()
		{

			return [
				'installable'   => false,
				'type'          => 'bulletin',
				'reloadable'    => true
			];
		}
	}
<?php
	declare(strict_types=1);
	/**
	 * Created by PhpStorm.
	 * User: newsy
	 * Date: 09/05/2019
	 * Time: 10:13
	 */

	namespace Framework\Syscrack\Game\Utilities;


	use Framework\Syscrack\Game\Tool;

	/**
	 * Class EmptyTool
	 * @package Framework\Syscrack\Game\Utilities
	 */
	class EmptyTool extends Tool
	{

		/**
		 * @return array
		 */
		public function getRequirements()
		{

			return (["empty" => true]);
		}
	}
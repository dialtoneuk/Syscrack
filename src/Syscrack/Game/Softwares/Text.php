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
	use Framework\Syscrack\Game\Tab;


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

		/**
		 * @param null $userid
		 * @param null $sofwareid
		 * @param null $computerid
		 *
		 * @return Tab
		 */

		public function tab($userid = null, $sofwareid = null, $computerid = null): Tab
		{

			$tab = new Tab("Text Editor");
			$tab->bypass();
			$tab->render("syscrack/tabs/tab.texteditor");

			return( $tab );
		}
	}
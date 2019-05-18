<?php

	namespace Framework\Views\Pages;

	/**
	 * Lewis Lancaster 2016
	 *
	 * Class Index
	 *
	 * @package Framework\Views\Pages
	 */

	use Framework\Application\Container;
	use Framework\Application\Render;
	use Framework\Application\Settings;
	use Framework\Syscrack\Game\Computer;
	use Framework\Views\BaseClasses\Page as BaseClass;
	use Framework\Views\Structures\Page as Structure;

	class Index extends BaseClass implements Structure
	{

		/**
		 * Index constructor.
		 */

		public function __construct()
		{

			parent::__construct(true, true);
		}

		/**
		 * The index page has a special algorithm which allows it to access the root. Only the index can do this.
		 *
		 * @return array
		 */

		public function mapping()
		{

			return array(
				[
					'/', 'page'
				],
				[
					'/index/', 'page'
				]
			);
		}

		/**
		 * Default page
		 */

		public function page()
		{

			Render::view('syscrack/page.index', [], $this->model());
		}
	}
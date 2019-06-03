<?php

	namespace Framework\Views\Pages;

	use Framework\Views\BaseClasses\Page as BaseClass;

	class Index extends BaseClass
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

			$this->getRender('syscrack/page.index', [] );
		}
	}
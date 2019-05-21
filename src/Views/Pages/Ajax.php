<?php
	namespace Framework\Views\Pages;

	/**
	 * Class Ajax
	 *
	 * Automatically created at: 2019-05-18 10:19:27
	 * @package Framework\Views\Pages
	 */

	use Framework\Views\BaseClasses\Page;

	class Ajax extends Page
	{

		/**
		 * Ajax constructor.
		 */

		public function __construct()
		{

			parent::__construct(true, true );
		}

		/**
		 * @return array|mixed|void
		 */

		public function mapping()
		{

			parent::mapping();
		}
	}
<?php
	declare(strict_types=1);

	namespace Framework\Views\BaseClasses;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class Middleware
	 *
	 * @package Framework\Views\BaseClasses
	 */

	use Framework\Application\Render;
	use Framework\Application\Settings;
	use Framework\Views\Structures\Middleware as Structure;

	/**
	 * Class Middleware
	 * @package Framework\Views\BaseClasses
	 */
	class Middleware implements Structure
	{


		/**
		 * @return bool|mixed
		 */
		public function onRequest()
		{

			return( true );
		}

		/**
		 * @return bool|mixed
		 */
		public function onSuccess()
		{

			return( true );
		}

		/**
		 * @return bool|mixed
		 */
		public function onFailure()
		{

			return( true );
		}

		/**
		 * Renders a page
		 *
		 * @param $file
		 *
		 * @param $data
		 *
		 * @param bool $obclean
		 */

		public function render($file, $data, $obclean = true)
		{

			if ($obclean == true)
				ob_clean();

			Render::view($file, $data);
			exit;
		}

		/**
		 * Redirects the user to a page
		 *
		 * @param $url
		 *
		 * @param bool $exit
		 */

		public function redirect($url, $exit = false)
		{

			Render::redirect($url);

			if ($exit)
				exit;
		}

		/**
		 * Gets the current page
		 *
		 * @return string
		 */

		public function getCurrentPage()
		{

			$page = $this->getPageSplat();

			if (empty($page))
				return Settings::setting('controller_index_page');


			if (empty(explode('?', $page[0])) == false)
				return explode('?', $page[0])[0];

			return $page[0];
		}

		/**
		 * Gets the entire path in the form of an array
		 *
		 * @return array
		 */

		private function getPageSplat()
		{

			return array_values(array_filter(explode('/', strip_tags($_SERVER['REQUEST_URI']))));
		}
	}
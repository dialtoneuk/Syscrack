<?php
	declare(strict_types=1);

	namespace Framework\Views\Pages;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class Api
	 *
	 * @package Framework\Views\Pages
	 */

	use Flight;
	use Framework\Application\Api\Controller;
	use Framework\Application\Api\Manager;
	use Framework\Application\Render;
	use Framework\Application\Settings;
	use Framework\Application\Utilities\PostHelper;

	use Framework\Views\BaseClasses\Page as BaseClass;

	/**
	 * Class Api
	 * @package Framework\Views\Pages
	 */
	class Api extends BaseClass
	{

		/**
		 * @var Manager
		 */

		protected $manager;

		/**
		 * @var Controller
		 */

		protected $controller;

		/**
		 * @var mixed|string
		 */

		public $apikey = "";

		/**
		 * Api constructor.
		 */

		public static function setup( $autoload = true, $session = true )
		{

			parent::setup(false);
		}

		/**
		 * The themes mapping
		 *
		 * @return array
		 */

		public function mapping()
		{

			return [
				[
					'/api/@endpoint/(@method)/', 'process'
				]
			];
		}

		/**
		 * Processes the API request
		 *
		 * @param $endpoint
		 *
		 * @param null $method
		 */

		public function process($endpoint, $method = null)
		{

		}
	}
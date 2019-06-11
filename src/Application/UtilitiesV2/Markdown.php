<?php
	declare(strict_types=1);
	/**
	 * Created by PhpStorm.
	 * User: lewis
	 * Date: 06/07/2018
	 * Time: 01:20
	 */

	namespace Framework\Application\UtilitiesV2;

	use Parsedown;

	/**
	 * Class Markdown
	 * @package Framework\Application\UtilitiesV2
	 */
	class Markdown
	{

		/**
		 * @var Parsedown
		 */

		protected $parsedown;

		/**
		 * Markdown constructor.
		 */

		public function __construct()
		{

			$this->parsedown = new Parsedown();
		}

		/**
		 * @param string $text
		 *
		 * @return string
		 */

		public function markup(string $text)
		{

			return ($this->parsedown->text($text));
		}

		/**
		 * @param string $text
		 *
		 * @return string
		 */

		public function markdown(string $text)
		{

			return ($this->parsedown->parse($text));
		}

		/**
		 * @param bool $switch
		 */

		public function safeMode($switch = true)
		{

			$this->parsedown->setSafeMode($switch);
		}
	}
<?php
	/**
	 * Created by PhpStorm.
	 * User: lewis
	 * Date: 05/08/2018
	 * Time: 02:13
	 */

	namespace Framework\Application\UtilitiesV2\Controller;


	use Framework\Application\UtilitiesV2\Interfaces\Response;

	class FormRedirect implements Response
	{

		/**
		 * @var string
		 */

		protected $url;

		/**
		 * @var int
		 */

		protected $delay;

		/**
		 * @var bool
		 */

		protected $success = true;

		/**
		 * FormError constructor.
		 *
		 * @param $url
		 * @param int $delay
		 * @param null $success
		 *
		 * @throws \RuntimeException
		 */

		public function __construct($url, $delay = 0, $success = null)
		{

			if (is_string($url) == false || is_int($delay) == false)
				throw new \RuntimeException("Invalid param types");

			if ($success !== null)
				if (is_bool($success))
					$this->success = $success;

			$this->url = $url;
			$this->delay = $delay;
		}

		/**
		 * @return array
		 */

		public function get()
		{

			return (array(
				"success" => $this->success,
				"url" => $this->url,
				"delay" => $this->delay
			));
		}
	}
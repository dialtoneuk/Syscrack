<?php
	declare(strict_types=1);
	/**
	 * Created by PhpStorm.
	 * User: lewis
	 * Date: 05/08/2018
	 * Time: 02:13
	 */

	namespace Framework\Application\UtilitiesV2\Controller;


	use Framework\Application;
	use Framework\Application\UtilitiesV2\Interfaces\Response;

	/**
	 * Class FormError
	 * @package Framework\Application\UtilitiesV2\Controller
	 */
	class FormError implements Response
	{

		/**
		 * @var string
		 */

		protected $message;

		/**
		 * @var string
		 */

		protected $type;

		/**
		 * @var bool
		 */

		protected $success = true;

		/**
		 * @var int
		 */

		private $time;

		/**
		 * FormError constructor.
		 *
		 * @param string $type
		 * @param string $message
		 * @param null $success
		 *
		 * @throws \Error
		 */

		public function __construct($type = null, $message = "", $success = null)
		{

			if( $type == null )
				$type = Application::globals()->FORM_ERROR_GENERAL;

			if (is_string($message) == false || is_string($type) == false)
				throw new \Error("Invalid param types");

			if ($success !== null)
				if (is_bool($success))
					$this->success = $success;

			$this->message = $message;
			$this->type = $type;
			$this->time = time();
		}

		/**
		 * @return array
		 */

		public function get()
		{

			return ([
				"success" => $this->success,
				"message" => $this->message,
				"type" => $this->type,
				"time" => Application\UtilitiesV2\Format::timestamp( $this->time ),
			]);
		}
	}
<?php
	/**
	 * Created by PhpStorm.
	 * User: lewis
	 * Date: 05/08/2018
	 * Time: 02:13
	 */

	namespace Framework\Application\UtilitiesV2\Controller;


	use Framework\Application\UtilitiesV2\Interfaces\Response;
	use Framework\Application;

	class FormMessage implements Response
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
				$type = Application::globals()->FORM_MESSAGE_INFO;

			if (is_string($message) == false || is_string($type) == false)
				throw new \Error("Invalid param types");

			if ($success !== null)
				if (is_bool($success))
					$this->success = $success;

			$this->message = $message;
			$this->type = $type;
		}

		/**
		 * @return array
		 */

		public function get()
		{

			return (array(
				"success" => $this->success,
				"message" => $this->message,
				"type" => $this->type
			));
		}
	}
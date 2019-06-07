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

	class FormData implements Response
	{

		/**
		 * @var string
		 */

		protected $data;

		/**
		 * @var string
		 */

		protected $type;

		/**
		 * @var bool
		 */

		protected $success = true;

		/**
		 * FormData constructor.
		 *
		 * @param string $type
		 * @param $data
		 * @param null $success
		 */

		public function __construct($type = null, $data=[], $success = null)
		{

			if( $type == null )
				$type = Application::globals()->FORM_ERROR_GENERAL;

			if ($success !== null)
				if (is_bool($success))
					$this->success = $success;

			$this->data = $data;
			$this->type = $type;
			$this->time = time();
		}

		/**
		 * @return array
		 */

		public function get()
		{

			return (array(
				"success" => $this->success,
				"data" => $this->data,
				"type" => $this->type,
				"time" => $this->time,
			));
		}
	}
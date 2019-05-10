<?php
	/**
	 * Created by PhpStorm.
	 * User: lewis
	 * Date: 06/08/2018
	 * Time: 01:50
	 */

	namespace Framework\Application\UtilitiesV2\AutoExecs;


	use Framework\Application\UtilitiesV2\Container;
	use Framework\Application\UtilitiesV2\Interfaces\AutoExec;

	abstract class Base implements AutoExec
	{

		/**
		 * @var \Framework\Application\UtilitiesV2\Session
		 */

		protected $session;

		/**
		 * Base constructor.
		 * @throws \RuntimeException
		 */

		public function __construct()
		{

			if (Container::exist("application") == false)
				throw new \RuntimeException("Needs application");

			$this->session = Container::get("application")->session;
		}

		/**
		 * @param array $data
		 *
		 * @return mixed|void
		 */

		public function execute(array $data)
		{

			return;
		}
	}
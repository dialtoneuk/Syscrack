<?php
	/**
	 * Created by PhpStorm.
	 * User: lewis
	 * Date: 21/07/2018
	 * Time: 03:26
	 */

	namespace Framework\Application\UtilitiesV2\Setups;


	class Aws extends Base
	{

		/**
		 * Aws constructor.
		 * @throws \RuntimeException
		 */

		public function __construct()
		{

			if ($this->exists(AMAZON_CREDENTIALS_FILE) == false)
				throw new \RuntimeException("File does not exist");

			parent::__construct();
		}

		/**
		 * @return bool
		 */

		public function process()
		{

			$inputs = $this->getInputs([
				"key",
				"secret"
			]);

			$this->write(AMAZON_CREDENTIALS_FILE, $inputs);

			return parent::process();
		}
	}
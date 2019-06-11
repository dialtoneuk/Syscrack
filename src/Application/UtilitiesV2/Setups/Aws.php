<?php
	declare(strict_types=1);
	/**
	 * Created by PhpStorm.
	 * User: lewis
	 * Date: 21/07/2018
	 * Time: 03:26
	 */

	namespace Framework\Application\UtilitiesV2\Setups;

	use Framework\Application;

	/**
	 * Class Aws
	 * @package Framework\Application\UtilitiesV2\Setups
	 */

	class Aws extends Base
	{

		/**
		 * Aws constructor.
		 * @throws \Error
		 */

		public function __construct()
		{

			if ($this->exists(Application::globals()->AMAZON_CREDENTIALS_FILE) == false)
				throw new \Error("File does not exist");

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

			$this->write(Application::globals()->AMAZON_CREDENTIALS_FILE, $inputs);

			return parent::process();
		}
	}
<?php
	declare(strict_types=1);
	/**
	 * Created by PhpStorm.
	 * User: lewis
	 * Date: 21/07/2018
	 * Time: 03:06
	 */

	namespace Framework\Application\UtilitiesV2\Setups;


	use Framework\Application\UtilitiesV2\Debug;
	use Framework\Application\UtilitiesV2\Interfaces\Setup;

	/**
	 * Class Base
	 * @package Framework\Application\UtilitiesV2\Setups
	 */
	abstract class Base implements Setup
	{

		/**
		 * Base constructor.
		 * @throws \Error
		 */

		public function __construct()
		{

			if (Debug::isCMD() == false)
				throw new \Error("Not in CMD mode");
		}

		/**
		 * @return bool
		 */

		public function process()
		{

			return (true);
		}

		/**
		 * @param array $inputs
		 *
		 * @return array
		 */

		public function getInputs(array $inputs)
		{

			$results = [];

			foreach ($inputs as $key => $value)
				$results[$key] = Debug::getLine($key);

			return ($results);
		}

		/**
		 * @param $file
		 * @param bool $array
		 *
		 * @return mixed
		 */

		public function read($file, $array = true)
		{

			return (json_decode(file_get_contents(SYSCRACK_ROOT . $file), $array));
		}

		/**
		 * @param $file
		 * @param array $data
		 */

		public function write($file, array $data)
		{

			file_put_contents(SYSCRACK_ROOT . $file, json_encode($data));
		}

		/**
		 * @param $file
		 *
		 * @return bool
		 */

		public function exists($file)
		{

			return (file_exists(SYSCRACK_ROOT . $file));
		}
	}
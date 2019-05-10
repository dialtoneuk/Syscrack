<?php
	/**
	 * Created by PhpStorm.
	 * User: lewis
	 * Date: 20/07/2018
	 * Time: 18:56
	 */

	namespace Framework\Application\UtilitiesV2\Migrators;

	use Framework\Application\UtilitiesV2\Debug;
	use Framework\Application\UtilitiesV2\Featured as Features;

	class Featured extends Base
	{

		/**
		 * @var Features
		 */

		protected $features;

		/**
		 * Featured constructor.
		 * @throws \RuntimeException
		 */

		public function __construct()
		{

			$this->features = new Features(false);
		}

		/**
		 * @throws \RuntimeException
		 */

		public function migrate()
		{

			$default = $this->default();

			if (isset($default["name"]) == false || isset($default["default"]) == false)
				throw new \RuntimeException("Invalid default structure");

			foreach ($default['default'] as $name => $item)
			{

				if (file_exists(SYSCRACK_ROOT . FEATURED_ROOT . $name . ".json"))
					unlink(SYSCRACK_ROOT . FEATURED_ROOT . $name . ".json");

				$item["name"] = $name;
				$item["updated"] = time();
				$this->features->features[$name] = $item;

				if (Debug::isCMD())
					Debug::echo("Writing " . $name, 6);

				file_put_contents(SYSCRACK_ROOT . FEATURED_ROOT . $name . ".json", json_encode($this->features->features[$name]));
			}
		}


		/**
		 * @return mixed
		 * @throws \RuntimeException
		 */

		private function default()
		{

			if (file_exists(SYSCRACK_ROOT . FEATURED_ROOT . "default.json") == false)
				throw new \RuntimeException("Default file does not exist");

			return (json_decode(file_get_contents(SYSCRACK_ROOT . FEATURED_ROOT . "default.json"), true));
		}
	}
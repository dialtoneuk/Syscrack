<?php
	/**
	 * Created by PhpStorm.
	 * User: lewis
	 * Date: 21/07/2018
	 * Time: 03:14
	 */

	namespace Framework\Application\UtilitiesV2\Setups;


	class Database extends Base
	{

		/**
		 * Database constructor.
		 * @throws \Error
		 */

		public function __construct()
		{

			if ($this->exists(DATABASE_MAP) == false)
				throw new \Error("File does not exist");

			parent::__construct();
		}

		/**
		 * @return bool
		 * @throws \Error
		 */

		public function process()
		{

			$map = $this->getMap();

			if (empty($map))
				throw new \Error("Invalid map");

			$inputs = $this->getInputs($map);

			if (count($map) !== count($inputs))
				throw new \Error("Count mismatch");

			foreach ($inputs as $key => $value)
				if (isset($map[$key]) == false)
					throw new \Error("Key not set");

			$this->write(DATABASE_CREDENTIALS, $inputs);

			return parent::process();
		}

		/**
		 * @return mixed
		 */

		private function getMap()
		{

			return ($this->read(DATABASE_MAP));
		}
	}
<?php
	declare(strict_types=1);

	namespace Framework\Syscrack;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class BetaKey
	 *
	 * @package Framework\Syscrack
	 */

	use Framework\Application\Settings;
	use Framework\Application\Utilities\FileSystem;

	/**
	 * Class BetaKeys
	 * @package Framework\Syscrack
	 */
	class BetaKeys
	{

		/**
		 * @var null
		 */

		private $keys = [];

		/**
		 * Removes a betakey from the list
		 *
		 * @param $betakey
		 */

		public function remove($betakey)
		{

			foreach ($this->keys as $key => $value)
			{

				if ($value == $betakey)
				{

					unset($this->keys[$key]);
				}
			}

			$this->save();
		}

		/**
		 * Returns true if this beta-key exists
		 *
		 * @param $betakey
		 *
		 * @return bool
		 */

		public function exists($betakey)
		{

			if (empty($this->keys))
			{

				return false;
			}

			foreach ($this->keys as $key => $value)
			{

				if ($value == $betakey)
				{

					return true;
				}
			}

			return false;
		}

		/**
		 * Gets the beta keys
		 *
		 * @return null
		 */

		public function keys()
		{

			if (FileSystem::exists(Settings::setting('betakey_location')) == false)
				return null;

			$this->keys = FileSystem::readJson(Settings::setting('betakey_location'));

			return( $this->keys );
		}

		/**
		 * Generates a set of Betakeys
		 *
		 * @param int $count
		 *
		 * @return array
		 */

		public function create($count = 1)
		{

			$keys = [];

			for ($i = 0; $i < $count; $i++)
			{

				$keys[] = $this->generate();
			}

			$this->keys = array_merge( $keys, $this->keys );

			return $keys;
		}

		/**
		 * Generates a new betakey
		 *
		 * @return string
		 */

		private function generate()
		{

			$key = "";

			for ($i = 0; $i < Settings::setting('betakey_steps'); $i++)
			{

				$step = "";

				for ($k = 0; $k < Settings::setting('betakey_length'); $k++)
				{

					$step = $step . rand(0, 9);
				}

				$key = $step . "-" . $key;
			}

			return rtrim($key, '-');
		}

		/**
		 * Adds the beta-key to the array and then saves
		 *
		 * @param $betakey
		 */

		protected function add($betakey = null)
		{


			if (is_array($betakey))
			{

				foreach ($betakey as $key => $value)
				{

					$this->keys[] = $value;
				}

				$this->save();
			}
			else
			{

				$this->keys[] = $betakey;

				$this->save();
			}
		}

		/**
		 * Saves the betakeys
		 */

		private function save()
		{

			FileSystem::writeJson(Settings::setting('betakey_location'), $this->keys);
		}
	}
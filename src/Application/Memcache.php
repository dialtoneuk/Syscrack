<?php

	namespace Framework\Application;

	/**
	 * Lewis Lancaster 2018
	 *
	 * Class Memcache
	 *
	 * @package Framework\Application
	 */

	use Framework\Exceptions\ApplicationException;
	use Memcache as MemcacheServer;

	class Memcache
	{

		/**
		 * @var MemcacheServer
		 */

		protected $memcache;

		/**
		 * Memcached constructor.
		 *
		 * @param bool $autoconnect
		 */

		public function __construct($autoconnect = true)
		{

			if (Settings::setting('memcache_enabled') == false)
			{

				return;
			}

			if (extension_loaded('memcache') == false)
			{

				throw new ApplicationException('Memcache is not enabled as an extension ( or it isnt installed )');
			}

			$this->memcache = new MemcacheServer();

			if ($autoconnect == true)
			{

				if ($this->hasConnection() == false)
				{

					$this->createConnection();
				}
			}
		}

		/**
		 * Adds a variable to the memcache
		 *
		 * @param $variable
		 *
		 * @param $value
		 *
		 * @param null $lifespan
		 */

		public function add($variable, $value, $lifespan = null)
		{

			if ($lifespan = null)
			{

				$lifespan = Settings::setting('memcache_default_lifespan');
			}

			$this->memcache->set($variable, $value, MEMCACHE_COMPRESSED, $lifespan);
		}

		/**
		 * Deletes a variable
		 *
		 * @param $variable
		 */

		public function delete($variable)
		{

			$this->memcache->delete($variable);
		}

		/**
		 * Returns true if we have this key ( already )
		 *
		 * @param $variable
		 *
		 * @return bool
		 */

		public function hasKey($variable)
		{

			if ($this->memcache->add($variable, null))
			{

				$this->memcache->delete($variable);

				return false;
			}

			return true;
		}

		/**
		 * Gets a variable from the memcache
		 *
		 * @param $variable
		 *
		 * @return array|string
		 */

		public function get($variable)
		{

			return $this->memcache->get($variable);
		}

		/**
		 * Flushes the memcache
		 */

		public function flush()
		{

			$this->memcache->flush();
		}

		/**
		 * Creates a new connection
		 */

		public function createConnection()
		{

			if ($this->hasConnection() == true)
			{

				return;
			}

			$this->memcache->connect(Settings::setting('memcache_address'), Settings::setting('memcache_port'), Settings::setting('memcache_timeout'));
		}

		/**
		 * Returns true if we have a memcache connection
		 *
		 * @return bool
		 */

		public function hasConnection()
		{

			$stats = $this->memcache->getStats();

			if (isset($stats[Settings::setting('memcache_address') . ':' . Settings::setting('memcache_port')]) == false)
			{

				return false;
			}

			if ($stats[Settings::setting('memcache_address') . ':' . Settings::setting('memcache_port')]['uptime'] > 1)
			{

				return false;
			}

			return true;
		}
	}
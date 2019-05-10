<?php
	/**
	 * Created by PhpStorm.
	 * User: lewis
	 * Date: 05/08/2018
	 * Time: 23:15
	 */

	namespace Framework\Application\UtilitiesV2\Controller;


	use Framework\Application\UtilitiesV2\Collector;
	use Framework\Application\UtilitiesV2\Container;
	use Framework\Application\UtilitiesV2\Group;
	use Framework\Application\UtilitiesV2\User;

	class Permissions
	{

		/**
		 * @var Group
		 */

		protected $group;

		/**#
		 * @var User
		 */

		protected $user;

		/**
		 * @var mixed
		 */

		protected $cache = null;

		/**
		 * @var \Framework\Application\UtilitiesV2\Session
		 */

		protected $session;

		/**
		 * Permissions constructor.
		 *
		 * @param bool $auto_create
		 *
		 * @throws \RuntimeException
		 */

		public function __construct($auto_create = true)
		{

			if (Container::exist("application") == false)
				throw new \RuntimeException("Needs application");

			if ($auto_create)
				$this->create();
		}

		/**
		 * @throws \RuntimeException
		 */

		public function create()
		{

			$this->session = Container::get("application")->session;
			$this->group = Collector::new("Group");
			$this->user = Collector::new("User");
		}

		/**
		 * @param $flag
		 *
		 * @return bool
		 * @throws \RuntimeException
		 */

		public function hasPermission($flag)
		{

			$group = $this->getUserGroupName();

			if ($group == null)
				return false;

			if ($this->group->hasFlag($group, $flag) == false)
				return false;

			return true;
		}

		/**
		 * @param $flag
		 *
		 * @return mixed|null
		 * @throws \RuntimeException
		 */

		public function getPermission($flag)
		{


			$group = $this->getUserGroupName();

			if ($group == null)
				return null;

			if ($this->group->hasFlag($group, $flag) == false)
				return null;

			return ($this->group->get($flag));
		}

		/**
		 * @return bool
		 * @throws \RuntimeException
		 */

		public function isAdmin()
		{

			if ($this->hasPermission(GROUPS_FLAG_ADMIN) == false)
				return false;

			return true;
		}

		/**
		 * @return bool
		 * @throws \RuntimeException
		 */

		public function canUploadLoessless()
		{

			if ($this->hasPermission(GROUPS_FLAG_LOSSLESS) == false)
				return false;

			return true;
		}

		/**
		 * @param bool $use_cache
		 *
		 * @return null
		 * @throws \RuntimeException
		 */

		public function getUserGroupName($use_cache = true)
		{

			if ($this->session->isLoggedIn() == false)
				return null;


			if ($use_cache)
			{

				if ($this->isCached())
					$user = $this->cache;
				else
					$user = $this->cache($this->user->get($this->session->userid()));
			}
			else
				$user = $this->user->get($this->session->userid());

			if ($this->group->exist($user->group) == false)
				return null;

			return ($user->group);
		}

		/**
		 * @param $user
		 *
		 * @return mixed
		 */

		private function cache($user)
		{

			$this->cache = $user;

			return ($this->cache);
		}

		/**
		 * @return bool
		 */

		private function isCached()
		{

			if (empty($this->cache))
				return false;

			return true;
		}

		/**
		 * @param $group
		 *
		 * @return mixed
		 */

		private function getGroup($group)
		{

			return ($this->group->get($group));
		}
	}
<?php
	/**
	 * Created by PhpStorm.
	 * User: lewis
	 * Date: 11/08/2018
	 * Time: 19:05
	 */

	namespace Framework\Application\UtilitiesV2\Controller;


	use Framework\Application\UtilitiesV2\Container;
	use Framework\Application\UtilitiesV2\UserPermissions as Permissions;

	class UserPermissions
	{

		/**
		 * @var \Framework\Application\UtilitiesV2\Session
		 */

		protected $session;

		/**
		 * @var Permissions
		 */

		protected $userpermissions;

		/**
		 * @var int|null
		 */

		protected $userid = null;

		/**
		 * @var array
		 */

		protected $cache = [];

		/**
		 * UserPermissions constructor.
		 *
		 * @param bool $auto_read
		 *
		 * @throws \Error
		 */

		public function __construct($auto_read = true)
		{

			if (Container::exist('application') == false)
				throw new \Error("Please init application");

			$this->session = Container::get('application')->session;

			if ($this->session->isLoggedIn())
				$this->userid = $this->session->userid();
			else
				throw new \Error("User needs to be logged in");

			$this->userpermissions = new Permissions();
		}

		/**
		 * @param $flag
		 *
		 * @return mixed
		 */

		public function getPermission($flag)
		{

			if ($this->checkCache() == false)
				$this->cache($this->userid);

			return ($this->cache[$flag]);
		}

		/**
		 * @param $flag
		 *
		 * @return bool
		 */

		public function has($flag)
		{

			if ($this->checkCache() == false)
				$this->cache($this->userid);

			if (isset($this->cache[$flag]) == true)
				return true;

			return false;
		}

		/**
		 * Refresh
		 */

		public function refresh()
		{

			$this->cache($this->userid);
		}

		/**
		 * @return bool
		 */

		public function hasUserPermissions()
		{

			$this->cache($this->userid);

			if (empty($this->cache))
				return false;

			return true;
		}

		/**
		 * @return mixed|null
		 */

		public function updated()
		{

			if ($this->checkCache() == false)
				$this->cache($this->userid);

			if (isset($this->cache["updated"]) == false)
				return null;

			return ($this->cache["updated"]);
		}

		/**
		 * @param array $values
		 */

		public function update(array $values)
		{

			if ($this->checkCache() == false)
				$this->cache($this->userid);

			$this->userpermissions->save($this->userid, array_merge($this->cache, $values));
			$this->cache($this->userid);
		}

		/**
		 * @return bool
		 */

		private function checkCache()
		{

			if (empty($this->cache))
				return false;

			return true;
		}

		/**
		 * @param $userid
		 */

		private function cache($userid)
		{

			if ($this->userpermissions->exist($userid) == false)
				$this->cache = [];
			else
				$this->cache = $this->userpermissions->get($userid);
		}
	}
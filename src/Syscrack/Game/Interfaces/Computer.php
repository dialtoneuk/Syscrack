<?php
	declare(strict_types=1);

	namespace Framework\Syscrack\Game\Interfaces;

	use Framework\Syscrack\Game\Tabs;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Interface BaseComputer
	 *
	 * @package Framework\Syscrack\Game\Interfaces
	 */
	interface Computer
	{

		/**
		 * @return array
		 */

		public function configuration();

		/**
		 * @param $computerid
		 * @param $userid
		 * @param array $software
		 * @param array $hardware
		 * @param array $custom
		 *
		 * @return mixed
		 */

		public function onStartup($computerid, $userid, array $software = [], array $hardware = [], array $custom = []);

		/**
		 * @param $computerid
		 *
		 * @return mixed
		 */

		public function onReset($computerid);

		/**
		 * @param $computerid
		 * @param $ipaddress
		 *
		 * @return mixed
		 */

		public function onLogin($computerid, $ipaddress);

		/**
		 * @param $computerid
		 * @param $ipaddress
		 *
		 * @return mixed
		 */

		public function onLogout($computerid, $ipaddress);

		/**
		 * @param null $userid
		 * @param null $softwareid
		 * @param null $computerid
		 *
		 * @return Tabs
		 */

		public function tab( $userid = null, $softwareid = null, $computerid = null ): Tabs;

		/**
		 * @param $computerid
		 * @param $userid
		 *
		 * @return array
		 */

		public function data($computerid, $userid);
	}
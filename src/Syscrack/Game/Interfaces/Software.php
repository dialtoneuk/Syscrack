<?php
	declare(strict_types=1);

	namespace Framework\Syscrack\Game\Interfaces;

	use Framework\Syscrack\Game\Tool;
	use Framework\Syscrack\Game\Tab;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Interface Software
	 *
	 * @package Framework\Syscrack\Game\Interfaces
	 */
	interface Software
	{

		/**
		 * The configuration of this software
		 *
		 * @return array
		 */

		public function configuration();

		/**
		 * Called when a software is executed
		 *
		 * @param $softwareid
		 *
		 * @param $userid
		 *
		 * @param $computerid
		 *
		 * @return mixed
		 */

		public function onExecuted($softwareid, $userid, $computerid);

		/**
		 * Called when this software is installed on a computer
		 *
		 * @param $softwareid
		 *
		 * @param $userid
		 *
		 * @param $comptuerid
		 *
		 * @return mixed
		 */

		public function onInstalled($softwareid, $userid, $comptuerid);

		/**
		 * Called when the software is uninstalled
		 *
		 * @param $softwareid
		 *
		 * @param $userid
		 *
		 * @param $computerid
		 *
		 * @return mixed
		 */

		public function onUninstalled($softwareid, $userid, $computerid);

		/**
		 * Called when the software collects ( virus type only )
		 *
		 * @param $softwareid
		 *
		 * @param $userid
		 *
		 * @param $computerid
		 *
		 * @param $timeran
		 *
		 * @return float
		 */

		public function onCollect($softwareid, $userid, $computerid, $timeran);

		/**
		 * Gets the execute completion time ( only on execute and if executable is equal to true )
		 *
		 * @param $softwareid
		 *
		 * @param $computerid
		 *
		 * @return mixed|null
		 */

		public function getExecuteCompletionTime($softwareid, $computerid);

		/**
		 * @param $softwareid
		 * @param $userid
		 * @param $computerid
		 *
		 * @return mixed
		 */

		public function onLogin($softwareid, $userid, $computerid);

		/**
		 * @param $userid
		 * @param $sofwareid
		 * @param $computerid
		 *
		 * @return Tool
		 */

		public function tool($userid = null, $sofwareid = null, $computerid = null): Tool;

		/**
		 * @param null $userid
		 * @param null $softwareid
		 * @param null $computerid
		 *
		 * @return Tab
		 */

		public function tab( $userid = null, $softwareid = null, $computerid = null ): Tab;


		/**
		 * @param null $userid
		 * @param null $softwareid
		 * @param null $computer
		 *
		 * @return array
		 */
		public function data( $userid = null, $softwareid = null, $computer = null ): array;
	}
<?php
	/**
	 * Created by PhpStorm.
	 * User: newsy
	 * Date: 13/05/2019
	 * Time: 22:44
	 */

	namespace Framework\Syscrack\Game\Interfaces;

	/**
	 * Interface Item
	 * @package Framework\Syscrack\Game\Interfaces
	 */

	interface Item
	{

		/**
		 * @param $itemid
		 * @param $userid
		 *
		 * @return bool
		 */

		public function used( $itemid, $userid ): bool

		/**
		 * @param $itemid
		 * @param $userid
		 * @param $computer
		 *
		 * @return bool
		 */

		public function equipped( $itemid, $userid, $computerid ): bool

		/**
		 * @param $itemud
		 * @param $userid
		 * @param $targetid
		 *
		 * @return bool
		 */

		public function traded( $itemud, $userid, $targetid ): bool
	}
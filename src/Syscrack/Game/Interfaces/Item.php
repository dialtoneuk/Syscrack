<?php
	declare(strict_types=1);
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
		 * @param $targetid
		 *
		 * @return bool
		 */

		public function used( $itemid, $userid, $targetid ): bool;

		/**
		 * @param $itemid
		 * @param $userid
		 * @param $computerid
		 *
		 * @return bool
		 */

		public function equipped( $itemid, $userid, $computerid ): bool;

		/**
		 * @param $itemud
		 * @param $userid
		 * @param $targetid
		 *
		 * @return bool
		 */

		public function traded( $itemud, $userid, $targetid ): bool;
	}
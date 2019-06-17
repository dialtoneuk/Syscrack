<?php
	declare(strict_types=1);
	/**
	 * Created by PhpStorm.
	 * User: newsy
	 * Date: 09/05/2019
	 * Time: 20:01
	 */

	namespace Framework\Syscrack\Game\Operations;


	use Framework\Syscrack\Game\Bases\BaseOperation;

	/**
	 * Class ResearchCentre
	 * @package Framework\Syscrack\Game\Operations
	 */
	class ResearchCentre extends BaseOperation
	{

		/**
		 * Returns the configuration
		 *
		 * @return array
		 */

		public function configuration()
		{

			return [
				'allowsoftware' => false,
				'allowlocal' => true,
				'requiresoftware' => false,
				'requireloggedin' => false,
				'allowpost' => false
			];
		}

		/**
		 * @param null $ipaddress
		 *
		 * @return string
		 */

		public function url($ipaddress = null)
		{

			return( parent::url($ipaddress) );
		}

		/**
		 * @param $timecompleted
		 * @param $computerid
		 * @param $userid
		 * @param $process
		 * @param array $data
		 *
		 * @return bool
		 */
		public function onCreation($timecompleted, $computerid, $userid, $process, array $data)
		{

			if ( @self::$computer->hasType($computerid, 'research') == false)
				return false;

			return true;
		}

		/**
		 * @param $timecompleted
		 * @param $timestarted
		 * @param $computerid
		 * @param $userid
		 * @param $process
		 * @param array $data
		 *
		 * @return bool|null|string
		 */

		public function onCompletion($timecompleted, $timestarted, $computerid, $userid, $process, array $data)
		{

			$softwares = self::$software->getLicensedSoftware($computerid);

			if( parent::onCompletion(
					$timecompleted,
					$timestarted,
					$computerid,
					$userid,
					$process,
					$data) == false )
				return false;
			else
				$this->render('operations/operations.research',
					["licenses" => $softwares], true, true);

			return null;
		}
	}
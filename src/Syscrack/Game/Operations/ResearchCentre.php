<?php
	/**
	 * Created by PhpStorm.
	 * User: newsy
	 * Date: 09/05/2019
	 * Time: 20:01
	 */

	namespace Framework\Syscrack\Game\Operations;


	use Framework\Syscrack\Game\Bases\BaseOperation;

	class ResearchCentre extends BaseOperation
	{

		/**
		 * Returns the configuration
		 *
		 * @return array
		 */

		public function configuration()
		{

			return array(
				'allowsoftware' => false,
				'allowlocal' => true,
				'requiresoftware' => false,
				'requireloggedin' => false,
				'allowpost' => false
			);
		}

		/**
		 * @param null $ipaddress
		 *
		 * @return string
		 */

		public function url($ipaddress = null)
		{

			if ($ipaddress == null)
				return (parent::url($ipaddress));

			return ('game/internet/' . @$ipaddress . '/remoteadmin');
		}

		public function onCreation($timecompleted, $computerid, $userid, $process, array $data)
		{

			if (self::$computer->hasType($computerid, 'research') == false)
				return false;

			return parent::onCreation($timecompleted, $computerid, $userid, $process, $data);
		}

		public function onCompletion($timecompleted, $timestarted, $computerid, $userid, $process, array $data)
		{

			$softwares = self::$software->getLicensedSoftware($computerid);
			$this->render('operations/operations.research', ["licenses" => $softwares], true, true);
			return null;
		}
	}
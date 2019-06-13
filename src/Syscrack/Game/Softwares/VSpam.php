<?php
	declare(strict_types=1);

	namespace Framework\Syscrack\Game\Softwares;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class VSpam
	 *
	 * @package Framework\Syscrack\Game\Softwares
	 */

	use Framework\Application\Settings;
	use Framework\Syscrack\Game\Bases\BaseSoftware;


	/**
	 * Class VSpam
	 * @package Framework\Syscrack\Game\Softwares
	 */
	class VSpam extends BaseSoftware
	{

		/**
		 * The configuration of this Structure
		 *
		 * @return array
		 */

		public function configuration()
		{

			return [
				'uniquename' => 'vspam',
				'extension' => '.vspam',
				'type' => 'virus',
				'installable' => true,
				'uninstallable' => true,
				'executable' => false,
				'removable' => false,
			];
		}

		/**
		 * @param $softwareid
		 * @param $userid
		 * @param $computerid
		 * @param $timeran
		 *
		 * @return float|int
		 */

		public function onCollect($softwareid, $userid, $computerid, $timeran)
		{

			if (parent::$hardware->hasHardwareType($computerid, Settings::setting('hardware_type_cpu')) == false)
				return Settings::setting('collector_vspam_yield') * $timeran;


			return (Settings::setting('collector_vspam_yield') * (parent::$hardware->getCPUSpeed($computerid) * $timeran)) / Settings::setting('collector_global_yield');
		}
	}
<?php
	declare(strict_types=1); //Created at 2019-06-11 03:15:36 by 19416

	namespace Framework\Syscrack\Game\Softwares;

	use Framework\Syscrack\Game\Bases\BaseSoftware;
	use Framework\Syscrack\Game\Tab;

	/**
	 * Class Package
	 * @package Framework\Syscrack\Game\Softwares
	 */
	class Package extends BaseSoftware
	{

		/**
		 * @return array
		 */

		public function configuration()
		{

			return [
				'uniquename' => 'package',
				'extension' => '.pk',
				'type' => 'package',
				'installable' => false,
				'executable' => true
			];
		}

		/**
		 * @param $softwareid
		 * @param $userid
		 * @param $computerid
		 *
		 * @return mixed|void
		 */

		public function onExecuted($softwareid, $userid, $computerid)
		{

			$data = self::$software->getSoftwareData( $softwareid );

			if( isset( $data["softwares"] ) == false )
				$this->formError('Package is corrupted', $this->path( $computerid ) );
			else
			{

				$softwares = $data["softwares"];
				$results = [];

				foreach( $softwares as $software )
					$results[] = $this->addSoftware( $computerid, $userid, $software );
			}

			if( empty( $results ) )
				$this->formError("Failed to extract any files", $this->path( $computerid), true );

			parent::onExecuted($softwareid, $userid, $computerid);
		}

		/**
		 * @param null $userid
		 * @param null $sofwareid
		 * @param null $computerid
		 *
		 * @return Tab
		 */

		public function tab($userid = null, $sofwareid = null, $computerid = null): Tab
		{

			$tab = new Tab("Package Editor");
			$tab->bypass();
			$tab->render("syscrack/tabs/tab.package");

			return( $tab );
		}
	}
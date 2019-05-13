<?php

	namespace Framework\Syscrack\Game\Softwares;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class AntiVirus
	 *
	 * @package Framework\Syscrack\Game\Softwares
	 */

	use Framework\Syscrack\Game\Bases\BaseSoftware;
	use Framework\Syscrack\Game\Utilities\TimeHelper;
	use Framework\Syscrack\Game\Viruses;

	class AntiVirus extends BaseSoftware
	{

		/**
		 * @var Viruses
		 */

		protected static $viruses;

		/**
		 * AntiVirus constructor.
		 */

		public function __construct()
		{

			if (isset(self::$viruses) == false)
				self::$viruses = new Viruses();

			parent::__construct(true);
		}

		/**
		 * The configuration of this Structure
		 *
		 * @return array
		 */

		public function configuration()
		{

			return array(
				'uniquename' => 'antivirus',
				'extension' => '.av',
				'type' => 'exe',
				'installable' => true,
				'executable' => true
			);
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

			$viruses = self::$viruses->getVirusesOnComputer($computerid);

			if (empty($viruses))
				$this->redirectError('No viruses were found', $this->getRedirect(self::$internet->getComputerAddress($computerid)));

			$software = parent::$software->getSoftware($softwareid);
			$results = [];

			foreach ($viruses as $virus)
			{

				if ($virus->level > $software->level)
					continue;


				if ($virus->installed == false)
					continue;


				$results[] = array(
					'softwareid' => $virus->softwareid
				);

				parent::$software->deleteSoftware($virus->softwareid);
				self::$computers->removeSoftware($computerid, $virus->softwareid);
			}

			if (empty($results))
				$this->redirectError('No errors were deleted, this could be due to your anti-virus being too weak', $this->getRedirect(self::$internet->getComputerAddress($computerid)));

			$this->redirectSuccess($this->getRedirect(self::$internet->getComputerAddress($computerid)));
		}

		/**
		 * @param $softwareid
		 * @param $computerid
		 *
		 * @return int|mixed|null
		 */

		public function getExecuteCompletionTime($softwareid, $computerid)
		{

			return (TimeHelper::getSecondsInFuture(1));
		}
	}
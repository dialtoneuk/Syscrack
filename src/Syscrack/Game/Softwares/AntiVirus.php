<?php
	declare(strict_types=1);

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

	/**
	 * Class AntiVirus
	 * @package Framework\Syscrack\Game\Softwares
	 */
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

			return [
				'uniquename' => 'antivirus',
				'extension' => '.av',
				'type' => 'antivirus',
				'installable' => true,
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

			$viruses = self::$viruses->getVirusesOnComputer($computerid);

			if (empty($viruses))
				$this->formError('No viruses were found', $this->getRedirect(self::$internet->getComputerAddress($computerid)), false );
			else
			{

				$software = parent::$software->getSoftware($softwareid);
				$results = [];

				foreach ($viruses as $virus)
				{

					if ($virus->level > $software->level)
						continue;

					if ($virus->installed == false)
						continue;

					$results[] = [
						'softwareid' => $virus->softwareid
					];

					parent::$software->deleteSoftware($virus->softwareid);
					self::$computer->removeSoftware($computerid, $virus->softwareid);
				}
			}

			if (empty($results))
				$this->formError('No errors were deleted, this could be due to your anti-virus being too weak', $this->getRedirect(self::$internet->getComputerAddress($computerid)));

			$this->formSuccess($this->getRedirect(self::$internet->getComputerAddress($computerid)));
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
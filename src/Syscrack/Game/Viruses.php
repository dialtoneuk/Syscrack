<?php
	declare(strict_types=1);

	namespace Framework\Syscrack\Game;

	/**
	 * Lewis Lancaster 2016
	 *
	 * Class Viruses
	 *
	 * @package Framework\Syscrack\Game
	 */

	use Framework\Application\Settings;
	use Framework\Database\Tables\Computer;
	use Framework\Database\Tables\Software;

	/**
	 * Class Viruses
	 * @package Framework\Syscrack\Game
	 */
	class Viruses
	{

		/**
		 * @var Computer
		 */

		protected $computers;

		/**
		 * @var Software
		 */

		protected $software;

		/**
		 * Viruses constructor.
		 */

		public function __construct()
		{

			$this->computers = new Computer();

			$this->software = new Software();
		}

		/**
		 * Updates the time since the virus has been modified
		 *
		 * @param $softwareid
		 */

		public function updateVirusModified($softwareid)
		{

			$this->software->updateSoftware($softwareid, [
				'lastmodified' => time()
			]);
		}

		/**
		 * Returns true if there are viruses on the computer
		 *
		 * @param $computerid
		 *
		 * @param null $userid
		 *
		 * @return bool
		 */

		public function hasVirusesOnComputer($computerid, $userid = null)
		{

			if ($userid != null)
			{

				if (empty($this->getVirusesOnComputer($computerid, $userid)) || $this->getVirusesOnComputer($computerid, $userid) == null)
				{

					return false;
				}

				return true;
			}

			if ($this->software->getTypeOnComputer(Settings::setting('software_virus_type'), $computerid) == null)
			{

				return false;
			}

			return true;
		}

		/**
		 * Returns true if this software is a virus
		 *
		 * @param $softwareid
		 *
		 * @return bool
		 */

		public function isVirus($softwareid)
		{

			if ($this->software->getSoftware($softwareid)->type == Settings::setting('software_virus_type'))
			{

				return true;
			}

			return false;
		}

		/**
		 * Checks if this type virus is already installed
		 *
		 * @param $uniquename
		 *
		 * @param $computerid
		 *
		 * @param $userid
		 *
		 * @return bool
		 */

		public function virusAlreadyInstalled($uniquename, $computerid, $userid)
		{

			$viruses = $this->software->getTypeOnComputer(Settings::setting('software_virus_type'), $computerid);

			foreach ($viruses as $virus)
			{

				if ($virus->userid == $userid)
				{

					if ($virus->uniquename == $uniquename)
					{

						if ($virus->installed == true)
						{

							return true;
						}
					}
				}
			}

			return false;
		}

		/**
		 * Gets the viruses on the computer
		 *
		 * @param $computerid
		 *
		 * @param null $userid
		 *
		 * @return array|\Illuminate\Support\Collection|null
		 */

		public function getVirusesOnComputer($computerid, $userid = null)
		{

			if ($userid != null)
			{

				$viruses = $this->software->getTypeOnComputer(Settings::setting('software_virus_type'), $computerid);

				if (empty($viruses))
				{

					return null;
				}

				$result = [];

				foreach ($viruses as $virus)
				{

					if ($virus->userid == $userid)
					{

						$result[] = $virus;
					}
				}

				return $result;
			}

			return $this->software->getTypeOnComputer(Settings::setting('software_virus_type'), $computerid);
		}
	}
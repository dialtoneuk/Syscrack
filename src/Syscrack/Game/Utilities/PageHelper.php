<?php

	namespace Framework\Syscrack\Game\Utilities;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class PageHelper
	 *
	 * @package Framework\Syscrack\Game\Utility
	 */

	use Framework\Application\Container;
	use Framework\Application\Settings;
	use Framework\Application\Utilities\ArrayHelper;
	use Framework\Exceptions\SyscrackException;
	use Framework\Syscrack\Game\AddressDatabase;
	use Framework\Syscrack\Game\Computer;
	use Framework\Syscrack\Game\Finance;
	use Framework\Syscrack\Game\Software;
	use Framework\Syscrack\User;

	class PageHelper
	{

		/**
		 * @var \Framework\Application|\Framework\Application\Session
		 */

		protected $session;

		/**
		 * PageHelper constructor.
		 */

		public function __construct()
		{

			if (Container::hasObject('session') == false)
			{

				return;
			}

			$this->session = Container::getObject('session');
		}

		/**
		 * Returns true if we are currently connected
		 *
		 * @param $ipaddress
		 *
		 * @return bool
		 */

		public function isCurrentlyConnected($ipaddress)
		{

			if (isset($_SESSION['connected_ipaddress']) == false)
			{

				return false;
			}

			if ($_SESSION['connected_ipaddress'] == $ipaddress)
			{

				return true;
			}

			return false;
		}

		/**
		 * Returns true if we have already hacked this computer
		 *
		 * @param $ipaddress
		 *
		 * @return bool
		 */

		public function alreadyHacked($ipaddress)
		{

			$addressdatabase = new AddressDatabase();

			if ($addressdatabase->hasAddress($ipaddress, $this->session->userid()) == false)
			{

				return false;
			}

			return true;
		}

		/**
		 * Gets the users id
		 *
		 * @return string
		 */

		public function getUserID()
		{

			return $this->session->userid();
		}

		/**
		 * Gets the users address
		 *
		 * @return mixed
		 */

		public function getUserAddress()
		{

			return $this->session->getSessionAddress();
		}

		/**
		 * Gets computers software
		 *
		 * @return array
		 */

		public function getComputerSoftware()
		{

			$computer = new Computer();

			if ($computer->userHasComputers($this->session->userid()) == false)
			{

				throw new SyscrackException();
			}

			return $computer->getComputerSoftware($computer->computerid());
		}

		/**
		 * Gets the computers hardware
		 *
		 * @return array
		 */

		public function getComputerHardware()
		{

			$computer = new Computer();

			if ($computer->userHasComputers($this->session->userid()) == false)
			{

				throw new SyscrackException();
			}

			return $computer->getComputerHardware($computer->computerid());
		}

		/**
		 * Gets a computers type
		 *
		 * @param $computerid
		 *
		 * @return mixed
		 */

		public function getComputerType($computerid)
		{

			$computer = new Computer();

			if ($computer->getComputer($computerid) == null)
			{

				throw new SyscrackException();
			}

			return $computer->getComputer($computerid)->type;
		}

		/**
		 * Gets the users installed type of software
		 *
		 * @param $type
		 *
		 * @return array|null
		 */

		public function getInstalledType($type)
		{

			$computer = new Computer();

			$software = new Software();

			if ($computer->userHasComputers($this->session->userid()) == false)
			{

				throw new SyscrackException();
			}

			$computersoftware = $computer->getComputerSoftware($computer->computerid());

			$results = [];

			foreach ($computersoftware as $csoftware)
			{

				if ($csoftware['type'] == $type)
				{

					if ($csoftware['installed'] == true)
					{

						if ($software->softwareExists($csoftware['softwareid']) == false)
						{

							continue;
						}

						$results[] = $software->getSoftware($csoftware['softwareid']);
					}
				}
			}

			if (empty($results))
			{

				return null;
			}

			$results = ArrayHelper::sortArray($results, 'level');

			if (is_array($results) == false)
			{

				return (array)$results;
			}

			return (array)$results[0];
		}

		/**
		 * Gets the users installed collector
		 *
		 * @return null
		 */

		public function getInstalledCollector()
		{

			$computer = new Computer();

			$software = new Software();

			if ($computer->userHasComputers($this->session->userid()) == false)
			{

				throw new SyscrackException();
			}

			$computersoftware = $computer->getComputerSoftware($computer->computerid());

			$results = [];

			foreach ($computersoftware as $csoftware)
			{

				if ($csoftware['type'] == Settings::setting('syscrack_software_collector_type'))
				{

					if ($csoftware['installed'] == true)
					{

						if ($software->softwareExists($csoftware['softwareid']) == false)
						{

							continue;
						}

						$results[] = $software->getSoftware($csoftware['softwareid']);
					}
				}
			}

			if (empty($results))
			{

				return null;
			}

			$results = ArrayHelper::sortArray($results, 'level');

			if (is_array($results) == false)
			{

				return (array)$results;
			}

			return (array)$results[0];
		}

		/**
		 * Gets the users installed hasher
		 *
		 * @return null
		 */

		public function getInstalledHasher()
		{

			$computer = new Computer();

			$software = new Software();

			if ($computer->userHasComputers($this->session->userid()) == false)
			{

				throw new SyscrackException();
			}

			$computersoftware = $computer->getComputerSoftware($computer->computerid());

			$results = [];

			foreach ($computersoftware as $csoftware)
			{

				if ($csoftware['type'] == Settings::setting('syscrack_software_hasher_type'))
				{

					if ($csoftware['installed'] == true)
					{

						if ($software->softwareExists($csoftware['softwareid']) == false)
						{

							continue;
						}

						$results[] = $software->getSoftware($csoftware['softwareid']);
					}
				}
			}

			if (empty($results))
			{

				return null;
			}

			$results = ArrayHelper::sortArray($results, 'level');

			if (is_array($results) == false)
			{

				return (array)$results;
			}

			return (array)$results[0];
		}

		/**
		 * Gets the users installed firewall
		 *
		 * @return null
		 */

		public function getInstalledFirewall()
		{

			$computer = new Computer();

			$software = new Software();

			if ($computer->userHasComputers($this->session->userid()) == false)
			{

				throw new SyscrackException();
			}

			$computersoftware = $computer->getComputerSoftware($computer->computerid());

			$results = [];

			foreach ($computersoftware as $csoftware)
			{

				if ($csoftware['type'] == Settings::setting('syscrack_software_firewall_type'))
				{

					if ($csoftware['installed'] == true)
					{

						if ($software->softwareExists($csoftware['softwareid']) == false)
						{

							continue;
						}

						$results[] = $software->getSoftware($csoftware['softwareid']);
					}
				}
			}

			if (empty($results))
			{

				return null;
			}

			$results = ArrayHelper::sortArray($results, 'level');

			if (is_array($results) == false)
			{

				return (array)$results;
			}

			return (array)$results[0];
		}

		/**
		 * Gets the users installed cracker
		 *
		 * @return null
		 */

		public function getInstalledCracker()
		{

			$computer = new Computer();

			$software = new Software();

			if ($computer->userHasComputers($this->session->userid()) == false)
			{

				throw new SyscrackException();
			}

			$computersoftware = $computer->getComputerSoftware($computer->computerid());

			$results = [];

			foreach ($computersoftware as $csoftware)
			{

				if ($csoftware['type'] == Settings::setting('syscrack_software_cracker_type'))
				{

					if ($csoftware['installed'] == true)
					{

						if ($software->softwareExists($csoftware['softwareid']) == false)
						{

							continue;
						}

						$results[] = $software->getSoftware($csoftware['softwareid']);
					}
				}
			}

			if (empty($results))
			{

				return null;
			}

			$results = ArrayHelper::sortArray($results, 'level');

			if (is_array($results) == false)
			{

				return (array)$results;
			}

			return (array)$results[0];
		}

		/**
		 * Gets a perticular software level
		 *
		 * @param $softwareid
		 *
		 * @return mixed
		 */

		public function getSoftwareLevel($softwareid)
		{

			$software = new Software();

			if ($software->softwareExists($softwareid) == false)
			{

				throw new SyscrackException();
			}

			return $software->getSoftware($softwareid)->level;
		}

		/**
		 * Gets the total cash of a user
		 *
		 * @return int
		 */

		public function getCash()
		{

			$finance = new Finance();

			if ($finance->hasAccount($this->session->userid()) == false)
			{

				return number_format(0.0);
			}

			return number_format($finance->getTotalUserCash($this->session->userid()));
		}

		/**
		 * Gets the raw cash int value
		 *
		 * @return int
		 */

		public function getRawCashValue()
		{

			$finance = new Finance();

			if ($finance->hasAccount($this->session->userid()) == false)
			{

				return 0;
			}

			return $finance->getTotalUserCash($this->session->userid());
		}

		/**
		 * Gets the username of the user
		 *
		 * @return string
		 */

		public function getUsername()
		{

			$user = new User();

			if ($user->userExists($this->session->userid()) == false)
			{

				throw new SyscrackException();
			}

			return $user->getUsername($this->session->userid());
		}
	}
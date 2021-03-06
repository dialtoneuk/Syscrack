<?php /** @noinspection PhpUnusedLocalVariableInspection */
	declare(strict_types=1);

	namespace Framework\Syscrack\Game\Bases;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class BaseComputer
	 *
	 * @package Framework\Syscrack\Game\Bases
	 */

	use Framework\Application\Settings;
	use Framework\Application\UtilitiesV2\Conventions\ComputerData;

	use Framework\Syscrack\Game\Computer as ComputerController;
	use Framework\Syscrack\Game\Internet;
	use Framework\Syscrack\Game\Log;
	use Framework\Syscrack\Game\Metadata;
	use Framework\Syscrack\Game\Software;
	use Framework\Syscrack\Game\Interfaces\Computer as Structure;
	use Framework\Syscrack\Game\Tab;
	use Framework\Syscrack\Game\Tabs;

	/**
	 * Class BaseComputer
	 * @package Framework\Syscrack\Game\Bases
	 */
	class BaseComputer implements Structure
	{

		/**
		 * @var string
		 */

		protected $self;

		/**
		 * @var ComputerController
		 */

		protected static $computer;

		/**
		 * @var BaseSoftware
		 */

		protected static $software;

		/**
		 * @var Internet
		 */

		protected static $internet;

		/**
		 * @var Log
		 */

		protected static $log;

		/**
		 * @var Metadata
		 */

		protected static $metadata;


		/**
		 * BaseComputer constructor.
		 *
		 * @param bool $createclasses
		 */

		public function __construct($createclasses = true)
		{

			if (isset(self::$metadata) == false)
				self::$metadata = new Metadata();

			if ($createclasses == true)
			{

				if ($createclasses && isset(self::$computer) == false)
					self::$computer = new ComputerController();
				if ($createclasses && isset(self::$software) == false)
					self::$software = new Software();
				if ($createclasses && isset(self::$internet) == false)
					self::$internet = new Internet();
				if ($createclasses && isset(self::$log) == false)
					self::$log = new Log();
			}

			if( isset( $this->self ) == false )
				$this->self = get_called_class();
		}

		/**
		 * @return array
		 */

		public function configuration()
		{

			return ([]);
		}

		/**
		 * @param $computerid
		 * @param $userid
		 *
		 * @return array
		 */

		public function data($computerid, $userid)
		{

			return ([]);
		}

		/**
		 * @param $computerid
		 * @param $ipaddress
		 *
		 * @return bool
		 */

		public function onLogin($computerid, $ipaddress)
		{

			//TODO: Code for login executed softwares go here

			return (true);
		}

		/**
		 * @param $computerid
		 * @param $ipaddress
		 *
		 * @return bool
		 */

		public function onLogout($computerid, $ipaddress)
		{


			//TODO: Code for logout executed softwares go here

			return (true);
		}

		/**
		 * @return Metadata
		 */

		public function metadata(): Metadata
		{

			return (self::$metadata);
		}

		/**
		 * @param $computerid
		 * @param ComputerData $metadata
		 */

		private function metareload($computerid, ComputerData $metadata)
		{

			$this->addHardwares($computerid, $metadata->hardware);
			$this->addSoftware($computerid, null, $metadata->software);

			$array = $metadata->info;
			$array["reset"] = microtime(true);
			$this->metadata()->update($computerid, ["info" => $array]);
		}

		/**
		 * @return mixed
		 */

		public function hasData()
		{

			return (@$this->configuration()["data"]);
		}


		/**
		 * Adds the software
		 *
		 * @param $computerid
		 *
		 * @param int $userid
		 *
		 * @param array $softwares
		 */

		public function addSoftware($computerid, $userid = null, array $softwares = [])
		{

			if ($userid == null)
				$userid = Settings::setting("user");

			foreach ($softwares as $software)
			{

				if (isset($software['uniquename']) == false)
					continue;

				$class = self::$software->findSoftwareByUniqueName($software['uniquename']);

				if ($class == null)
					continue;

				$name = self::$software->getNameFromClass($class);

				if (isset($software['data']) == false)
					$software['data'] = [];

				$softwareid = self::$software->createSoftware(
					$name,
					$userid,
					$computerid,
					$software['name'],
					$software['level'],
					$software['size'],
					$software['data']);

				self::$computer->addSoftware(
					$computerid,
					$softwareid,
					self::$software->getSoftwareType($name)
				);


				if (isset($software['installed']) && $software['installed'])
				{

					self::$computer->installSoftware($computerid, $softwareid);
					self::$software->installSoftware($softwareid, $userid);
				}
			}
		}

		/**
		 * @param $computerid
		 */

		public function onReset($computerid)
		{

			$this->clearSoftware($computerid);
			self::$computer->resetHardware($computerid);

			if (self::$log->hasLog($computerid))
				self::$log->saveLog($computerid, []);

			if ($this->metadata()->exists($computerid))
				$this->metareload($computerid, $this->metadata()->get($computerid));
			else
				$this->addHardwares($computerid, Settings::setting('default_hardware'));
		}

		/**
		 * @param $computerid
		 * @param $userid
		 * @param array $software
		 * @param array $hardware
		 * @param array $custom
		 */

		public function onStartup($computerid, $userid, array $software = [], array $hardware = [], array $custom = [])
		{

			if (self::$log->hasLog($computerid) == false)
				self::$log->createLog($computerid);

			$this->addSoftware($computerid, $userid, $software);
			$this->addHardwares($computerid, $hardware);

			$this->metadata()->create($computerid, Metadata::generateData("Computer_" . $computerid, $this->configuration()["type"], $software, $hardware, $custom));
		}

		/**
		 * @param null $userid
		 * @param null $sofwareid
		 * @param null $computerid
		 * @param array $tabs
		 *
		 * @return Tabs
		 */

		public function tab($userid = null, $sofwareid = null, $computerid = null, $tabs = []): Tabs
		{

			$tabs       = new Tabs( $tabs );
			$sofware    = &self::$software;
			$computer   = &self::$computer;
			$interent   = &self::$internet;
			$log        = &self::$log;

			$tabhardware = new Tab('hardware', false );
			$tabhardware->postMethod(function( $computerid, $userid ){
				return( true );
			});
			$tabhardware->dataMethod(function( $computerid, $userid ) use( $computer ){

				$icons      = Settings::setting("hardware_icons");
				$hardware   = $computer->getComputerHardware( $computerid );

				return( ['icons' => $icons, 'hardware' => $hardware]);
			});
			$tabhardware->bypass();
			$tabhardware->render("syscrack/tabs/tab.hardware");

			$tabsoftware = new Tab('software', false );
			$tabsoftware->bypass();
			$tabsoftware->render("syscrack/tabs/tab.softwares");

			$tabstorage = new Tab('storage', false );
			$tabstorage->bypass();
			$tabstorage->render("syscrack/tabs/tab.storage");

			$tablog = new Tab('log', false );
			$tablog->bypass();
			$tablog->render("syscrack/tabs/tab.log");

			$tabhistory = new Tab('history', true );
			$tabhistory->postMethod(function( $computerid, $userid ){
				return( true );
			});
			$tabhistory->dataMethod(function( $computerid, $userid ){
				//Nothing
			});

			$tabs->add( $tabhardware );
			$tabs->add( $tabsoftware );
			$tabs->add( $tablog );
			$tabs->add( $tabhistory );

			return( $tabs );
		}

		/**
		 * Clears the software
		 *
		 * @param $computerid
		 */

		public function clearSoftware($computerid)
		{

			$softwares = self::$computer->getComputerSoftware($computerid);

			foreach ($softwares as $software)
			{
				if (self::$software->softwareExists($software['softwareid']))
					self::$software->deleteSoftware($software['softwareid']);

				self::$computer->removeSoftware($computerid, $software['softwareid']);
			}
		}

		/**
		 * Sets the computer hardware
		 *
		 * @param $computerid
		 *
		 * @param array $hardware
		 */

		public function setHardwares($computerid, array $hardware)
		{

			self::$computer->setHardware($computerid, $hardware);
		}

		/**
		 * Adds a hardware to the computer
		 *
		 * @param $computerid
		 *
		 * @param array $hardwares
		 */

		public function addHardwares($computerid, array $hardwares)
		{

			$hardware = self::$computer->getComputerHardware($computerid);

			foreach ($hardwares as $item => $value)
			{

				if (isset($hardware[$item]))
					continue;

				$hardware[$item] = $value;
			}

			$this->setHardwares($computerid, $hardware);
		}

		/**
		 * @param $computerid
		 * @param $message
		 * @param $ipaddress
		 */

		public function log($computerid, $message, $ipaddress)
		{

			self::$log->updateLog($message, $computerid, $ipaddress);
		}

		/**
		 * @param $ipaddress
		 * @param $message
		 */

		public function logRemote($ipaddress, $message)
		{

			$computer = self::$internet->computer($ipaddress);

			if ($computer == null)
				throw new \Error();

			$this->log($computer->computerid, $message, Settings::setting('log_localhost_address'));
		}

		/**
		 * @return mixed
		 */

		public function localhost()
		{

			return self::$computer->getComputer(self::$computer->computerid())->ipaddress;
		}

		/**
		 * @param $computerid
		 *
		 * @return mixed
		 */

		public function getComputerOwner($computerid)
		{

			return self::$computer->getComputer($computerid)->userid;
		}
	}
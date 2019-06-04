<?php

	namespace Framework\Syscrack\Game;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class Software
	 *
	 * @package Framework\Syscrack\Game
	 *
	 * //TODO: On rewrite, try use classes as return variables instead of booleans in order to display more detailed
	 * information
	 */

	use Framework\Application\UtilitiesV2\Container;
	use Framework\Application\Settings;
	use Framework\Application\Utilities\Factory;
	use Framework\Application\Utilities\FileSystem;
	use Framework\Database\Tables\Software as Database;
	use Framework\Exceptions\SyscrackException;
	use Framework\Syscrack\Game\Bases\BaseSoftware;
	use Framework\Syscrack\Game\Interfaces\Software as Structure;

	class Software
	{

		/**
		 * @var Factory
		 *
		 * Since theres an issue with the software forever in a loop loading the software classes, this static variable
		 * holds these classes so we don't reload them
		 *
		 * TODO: rewrite this so this isn't a problem
		 */

		protected static $factory;

		/**
		 * @var Database
		 */

		protected static $database;

		/**
		 * Software constructor.
		 *
		 * @param bool $autoload
		 */

		public function __construct($autoload = true)
		{

			if (isset(self::$database) == false)
				self::$database = new Database();

			if ($autoload)
				if (empty(self::$factory))
				{

					self::$factory = new Factory(Settings::setting('syscrack_software_namespace'));
					$this->loadSoftware();
				}
		}

		/**
		 * Loads all the software classes into the factory
		 */

		private function loadSoftware()
		{

			$software = FileSystem::getFilesInDirectory(Settings::setting('syscrack_software_location'));

			foreach ($software as $softwares)
			{


				if (self::$factory->hasClass(FileSystem::getFileName($softwares)))
					continue;

				self::$factory->createClass(FileSystem::getFileName($softwares));
			}
		}

		/**
		 * @return array
		 */

		public function tools()
		{

			$tools = [];
			$classes = self::$factory->getAllClasses();

			/**
			 * @var $class BaseSoftware
			 */

			foreach ($classes as $key => $class)
				$tools[$key] = $class->tool();

			return ($tools);
		}

		/**
		 * @param $computerid
		 *
		 * @return array
		 */

		public function getAnonDownloads($computerid)
		{

			$softwares = self::$database->getByComputer($computerid);
			$results = [];

			foreach ($softwares as $software)
			{

				$data = json_decode($software->data, true);

				if (isset($data["allowanondownloads"]) && $data["allowanondownloads"] == true)
					$results[] = $software;
			}

			return $results;
		}

		/**
		 * Returns true if this softwareid exists
		 *
		 * @param $softwareid
		 *
		 * @return bool
		 */

		public function softwareExists($softwareid)
		{

			if ($softwareid == null)
				return false;


			if (self::$database->getSoftware($softwareid) == null)
				return false;

			return true;
		}

		/**
		 * Gets the software class related to this software id
		 *
		 * @param $softwareid
		 *
		 * @return Structure
		 */

		public function getSoftwareClassFromID($softwareid)
		{

			return $this->findSoftwareByUniqueName(self::$database->getSoftware($softwareid)->uniquename);
		}

		/**
		 * Finds a software class by its unique name
		 *
		 * @param $uniquename
		 *
		 * @return Structure
		 */

		public function findSoftwareByUniqueName($uniquename)
		{

			$classes = self::$factory->getAllClasses();

			foreach ($classes as $key => $class)
			{

				if ($class instanceof Structure == false)
				{

					throw new SyscrackException();
				}

				/** @var Structure $class */
				if ($class->configuration()['uniquename'] == $uniquename)
				{

					return $class;
				}
			}

			return null;
		}

		/**
		 * Gets all the licensed software on a computer
		 *
		 * @param $computerid
		 *
		 * @return array
		 */

		public function getLicensedSoftware($computerid)
		{

			$softwares = $this->getSoftwareOnComputer($computerid);

			$results = [];

			foreach ($softwares as $software)
			{

				$data = json_decode($software->data, true);

				if (isset($data['license']))
				{

					$results[] = $software;
				}
			}

			return $results;
		}

		/**
		 * Licenses a software
		 *
		 * @param $softwareid
		 *
		 * @param $userid
		 */

		public function licenseSoftware($softwareid, $userid)
		{

			$data = $this->getSoftwareData($softwareid);

			if (isset($data['license']))
			{

				if ($data['license'] == $userid)
				{

					throw new SyscrackException();
				}
			}

			$data['license'] = $userid;

			$this->updateData($softwareid, $data);
		}

		/**
		 * Unlicenses a software
		 *
		 * @param $softwareid
		 */

		public function unlicenseSoftware($softwareid)
		{

			$data = $this->getSoftwareData($softwareid);

			if (isset($data['license']) == false)
			{

				throw new SyscrackException();
			}

			unset($data['license']);

			$this->updateData($softwareid, $data);
		}

		/**
		 * Returns true if this software has a license
		 *
		 * @param $softwareid
		 *
		 * @return bool
		 */

		public function hasLicense($softwareid)
		{

			$data = $this->getSoftwareData($softwareid);

			if (isset($data['license']) == false)
			{

				return false;
			}

			return true;
		}

		/**
		 * Deletes the software
		 *
		 * @param $softwareid
		 */

		public function deleteSoftware($softwareid)
		{

			self::$database->deleteSoftware($softwareid);
		}

		/**
		 * Deletes all the software related to a computer id
		 *
		 * @param $computerid
		 */

		public function deleteSoftwareByComputer($computerid)
		{

			self::$database->deleteSoftwareByComputer($computerid);
		}

		/**
		 * @param $softwareid
		 * @param null $time
		 */

		public function updateLastModified($softwareid, $time = null)
		{

			if ($time === null)
				$time = microtime(true);

			self::$database->updateSoftware($softwareid, array("lastmodified" => $time));
		}

		/**
		 * Creates a new software
		 *
		 * @param $software
		 *
		 * @param int $userid
		 *
		 * @param int $computerid
		 *
		 * @param string $softwarename
		 *
		 * @param float $softwarelevel
		 *
		 * @param float $softwareize
		 *
		 * @param array $data
		 *
		 * @return int
		 */

		public function createSoftware($software, int $userid, int $computerid, string $softwarename = 'My Software', float $softwarelevel = 1.0, float $softwareize = 10.0, $data = [])
		{

			if ($this->hasSoftwareClass($software) == false)
			{

				throw new SyscrackException();
			}

			$class = $this->getSoftwareClass($software);

			if ($class instanceof Structure == false)
			{

				throw new SyscrackException();
			}

			$configuration = $class->configuration();

			$array = array(
				'userid' => $userid,
				'computerid' => $computerid,
				'level' => $softwarelevel,
				'size' => $softwareize,
				'uniquename' => $configuration['uniquename'],
				'type' => $configuration['type'],
				'softwarename' => $softwarename,
				'lastmodified' => time(),
				'installed' => false,
				'data' => json_encode($data)
			);

			return self::$database->insertSoftware($array);
		}

		/**
		 * Copys a software from one computer to the other
		 *
		 * @param $targetid
		 *
		 * @param $computerid
		 *
		 * @param $userid
		 *
		 * @param bool $installed
		 *
		 * @param array $data
		 *
		 * @return int
		 */

		public function copySoftware($targetid, $computerid, $userid, $installed = false, array $data = [])
		{

			$software = self::$database->getSoftware($targetid);

			$array = array(
				'userid' => $userid,
				'computerid' => $computerid,
				'level' => $software->level,
				'size' => $software->size,
				'uniquename' => $software->uniquename,
				'type' => $software->type,
				'softwarename' => $software->softwarename,
				'lastmodified' => time(),
				'installed' => $installed,
				'data' => json_encode($data)
			);

			return self::$database->insertSoftware($array);
		}

		/**
		 * Returns true if we have this software class in our factory
		 *
		 * @param $software
		 *
		 * @return bool
		 */

		public function hasSoftwareClass($software)
		{

			if (self::$factory->hasClass($software) == false)
			{

				return false;
			}

			return true;
		}

		/**
		 * @return array|Interfaces\Computer|Structure|\stdClass
		 */

		public function getAllClasses()
		{

			return( self::$factory->getAllClasses() );
		}

		/**
		 * Gets the software class, which is used when processing what a software actually does
		 *
		 * @param $software
		 *
		 * @return Structure
		 */

		public function getSoftwareClass($software)
		{

			return self::$factory->findClass($software);
		}

		/**
		 * Gets the software name from the software ID
		 *
		 * @param $softwareid
		 *
		 * @return int|null|string
		 */

		public function getSoftwareNameFromSoftwareID($softwareid)
		{

			return $this->getNameFromClass($this->findSoftwareByUniqueName($this->getSoftware($softwareid)->uniquename));
		}

		/**
		 * Returns the name of the software from class
		 *
		 * @param $softwareclass
		 *
		 * @return int|null|string
		 */

		public function getNameFromClass($softwareclass)
		{

			$factory = self::$factory->getAllClasses();

			foreach ($factory as $key => $value)
			{

				if ($value == $softwareclass)
				{

					return $key;
				}
			}

			return null;
		}

		/**
		 * Gets the software from the database
		 *
		 * @param $softwareid
		 *
		 * @return mixed|null
		 */

		public function getSoftware($softwareid)
		{

			return self::$database->getSoftware($softwareid);
		}

		/**
		 * Gets all of the viruses currently installed on the computer
		 *
		 * @param $computerid
		 *
		 * @return \Illuminate\Support\Collection|null
		 */

		public function getVirusesOnComputer($computerid)
		{

			return self::$database->getTypeOnComputer(Settings::setting('syscrack_software_virus_type'), $computerid);
		}

		/**
		 * Gets all the software tied to a computer id
		 *
		 * @param $computerid
		 *
		 * @return \Illuminate\Support\Collection|null
		 */

		public function getSoftwareOnComputer($computerid)
		{

			return self::$database->getByComputer($computerid);
		}

		/**
		 * @param $softwareid
		 * @param $userid
		 */

		public function installSoftware($softwareid, $userid)
		{

			$array = array(
				'installed' => true,
				'userid' => $userid
			);

			self::$database->updateSoftware($softwareid, $array);
		}

		/**
		 * Uninstalls a software
		 *
		 * @param $softwareid
		 */

		public function uninstallSoftware($softwareid)
		{

			$array = array(
				'installed' => false
			);

			self::$database->updateSoftware($softwareid, $array);
		}

		/**
		 * Updates a software data
		 *
		 * @param $softwareid
		 *
		 * @param array $data
		 */

		public function updateData($softwareid, array $data)
		{

			$array = array(
				'data' => json_encode($data)
			);

			self::$database->updateSoftware($softwareid, $array);
		}

		/**
		 * Returns true if this software is installable
		 *
		 * @param $softwareid
		 *
		 * @return bool
		 */

		public function canInstall($softwareid)
		{

			$software = self::$database->getSoftware($softwareid);

			if ($software == null)
			{

				throw new SyscrackException();
			}

			$softwareclass = $this->findSoftwareByUniqueName($software->uniquename);

			if (isset($softwareclass->configuration()['installable']) == false)
			{

				return true;
			}

			if ($softwareclass->configuration()['installable'] == false)
			{

				return false;
			}

			return true;
		}

		/**
		 * Returns true if this software cannot be uninstalled
		 *
		 * @param $softwareid
		 *
		 * @return bool
		 */

		public function canUninstall($softwareid)
		{

			$software = self::$database->getSoftware($softwareid);

			if ($software == null)
			{

				throw new SyscrackException();
			}

			$softwareclass = $this->findSoftwareByUniqueName($software->uniquename);

			if (isset($softwareclass->configuration()['uninstallable']) == false)
			{

				return true;
			}

			if ($softwareclass->configuration()['uninstallable'] == true)
			{

				return false;
			}

			return true;
		}

		/**
		 * If the software is uneditable, if viewable is equal to true, then the user will
		 * still be allowed to view this software
		 *
		 * @param $softwareid
		 *
		 * @return bool
		 */

		public function canView($softwareid)
		{

			$software = self::$database->getSoftware($softwareid);

			if ($software == null)
			{

				throw new SyscrackException();
			}

			$softwareclass = $this->findSoftwareByUniqueName($software->uniquename);

			if (isset($softwareclass->configuration()['viewable']) == false)
			{

				return false;
			}

			if ($softwareclass->configuration()['viewable'] == false)
			{

				return false;
			}

			return true;
		}

		/**
		 * Returns true if this software can be removed
		 *
		 * @param $softwareid
		 *
		 * @return bool
		 */

		public function canRemove($softwareid)
		{

			$software = self::$database->getSoftware($softwareid);

			if ($software == null)
			{

				throw new SyscrackException();
			}

			$softwareclass = $this->findSoftwareByUniqueName($software->uniquename);

			if (isset($softwareclass->configuration()['removable']) == false)
			{

				return true;
			}

			if ($softwareclass->configuration()['removable'] == false)
			{

				return false;
			}

			return true;
		}

		/**
		 * Returns true if we keep the data on downloads and uploads
		 *
		 * @param $softwareid
		 *
		 * @return bool
		 */

		public function keepData($softwareid)
		{

			$software = self::$database->getSoftware($softwareid);

			if ($software == null)
			{

				throw new SyscrackException();
			}

			$softwareclass = $this->findSoftwareByUniqueName($software->uniquename);

			if (isset($softwareclass->configuration()['keepdata']) == false)
			{

				return false;
			}

			if ($softwareclass->configuration()['keepdata'] == false)
			{

				return false;
			}

			return true;
		}

		/**
		 * Returns true if a software is executable
		 *
		 * @param $softwareid
		 *
		 * @return bool
		 */

		public function canExecute($softwareid)
		{

			$software = self::$database->getSoftware($softwareid);

			if ($software == null)
			{

				throw new SyscrackException();
			}

			$softwareclass = $this->findSoftwareByUniqueName($software->uniquename);

			if (isset($softwareclass->configuration()['executable']) == false)
			{

				return true;
			}

			if ($softwareclass->configuration()['executable'] == false)
			{

				return false;
			}

			return true;
		}

		/**
		 * Returns true if this software can only be executed locally
		 *
		 * @param $softwareid
		 *
		 * @return bool
		 */

		public function localExecuteOnly($softwareid)
		{

			$software = self::$database->getSoftware($softwareid);

			if ($software == null)
			{

				throw new SyscrackException();
			}

			$softwareclass = $this->findSoftwareByUniqueName($software->uniquename);

			if (isset($softwareclass->configuration()['localexecuteonly']) == false)
			{

				return false;
			}

			if ($softwareclass->configuration()['localexecuteonly'] == false)
			{

				return false;
			}

			return true;
		}

		/**
		 * Returns true if the software can be edited
		 *
		 * @param $softwareid
		 *
		 * @return bool
		 */

		public function isEditable($softwareid)
		{

			$data = $this->getSoftwareData($softwareid);

			if (empty($data))
			{

				return true;
			}

			if (isset($data['editable']) == false)
			{

				return true;
			}

			if ($data['editable'] == false)
			{

				return false;
			}

			return true;
		}

		/**
		 * Returns true if this software is an anon download software
		 *
		 * @param $softwareid
		 *
		 * @return bool
		 */

		public function isAnonDownloadSoftware($softwareid)
		{

			if ($this->hasData($softwareid) == false)
			{

				return false;
			}

			$data = $this->getSoftwareData($softwareid);

			if (isset($data['allowanondownloads']) == false)
			{

				return false;
			}

			if (is_bool($data['allowanondownloads']) == false)
			{

				throw new SyscrackException();
			}

			return $data['allowanondownloads'];
		}

		/**
		 * Returns true if the software has an icon
		 *
		 * @param $softwareid
		 *
		 * @return bool
		 */

		public function hasIcon($softwareid)
		{

			$software = self::$database->getSoftware($softwareid);

			if ($software == null)
			{

				throw new SyscrackException();
			}

			$softwareclass = $this->findSoftwareByUniqueName($software->uniquename);

			if (empty($softwareclass->configuration()))
			{

				return false;
			}

			if (isset($softwareclass->configuration()['icon']) == false)
			{

				return false;
			}

			if (empty($softwareclass->configuration()['icon']))
			{

				return false;
			}

			return true;
		}

		/**
		 * Gets the software icon
		 *
		 * @param $softwareid
		 *
		 * @return bool
		 */

		public function getIcon($softwareid)
		{

			$software = self::$database->getSoftware($softwareid);

			if ($software == null)
			{

				throw new SyscrackException();
			}

			$softwareclass = $this->findSoftwareByUniqueName($software->uniquename);

			if (empty($softwareclass->configuration()))
			{

				throw new SyscrackException();
			}

			if (isset($softwareclass->configuration()['icon']) == false)
			{

				return Settings::setting('syscrack_software_default_icon');
			}

			return $softwareclass->configuration()['icon'];
		}

		/**
		 * @param $software
		 *
		 * @return mixed
		 */

		public function getSoftwareType($software)
		{

			return $this->getSoftwareClass($software)->configuration()['type'];
		}

		/**
		 * @param $software
		 *
		 * @return mixed
		 */
		public function getSoftwareExtension($software)
		{

			return $this->getSoftwareClass($software)->configuration()['extension'];
		}

		/**
		 * Gets the software unique name
		 *
		 * @param $software
		 *
		 * @return mixed
		 */

		public function getSoftwareUniqueName($software)
		{

			return $this->getSoftwareClass($software)->configuration()['unqiuename'];
		}

		/**
		 * Returns wether the software is installable or not
		 *
		 * @param $software
		 *
		 * @return mixed
		 */

		public function getSoftwareInstallable($software)
		{

			return $this->getSoftwareClass($software)->configuration()['installable'];
		}

		/**
		 * Returns true if this data is set
		 *
		 * @param $softwareid
		 *
		 * @return bool
		 */

		public function hasData($softwareid)
		{

			if ($this->getSoftwareData($softwareid) == null)
			{

				return false;
			}

			return true;
		}

		/**
		 * Gets the software's data
		 *
		 * @param $softwareid
		 *
		 * @return mixed
		 */

		public function getSoftwareData($softwareid)
		{

			return json_decode(self::$database->getSoftware($softwareid)->data, true);
		}

		/**
		 * Checks the software data
		 *
		 * @param $softwareid
		 *
		 * @param array $requirements
		 *
		 * @return bool
		 */

		public function checkSoftwareData($softwareid, array $requirements = ['text'])
		{

			$data = $this->getSoftwareData($softwareid);

			foreach ($requirements as $requirement)
			{

				if (isset($data[$requirement]) == false)
				{

					return false;
				}
			}

			return true;
		}

		/**
		 * Executes a method inside the given software class
		 *
		 * @param $software
		 *
		 * @param string $method
		 *
		 * @param array $parameters
		 *
		 * @return mixed|null
		 */

		public function executeSoftwareMethod($software, $method = 'onExecute', array $parameters = [])
		{

			$software = $this->getSoftwareClass($software);

			try
			{

				if ($this->isCallable($software, $method) == false)
				{

					return null;
				}
			}
			catch (\ReflectionException $exception)
			{

				Container::get('application')->getErrorHandler()->handleError( $exception );
			}

			if (empty($parameters) == false)
			{

				return call_user_func_array(array($software, $method), $parameters);
			}

			return $software->{$method}();
		}

		/**
		 * @param Structure $software
		 * @param string $method
		 *
		 * @return bool
		 * @throws \ReflectionException
		 */

		private function isCallable(Structure $software, string $method)
		{

			$requirements = Settings::setting('syscrack_software_allowedmethods');

			if (isset($requirements[$method]))
			{

				throw new SyscrackException('Method is not in the allowed callable methods');
			}

			$software = new \ReflectionClass($software);

			if ($software->getMethod($method)->isPublic() == false)
			{

				return false;
			}

			return true;
		}

		/**
		 * Returns true if this software is installed
		 *
		 * @param $softwareid
		 *
		 * @param $computerid
		 *
		 * @return bool
		 */

		public function isInstalled($softwareid, $computerid)
		{

			if ($this->getSoftware($softwareid)->computerid !== $computerid)
			{

				return false;
			}

			if ($this->getSoftware($softwareid)->installed == false)
			{

				return false;
			}

			return true;
		}
	}
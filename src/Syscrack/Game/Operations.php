<?php

	namespace Framework\Syscrack\Game;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class Operations
	 *
	 * @package Framework\Syscrack\Game
	 */

	use Framework\Application\Settings;
	use Framework\Application\Utilities\Factory;
	use Framework\Application\Utilities\FileSystem;
	use Framework\Database\Tables\Processes as Database;
	use Framework\Exceptions\SyscrackException;
	use Framework\Syscrack\Game\BaseClasses\BaseOperation;
	use Framework\Syscrack\Game\Structures\Operation;

	class Operations
	{

		/**
		 * @var Factory
		 */

		protected $factory;

		/**
		 * @var Database
		 */

		protected $database;

		/**
		 * @var Hardware
		 */

		protected $hardware;

		/**
		 * Processes constructor.
		 *
		 * @param bool $autoload
		 */

		public function __construct($autoload = true)
		{

			$this->factory = new Factory("Framework\\Syscrack\\Game\\Operations\\");

			$this->database = new Database();

			if ($autoload)
			{

				$this->getProcessesClasses();
			}
		}

		/**
		 * Returns true if a process exists
		 *
		 * @param $processid
		 *
		 * @return bool
		 */

		public function processExists($processid)
		{

			if ($this->database->getProcess($processid) == null)
			{

				return false;
			}

			return true;
		}

		/**
		 * Gets a process
		 *
		 * @param $processid
		 *
		 * @return \Illuminate\Support\Collection|null
		 */

		public function getProcess($processid)
		{

			return $this->database->getProcess($processid);
		}

		/**
		 * Gets all of the users processes
		 *
		 * @param $userid
		 *
		 * @return \Illuminate\Support\Collection|null
		 */

		public function getUserProcesses($userid)
		{

			return $this->database->getUserProcesses($userid);
		}

		/**
		 * Gets the processes of a computer
		 *
		 * @param $computerid
		 *
		 * @return \Illuminate\Support\Collection|null
		 */

		public function getComputerProcesses($computerid)
		{

			return $this->database->getComputerProcesses($computerid);
		}

		/**
		 * Returns true if we already have a process like this on a users computer
		 *
		 * @param $computerid
		 *
		 * @param $process
		 *
		 * @return bool
		 */

		public function hasProcess($computerid, $process, $ipaddress, $softwareid = null)
		{

			$processes = $this->getComputerProcesses($computerid);

			if (empty($processes))
			{

				return false;
			}

			foreach ($processes as $key => $value)
			{

				if ($value->process == $process)
				{

					$data = json_decode($value->data, true);

					if ($data['ipaddress'] == $ipaddress)
					{

						if ($softwareid !== null)
						{

							if (isset($data['softwareid']) == false)
							{

								return false;
							}

							if ($data['softwareid'] == $softwareid)
							{

								return true;
							}
						}

						return true;
					}
				}
			}

			return false;
		}

		/**
		 * Creates a new process and adds it to the database
		 *
		 * @param $timecompleted
		 *
		 * @param $computerid
		 *
		 * @param $userid
		 *
		 * @param $process
		 *
		 * @param array $data
		 *
		 * @return int
		 */

		public function createProcess($timecompleted, $computerid, $userid, $process, array $data)
		{

			if ($this->findProcessClass($process) == false)
			{

				throw new SyscrackException();
			}

			return $this->addToDatabase($timecompleted, $computerid, $userid, $process, $data);
		}

		/**
		 * Returns true if the process can be completed
		 *
		 * @param $processid
		 *
		 * @return bool
		 */

		public function canComplete($processid)
		{

			$process = $this->database->getProcess($processid);

			if (time() - $process->timecompleted < 0)
			{

				return false;
			}

			return true;
		}

		/**
		 * Adds a process to the database
		 *
		 * @param $timecompleted
		 *
		 * @param $computerid
		 *
		 * @param $userid
		 *
		 * @param $process
		 *
		 * @param array $data
		 *
		 * @return int
		 */

		public function addToDatabase($timecompleted, $computerid, $userid, $process, array $data)
		{

			$array = array(
				'timecompleted' => $timecompleted,
				'timestarted' => time(),
				'computerid' => $computerid,
				'userid' => $userid,
				'process' => $process,
				'data' => json_encode($data)
			);

			return $this->database->insertProcess($array);
		}

		/**
		 * Deletes a process
		 *
		 * @param $processid
		 */

		public function deleteProcess($processid)
		{

			$this->database->trashProcess($processid);
		}

		/**
		 * @param $processid
		 *
		 * @return mixed
		 */

		public function completeProcess($processid)
		{

			$process = $this->getProcess($processid);

			if (empty($process))
			{

				throw new SyscrackException();
			}

			$this->database->trashProcess($processid);

			$result = $this->callProcessMethod($this->findProcessClass($process->process), 'onCompletion', array(
				'timecompleted' => $process->timecompleted,
				'timestarted' => $process->timestarted,
				'computerid' => $process->computerid,
				'userid' => $process->userid,
				'process' => $process->process,
				'data' => json_decode($process->data, true)
			));

			return ($result);
		}

		/**
		 * Returns true if the user has processes
		 *
		 * @param $userid
		 *
		 * @return bool
		 */

		public function userHasProcesses($userid)
		{

			if ($this->database->getUserProcesses($userid) == null)
			{

				return false;
			}

			return true;
		}

		/**
		 * Returns true if the computer has processes
		 *
		 * @param $computerid
		 *
		 * @return bool
		 */

		public function computerHasProcesses($computerid)
		{

			if ($this->database->getComputerProcesses($computerid) == null)
			{

				return false;
			}

			return true;
		}

		/**
		 * Returns true if we have this process class
		 *
		 * @param $process
		 *
		 * @return bool
		 */

		public function hasProcessClass($process)
		{

			if ($this->factory->hasClass($process) == false)
			{

				return false;
			}

			return true;
		}

		/**
		 * Finds a process class
		 *
		 * @param $process
		 *
		 * @return BaseOperation
		 */

		public function findProcessClass($process)
		{

			return $this->factory->findClass($process);
		}

		/**
		 * @param $process
		 *
		 * @return bool
		 */

		public function isElevatedProcess($process)
		{

			/**
			 * @var $class BaseOperation
			 */
			$class = $this->findProcessClass($process);

			return ($class->isElevated());
		}

		/**
		 * Returns true if this operation allows software
		 *
		 * @param $process
		 *
		 * @return bool
		 */

		public function allowSoftware($process)
		{

			if ($this->hasProcessClass($process) == false)
			{

				throw new SyscrackException();
			}

			$class = $this->findProcessClass($process);

			if (isset($class->configuration()['allowsoftware']) == false)
			{

				return false;
			}

			if ($class->configuration()['allowsoftware'] == false)
			{

				return false;
			}

			return true;
		}

		/**
		 * Returns true if this process requires software
		 *
		 * @param $process
		 *
		 * @return bool
		 */

		public function requireSoftware($process)
		{

			if ($this->hasProcessClass($process) == false)
			{

				throw new SyscrackException();
			}

			$class = $this->findProcessClass($process);

			if (isset($class->configuration()['requiresoftware']) == false)
			{

				return false;
			}

			if ($class->configuration()['requiresoftware'] == false)
			{

				return false;
			}

			return true;
		}

		/**
		 * Returns true if the user is required to login in order to complete this process
		 *
		 * @param $process
		 *
		 * @return bool
		 */

		public function requireLoggedIn($process)
		{

			if ($this->hasProcessClass($process) == false)
			{

				throw new SyscrackException();
			}

			$class = $this->findProcessClass($process);

			if (isset($class->configuration()['requireloggedin']) == false)
			{

				return false;
			}

			if ($class->configuration()['requireloggedin'] == false)
			{

				return false;
			}

			return true;
		}

		/**
		 * Returns true if this operation allows post
		 *
		 * @param $process
		 *
		 * @return bool
		 */

		public function allowPost($process)
		{

			if ($this->hasProcessClass($process) == false)
			{

				throw new SyscrackException();
			}

			$class = $this->findProcessClass($process);

			if (isset($class->configuration()['allowpost']) == false)
			{

				return false;
			}

			if ($class->configuration()['allowpost'] == false)
			{

				return false;
			}

			return true;
		}

		/**
		 * Returns true if we allow custom data to be passed
		 *
		 * @param $process
		 *
		 * @return bool
		 */

		public function allowCustomData($process)
		{

			if ($this->hasProcessClass($process) == false)
			{

				throw new SyscrackException();
			}

			$class = $this->findProcessClass($process);

			if (isset($class->configuration()['allowcustomdata']) == false)
			{

				return false;
			}

			if ($class->configuration()['allowcustomdata'] == false)
			{

				return false;
			}

			return true;
		}

		/**
		 * Gets the custom data for this operation
		 *
		 * @param $process
		 *
		 * @return array
		 */

		public function getCustomData($process, $ipaddress, $userid)
		{

			if ($this->hasProcessClass($process) == false)
			{

				throw new SyscrackException();
			}

			$class = $this->findProcessClass($process);

			if (isset($class->configuration()['allowcustomdata']) == false)
			{

				throw new SyscrackException();
			}

			return $class->getCustomData($ipaddress, $userid);
		}

		/**
		 * Returns true if we have post requirements
		 *
		 * @param $process
		 *
		 * @return bool
		 */

		public function hasPostRequirements($process)
		{

			if ($this->hasProcessClass($process) == false)
			{

				throw new SyscrackException();
			}

			$class = $this->findProcessClass($process);

			if (isset($class->configuration()['postrequirements']) == false)
			{

				return false;
			}

			if (empty($class->configuration()['postrequirements']))
			{

				return false;
			}

			return true;
		}

		/**
		 * Gets the post requirements for this operation
		 *
		 * @param $process
		 *
		 * @return mixed
		 */

		public function getPostRequirements($process)
		{

			if ($this->hasProcessClass($process) == false)
			{

				throw new SyscrackException();
			}

			$class = $this->findProcessClass($process);

			if (isset($class->configuration()['postrequirements']) == false)
			{

				throw new SyscrackException();
			}

			return $class->configuration()['postrequirements'];
		}

		/**
		 * Returns true if this operation uses the software of your computer and not the software of your current
		 * connection
		 *
		 * @param $process
		 *
		 * @return bool
		 */

		public function useLocalSoftware($process)
		{

			if ($this->hasProcessClass($process) == false)
			{

				throw new SyscrackException();
			}

			$class = $this->findProcessClass($process);

			if (isset($class->configuration()['uselocalsoftware']) == false)
			{

				return false;
			}

			if ($class->configuration()['uselocalsoftware'] == false)
			{

				return false;
			}

			return true;
		}

		/**
		 * Returns true if this action can be preformed with out logging in ( used when dealing with software )
		 *
		 * @param $process
		 *
		 * @return bool
		 */

		public function allowAnonymous($process)
		{

			if ($this->hasProcessClass($process) == false)
			{

				throw new SyscrackException();
			}

			$class = $this->findProcessClass($process);

			if (isset($class->configuration()['allowanonymous']) == false)
			{

				return false;
			}

			if ($class->configuration()['allowanonymous'] == false)
			{

				return false;
			}

			return true;
		}

		/**
		 * Returns true if this operation can be ran on the local users computer
		 *
		 * @param $process
		 *
		 * @return bool
		 */

		public function allowLocal($process)
		{

			if ($this->hasProcessClass($process) == false)
			{

				throw new SyscrackException();
			}

			$class = $this->findProcessClass($process);

			if (isset($class->configuration()['allowlocal']) == false)
			{

				return false;
			}

			if ($class->configuration()['allowlocal'] == false)
			{

				return false;
			}

			return true;
		}

		public function localOnly($process)
		{

			if ($this->hasProcessClass($process) == false)
			{

				throw new SyscrackException();
			}

			$class = $this->findProcessClass($process);

			if (isset($class->configuration()['localonly']) == false)
			{

				return false;
			}

			if ($class->configuration()['localonly'] == false)
			{

				return false;
			}

			return true;
		}

		/**
		 * Calls a method inside the process class
		 *
		 * @param Operation $process
		 *
		 * @param string $method
		 *
		 * @param array $data
		 *
		 * @return mixed
		 */

		private function callProcessMethod(Operation $process, $method = 'onCreation', array $data)
		{

			if ($process instanceof Operation === false)
			{

				throw new SyscrackException();
			}

			if ($this->isCallable($process, $method) == false)
			{

				throw new SyscrackException();
			}

			return call_user_func_array(array($process, $method), $data);
		}

		/**
		 * Returns true if the function is callable
		 *
		 * @param $process
		 *
		 * @param $method
		 *
		 * @return bool
		 */

		private function isCallable($process, $method)
		{

			$class = new \ReflectionClass($process);

			if (empty($class))
			{

				return false;
			}

			if ($class->getMethod($method)->isPublic() == false)
			{

				return false;
			}

			return true;
		}

		/**
		 * Gets all the processes
		 *
		 * @return array|Structures\Software|null|\stdClass
		 */

		private function getProcessesClasses()
		{

			if (empty($this->factory->getAllClasses()) == false)
			{

				throw new SyscrackException();
			}

			$files = FileSystem::getFilesInDirectory(Settings::setting('syscrack_operations_location'));

			if (empty($files))
			{

				throw new SyscrackException();
			}

			foreach ($files as $file)
			{

				$this->factory->createClass(FileSystem::getFileName($file));
			}
		}

	}
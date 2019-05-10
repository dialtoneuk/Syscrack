<?php

	namespace Framework\Views\Pages;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class Processes
	 *
	 * @package Framework\Views\Pages
	 */

	use Framework\Application\Container;
	use Framework\Syscrack\Game\Operations;
	use Framework\Views\BaseClasses\Page as BaseClass;
	use Framework\Views\Structures\Page as Structure;

	class Processes extends BaseClass implements Structure
	{

		/**
		 * @var Operations
		 */

		protected static $operations;

		/**
		 * Processes constructor.
		 */

		public function __construct()
		{

			if (isset(self::$operations) == false)
				self::$operations = new Operations();

			parent::__construct(true, true, true, false);
		}

		/**
		 * Returns the pages mapping
		 *
		 * @return array
		 */

		public function mapping()
		{

			return array(
				[
					'/processes/', 'page'
				],
				[
					'/processes/@processid', 'viewProcess'
				],
				[
					'/processes/@processid/complete', 'completeProcess'
				],
				[
					'/processes/@processid/delete', 'deleteProcess'
				],
				[
					'/processes/computer/@computerid/', 'machineProcess'
				]
			);
		}

		/**
		 * Default page
		 */

		public function page()
		{

			$processes = self::$operations->getUserProcesses(Container::getObject('session')->userid());

			if (empty($processes))
			{

				$array = [];
			}
			else
			{

				$array = array();

				foreach ($processes as $key => $value)
				{

					$array[$value->computerid][] = $value;
				}
			}
			$this->getRender('syscrack/page.process.php', array('processes' => $array, 'operations' => self::$operations, 'computerid' => self::$computer->computerid()));
		}

		/**
		 * Views a process
		 *
		 * @param $processid
		 */

		public function viewProcess($processid)
		{

			if (self::$operations->processExists($processid) == false)
			{

				$this->redirectError('This process does not exist');
			}
			else
			{

				$process = self::$operations->getProcess($processid);

				if ($process->userid != Container::getObject('session')->userid())
				{

					$this->redirectError('This process isnt yours');
				}
				else
				{

					if ($process->computerid != self::$computer->computerid())
					{

						$this->redirectError('You are connected as a different computer');
					}
					else
					{

						$this->getRender('syscrack/page.process.view', array('processid' => $processid, 'processclass' => self::$operations, 'auto' => true));
					}
				}
			}
		}


		public function completeProcess($processid)
		{

			$userid = Container::getObject('session')->userid();

			if (self::$operations->processExists($processid) == false)
				$this->redirectError('Process not found');
			else if (self::$operations->canComplete($processid) == false)
				$this->redirectError('Process not finished');
			else
			{

				$process = self::$operations->getProcess($processid);
				$class = self::$operations->findProcessClass($process->process);

				if ($process->userid != $userid)
					$this->redirectError('Process ownership error');
				else
				{

					$data = json_decode($process->data, true);

					if (isset($data['ipaddress']) == false)
						$this->redirectError('Process error');
					else if (self::$internet->ipExists($data['ipaddress']) == false)
						$this->redirectError('404');
					else
					{

						$target = self::$internet->getComputer($data["ipaddress"]);

						if (self::$operations->requireLoggedIn($process->process))
							if (self::$internet->hasCurrentConnection())
								if ($target->ipaddress != self::$computer->getComputer(self::$computer->computerid())->ipaddress
									&& $target->ipaddress !== self::$internet->getCurrentConnectedAddress()
									&& self::$operations->allowLocal($process->process) == false
									&& self::$operations->isElevatedProcess($process->process) == false)
									$this->redirectError("Connection error 1");
								else
									if (self::$internet->hasCurrentConnection() == false)
										if (self::$operations->isElevatedProcess($process->process) == false || self::$operations->allowLocal($process->process) == false)
											$this->redirectError("Connection error 2");

						$result = self::$operations->completeProcess($processid);

						if (is_string($result))
							$this->redirectSuccess($result);
						else if ($result === null)
							exit;
						else if (is_bool($result) && $result == false)
							$this->redirectError("Error completing process", $class->url($data['ipaddress']));
						else if (is_bool($result) && $result == true)
							$this->redirectSuccess($class->url($data['ipaddress']));
						else
							throw new \Error("Unknown result from process: " . $process . " => " . print_r($result));
					}

				}
			}
		}

		/**
		 * Deletes a process
		 *
		 * @param $processid
		 */

		public function deleteProcess($processid)
		{

			if (self::$operations->processExists($processid) == false)
			{

				$this->redirectError('This process does not exist');
			}
			else
			{

				$process = self::$operations->getProcess($processid);

				if ($process->userid !== Container::getObject('session')->userid())
				{

					$this->redirectError('You do not own this process');
				}

				if ($process->computerid != self::$computer->computerid())
				{

					$this->redirectError('You need to currently be switched to the computer this process was initiated on');
				}

				self::$operations->deleteProcess($processid);

				$this->redirectSuccess('processes/computer/' . self::$computer->computerid());
			}
		}

		public function machineProcess($computerid)
		{

			if (self::$computer->computerExists($computerid) == false)
			{

				$this->redirect('This computer does not exist');
			}

			$computer = self::$computer->getComputer($computerid);

			if ($computer->userid !== Container::getObject('session')->userid())
			{

				$this->redirect('Sorry, this computer is not yours, please try another one');
			}

			$processes = self::$operations->getComputerProcesses($computer->computerid);

			$this->getRender('syscrack/page.process.machine', array('processes' => $processes, 'operations' => self::$operations, 'computerid' => $computerid, 'ipaddress' => $computer->ipaddress));
		}
	}
<?php

	namespace Framework\Application\UtilitiesV2\Scripts;

	/**
	 * Created by PhpStorm.
	 * User: lewis
	 * Date: 31/08/2018
	 * Time: 15:57
	 */

	use Framework\Application\UtilitiesV2\Container;
	use Framework\Application\UtilitiesV2\Debug;
	use Framework\Application\UtilitiesV2\Format;
	use Framework\Application\UtilitiesV2\Scripts;

	class Instance extends Base
	{

		/**
		 * @var bool
		 */

		public static $active_instance = false;

		/**
		 * @var Scripts
		 */

		protected $scripts;

		/**
		 * Instance constructor.
		 */

		public function __construct()
		{

			//Sets the time limit to zero so you don't get booted off
			set_time_limit(0);
		}

		/**
		 * @param $arguments
		 *
		 * @return bool
		 */

		public function execute($arguments)
		{

			if (Container::exist("scripts") == false)
				throw new \Error("Scripts does not exist");

			if (self::$active_instance == true)
				throw new \Error("cannot run two instances at once");

			if ($this->scripts == null)
				$this->refresh();

			$this->scripts->execute("sysinfo", false, true);
			$this->setProcessWindow("Syscrack CLI", time());

			try
			{

				self::$active_instance = true;
				Debug::echo("type help for help, commands for a list of commands. exit or 'e' to exit.");

				while ($input = Debug::getLine("execute"))
				{

					$data = $this->parseInput($input);

					if ($this->canExit($data["script"]))
						$this->exit();

					if ($this->scripts->exists($data["script"]) == false)
						Debug::echo("script does not exist");
					else
					{

						$this->setProcessWindow("Syscrack CLI [" . $data["script"] . "]", time());

						if (empty($data["arguments"]) == false)
							$this->scripts->setArguments($data["arguments"]);

						$this->scripts->execute($data["script"], false);
						$this->scripts->setArguments([]);
					}

					if ($this->canRefresh($data["script"]))
						$this->refresh();

					Debug::echo("");
				}

				$this->setProcessWindow(null, time());
			} catch (\Error $exception)
			{

				self::$active_instance = false;

				$this->printError($exception);

				Debug::echo("Restarting in 2 seconds...\n");
				sleep(2);

				$this->execute($arguments);
			} finally
			{

				$this->setProcessWindow(null, time());
			}

			return parent::execute($arguments);
		}

		/**
		 * @param string $input
		 *
		 * @return array
		 */

		public function parseInput(string $input)
		{

			$split = explode(" ", $input);

			if (count($split) == 1)
				$script = $input;
			else
				$script = $split[0];

			array_shift($split);

			return ([
				"script" => $script,
				"arguments" => $split
			]);
		}

		/**
		 * @param string $title
		 * @param $timestamp
		 */

		public function setProcessWindow($title = null, $timestamp=null)
		{
			if ($title == null)
				$title = cli_get_process_title();

			if( $timestamp == null )
				$timestamp = time();

			$exp = explode(":", $title);

			if (count($exp) != 1)
				$title = $exp[0];

			cli_set_process_title($title . ":" . Format::timestamp($timestamp));
		}

		/**
		 * @param \Error $exception
		 */

		private function printError(\Error $exception)
		{

			Debug::echo("\n[EXCEPTION] " . $exception->getMessage());
			Debug::echo(" in file " . $exception->getFile() . "[" . $exception->getLine() . "]");

			Debug::echo("\nStack Trace\n");

			foreach ($exception->getTrace() as $key => $trace)
				Debug::echo(" " . $key . " : " . $trace["file"] . "[" . $trace["line"] . "]");
		}

		/**
		 * @param $input
		 *
		 * @return bool
		 */

		private function canExit($input)
		{

			return ($input == "exit" || $input == "e" || $input == "bye");
		}

		/**
		 * @param $input
		 *
		 * @return bool
		 */

		private function canRefresh($input)
		{

			return ($input == "r" || $input == "refresh");
		}

		/**
		 * Refreshes the local instance
		 */

		private function refresh()
		{

			Debug::echo("\n! Refreshing instance !\n");

			//Refreshes
			$this->scripts = Container::get("scripts");
		}

		/**
		 * Exits the instance
		 */

		private function exit()
		{


			Debug::echo("\n");
			Debug::echo(">>>---[END OF INSTANCE]--------------------[Terminated: " . date("j F Y c", time() ) . "]---<<<\n");
			Debug::echo("\n");
			exit(0);
		}

		/**
		 * @return array|null
		 */

		public function requiredArguments()
		{

			return parent::requiredArguments();
		}
	}
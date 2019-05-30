<?php

	namespace Framework\Application\UtilitiesV2\Scripts;

	/**
	 * Created by PhpStorm.
	 * User: lewis
	 * Date: 31/08/2018
	 * Time: 15:57
	 */

	use Framework\Application\Utilities\FileSystem;
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
		 * @var string
		 */

		public static $raw_line = "";

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

			parent::__construct();
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

			if( Container::exist("instance" ) == false )
				Container::add("instance", $this );

			if ($this->scripts == null)
				$this->refresh();

			if( Debug::session() )
				$this->session( Debug::$session );

			if (self::$active_instance == true)
				throw new \Error("cannot run two instances at once");

			$this->scripts->execute("sysinfo", false, true);
			$this->setProcessWindow("Syscrack CLI", time());

			try
			{

				Debug::echo("type help for help, commands for a list of commands. exit or 'e' to exit.");

				while ($input = Debug::getLine("execute"))
				{

					self::$active_instance = true;
					self::$raw_line = $input;

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
			}
			catch (\Error $exception)
			{

				self::$active_instance = false;
				$this->printError($exception);
				Debug::echo("Restarting in 2 seconds...\n");
				sleep(2);
				$this->execute($arguments);
			}
			catch ( \RuntimeException $exception )
			{

				self::$active_instance = false;
				$this->printRuntimeException($exception);
				Debug::echo("Terminating instance in 2 seconds...\n");
				sleep(2);
				$this->exit();

				return false;
			}
			finally
			{

				$this->setProcessWindow(null, time());
			}

			self::$active_instance = false;
			return parent::execute($arguments);
		}

		/**
		 * @param $index
		 */

		public function session( $index )
		{

			if( FileSystem::exists( FileSystem::separate("data","cli","session.json") ) == false )
				FileSystem::writeJson( FileSystem::separate("data","cli","session.json"), [ $index => [
					'pid' => getmypid(),
					'created' => time()
				]]);
			else
			{

				$data = FileSystem::readJson(  FileSystem::separate("data","cli","session.json") );
				$data[ $index ] = [
					'pid' => getmypid(),
					'created' => time()
				];
				FileSystem::writeJson( FileSystem::separate("data","cli","session.json"), $data );
			}
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

			Debug::echo("\n[ERROR] " . $exception->getMessage());
			Debug::echo(" in file " . $exception->getFile() . "[" . $exception->getLine() . "]");

			if( Debug::verbosity() >= Debug::VERBOSITY_ERRORS  )
			{

				Debug::echo("\nStack Trace\n");

				foreach ($exception->getTrace() as $key => $trace)
					Debug::echo(" " . $key . " : " . $trace["file"] . "[" . $trace["line"] . "]");
			}
		}

		/**
		 * @param \RuntimeException $exception
		 */


		private function printRuntimeException(\RuntimeException $exception)
		{

			Debug::echo("\n[CRITICAL RUNTIME EXCEPTION] " . $exception->getMessage());
			Debug::echo(" in file " . $exception->getFile() . "[" . $exception->getLine() . "]");

			if( Debug::verbosity() >= Debug::VERBOSITY_ERRORS  )
			{

				Debug::echo("\nStack Trace\n");

				foreach ($exception->getTrace() as $key => $trace)
					Debug::echo(" " . $key . " : " . $trace["file"] . "[" . $trace["line"] . "]");
			}
		}

		/**
		 * @param $input
		 *
		 * @return bool
		 */

		public function canExit($input)
		{

			return ($input == "exit" || $input == "e" || $input == "bye");
		}

		/**
		 * @param $input
		 *
		 * @return bool
		 */

		public function canRefresh($input)
		{

			return ($input == "r" || $input == "refresh");
		}

		/**
		 * Refreshes the local instance
		 */

		public function refresh()
		{

			Debug::echo("\n! Refreshing instance !\n");

			//Refreshes
			$this->scripts = Container::get("scripts");
		}

		/**
		 * Exits the instance
		 */

		public function exit()
		{

			if( Debug::session()  )
				Debug::echo(" @[Terminating " . Debug::$session . "]--------------------[Terminated: " . date("j F Y c", time() ) . "]", 1);
			else
				Debug::echo(" @[End Of Instance]--------------------[Terminated: " . date("j F Y c", time() ) . "]", 1);

			Debug::message("Ending instance and exiting PHP");

			Debug::stashMessages();
			Debug::stashTimers();

			if( Debug::session() )
				if( FileSystem::exists( FileSystem::separate("data","cli","session.json") ) )
				{

					$data = FileSystem::readJson( FileSystem::separate("data","cli","session.json") );
					if( isset( $data[ Debug::$session ] ) )
						unset( $data[ Debug::$session ] );
					FileSystem::writeJson( FileSystem::separate("data","cli","session.json"), $data );
				}

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
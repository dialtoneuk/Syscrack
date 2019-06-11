<?php
	declare(strict_types=1);
	/**
	 * Created by PhpStorm.
	 * User: lewis
	 * Date: 22/07/2018
	 * Time: 00:41
	 */

	namespace Framework\Application\UtilitiesV2;

	use Framework\Application;
	use Framework\Application\Utilities\FileSystem;
	use Framework\Application\UtilitiesV2\Interfaces\Script;

	/**
	 * Class Scripts
	 * @package Framework\Application\UtilitiesV2
	 */
	class Scripts
	{

		/**
		 * @var Constructor
		 */

		protected $constructor;

		/**
		 * @var array
		 */

		protected $arguments;

		/**
		 * @var string
		 */

		protected $script;

		/**
		 * Scripts constructor.
		 *
		 * @param array $arguments ( Should be $argv )
		 * @param bool $auto_create
		 *
		 * @throws \Error
		 */

		public function __construct(array $arguments, $auto_create = true)
		{

			if (count($arguments) < 2)
				$arguments[0] = Application::globals()->CLI_DEFAULT_COMMAND;

			//Unsets the file, leaving the first element the script
			array_shift($arguments);
			$this->script = $arguments[0];
			$this->arguments = $arguments;

			if ( Application::globals()->SCRIPTS_REQUIRE_CMD)
				if (Debug::isCMD() == false)
					Debug::setCMD();

			if( Debug::isCMD() )
				Debug::verbosity( $this->verbosity() );

			$this->constructor = new Constructor(Application::globals()->SCRIPTS_ROOT, Application::globals()->SCRIPTS_NAMESPACE);

			if ($auto_create)
				$this->create();
		}

		/**
		 * Propper desctrutor
		 */

		public function __destruct()
		{

			$result = $this->constructor->getAll();

			if (empty($result))
				return;

			foreach ($result as $class => $instance)
				$this->constructor->remove($class);

			unset($this->constructor);
		}

		/**
		 * @throws \Error
		 */

		public function create()
		{

			$this->constructor->createAll();

			if (empty($this->constructor->getAll()))
				throw new \Error("No scripts found");
		}

		/**
		 * @param null $int
		 *
		 * @return int
		 */

		public function verbosity( $int=null )
		{

			if( $int !== null && is_int( $int ) )
			{

				FileSystem::writeJson( FileSystem::separate("data","cli","verbosity.json" ), ["verbosity" => $int ] );
				return( $int );
			}

			if( FileSystem::exists( FileSystem::separate("data","cli","verbosity.json" ) ) == false )
			{

				FileSystem::writeJson( FileSystem::separate("data","cli","verbosity.json" ), ["verbosity" => 0 ] );
				return( 0 );
			}

			$data = FileSystem::readJson( FileSystem::separate( "data","cli","verbosity.json" ) );

			if( isset( $data["verbosity"] ) == false )
				throw new \Error("Invalid Json: " . SYSCRACK_ROOT . FileSystem::separate(["data","cli","verbosity.json"] ) );
			else
				return( (int)$data["verbosity"] );
		}

		/**
		 * @param $script
		 *
		 * @return bool
		 */

		public function exists($script)
		{

			if ($this->constructor->exist($script) == false)
				return false;

			return true;
		}

		/**
		 * @param $script
		 * @param bool $exit
		 * @param bool $quiet
		 *
		 * @return bool
		 */

		public function execute($script, $exit = true, $quiet = false)
		{

			$script = strtolower($script);
			$name = $script;

			if ($this->exists($script) == false)
				throw new \Error("Script does not exist");

			if ($quiet)
				Debug::setSupressed();

			if (Debug::isCMD())
				Debug::echo("Getting instance of " . $name, 1);

			/**
			 * @var Script $script
			 */

			$script = $this->constructor->get($script);

			if ($script instanceof Script == false)
				throw new \Error("Script is invalid type");

			$arguments = $this->parseArguments();

			if ($this->requiresArguments($script))
			{
				if ($this->checkArguments($script->requiredArguments()) == false)
				{

					Debug::echo("[ERROR] Missing arguments! Please check below for brief help advice on this command.");
					Debug::echo("");
					Debug::echo($this->help($name));
					Debug::echo("");
					Debug::echo("! Please use 'help " . $name . " for more detail. !");

					if ($exit)
						exit(0);
					else
						return true;
				}
			}

			if (Debug::isCMD())
				Debug::echo("Executing script", 2);

			Container::add("scripts", $this);

			$result = $script->execute($arguments);

			if (Debug::isCMD())
				Debug::echo("Script Concluded", 0);

			if ($quiet)
				Debug::setSupressed(false);

			if ($exit)
			{

				if ($result)
				{
					if (Debug::isCMD())
						Debug::echo("Execution Successful", 1);

					return( true );
				}
				else
				{

					if (Debug::isCMD())
						Debug::echo("Execution Failed", 1);

					return( false );
				}

			}
			else
			{

				Debug::echo("Execution Ready...", 1);
				return( $result );
			}
		}

		/**
		 * @param array $arguments
		 */

		public function setArguments(array $arguments)
		{

			$this->arguments = $arguments;
		}

		/**
		 * @param $script
		 *
		 * @return array
		 * @throws \Error
		 */

		public function help($script)
		{


			$script = strtolower($script);
			$name = $script;

			if ($this->exists($script) == false)
				throw new \Error("Script does not exist");

			if (Debug::isCMD())
				Debug::echo("Getting instance of " . $name, 1);

			/**
			 * @var Script $script
			 */

			$script = $this->constructor->get($script);
			$result = $script->help();

			if (is_array($result) == false)
				throw new \Error("Invalid return type");

			return ($result);
		}

		/**
		 * @return mixed
		 */

		public function script()
		{

			return ($this->script);
		}

		/**
		 * Gets the file names of all the scripts
		 *
		 * @return array
		 * @throws \Error
		 */

		public function scripts()
		{

			$directory = new DirectoryOperator(Application::globals()->SCRIPTS_ROOT);

			if ($directory->isEmpty())
				throw new \Error("Invalid directory: " . Application::globals()->SCRIPTS_ROOT);

			$result = $directory->omit($directory->search([".php"]));
			$names = [];

			foreach ($result as $key => $value)
			{

				$name = explode(".", $value)[0];

				if (strtolower($name) == Application::globals()->FRAMEWORK_BASECLASS)
					continue;

				$names[] = strtolower($name);
			}

			return $names;
		}

		/**
		 * @param $command
		 *
		 * @return null|string
		 */

		public function terminal( $command )
		{

			return( shell_exec( $command ) );
		}
		/**
		 * @param Script $script
		 *
		 * @return bool
		 */

		private function requiresArguments($script)
		{

			if (empty($script->requiredArguments()) || $script->requiredArguments() == null)
				return false;

			return true;
		}

		/**
		 * @param array $arguments
		 *
		 * @return bool
		 */

		private function checkArguments(array $arguments)
		{

			$array = $this->parseArguments();

			foreach ($arguments as $key => $argument)
			{

				if (isset($array[$key]) == false)
					if (isset($array[$argument]) == false)
						return false;
			}

			return true;
		}

		/**
		 * @return array|null
		 */

		private function parseArguments()
		{

			$array = $this->arguments;

			if (isset($array[0]) && $array[0] == $this->script())
				array_shift($array);

			$result = [];

			foreach ($array as $key => $value)
			{

				$element = explode("=", $value);

				if (count($element) == 1)
					$result[$element[0]] = null;
				else
				{

					if (strstr($element[1], ".."))
						$element[1] = str_replace("..", " ", $element[1]);

					$result[$element[0]] = $element[1];
				}

			}

			return ($result);
		}

	}
<?php
	declare(strict_types=1);

	namespace Framework\Application\UtilitiesV2;

	/**
	 * Class Debug
	 * @package Framework\Application\UtilitiesV2
	 */

	use Framework\Application\Utilities\FileSystem;
	use Framework\Application;

	/**
	 * Class Debug
	 * @package Framework\Application\UtilitiesV2
	 */
	class Debug
	{

		/**
		 * @var string
		 */

		public static $session;

		/**
		 * @var \stdClass
		 */

		protected static $objects;

		/**
		 * @var bool
		 */

		protected static $supressed = false;

		/**
		 * @var int
		 */

		protected static $verbosity = 0;

		/**
		 * @var string
		 */

		protected static $buffer = "";

		/**
		 * Verbosity consts
		 */
		const VERBOSITY_FULL = 2;
		const VERBOSITY_ERRORS = 1;
		const VERBOSITY_NONE = 0;

		/**
		 * Works in two ways, passing an integer will set the current verbosity and also return it. Passing nothing will
		 * just return the current verbosity;
		 *
		 * @param null $int
		 *
		 * @return int
		 */

		public static function verbosity( $int=null )
		{

			if( $int !== null )
				if( is_int( $int ) )
					self::$verbosity = $int;

			return( self::$verbosity );
		}

		/**
		 *
		 */

		public static function initialization()
		{

			self::$objects = new \stdClass();
			self::$objects->timers = new \stdClass();
		}

		/**
		 * @param int|null $session
		 *
		 * @return bool
		 */

		public static function session( int $session = null )
		{

			if( empty( $session ) || $session === null )
				return( isset( self::$session ) );

			self::$session = $session;

			return( true );
		}

		/**
		 * @param string $message
		 * @param bool $include_time
		 *
		 * @throws \Error
		 */

		public static function message($message, bool $include_time = true)
		{

			if( is_null( $message ) )
				throw new \Error("Message must be not null");

			if( is_array( $message ) )
				$message = implode("\n", $message );

			$message = (string)$message;

			if (Debug::isCMD() )
				if( self::$verbosity > self::VERBOSITY_ERRORS )
					Debug::echo("debug message: " . $message, 2);

			if( Debug::isPHPUnitTest() )
				Debug::echo("phpunit debug message: " . $message, 2);

			if( Application::globals() !== null )
			{

				if (Application::globals()->DEBUG_ENABLED == false)
					return;

				if (self::isInit() == false)
					self::initialization();

				if (isset(self::$objects->messages) == false)
					self::$objects->messages = Debug::getMessages();

				if ($include_time)
					$time = time();
				else
					$time = false;

				self::$objects->messages[] = [
					'message' => $message,
					'time' => $time
				];
			}
		}

		/**
		 * @return bool
		 */

		public static function isPHPUnitTest()
		{

			return (defined("PHPUNIT_ROOT"));
		}

		/**
		 * Shorthand msg
		 *
		 * @param string $msg
		 */

		public static function msg(string $msg)
		{

			self::message($msg);
		}

		/**
		 * @return bool
		 */

		public static function isEnabled()
		{

			return (Application::globals()->DEBUG_ENABLED);
		}

		/**
		 * @param $name
		 * @param $time
		 *
		 * @throws \Error
		 */

		public static function setStartTime($name, $time = null)
		{

			if (Application::globals()->DEBUG_ENABLED == false)
				return;

			if ($time == null)
				$time = time();

			if (self::isInit() == false)
				throw new \Error('Please enable error debugging');

			if (isset(self::$objects->timers->$name))
			{

				if (isset(self::$objects->timers->$name["start"]))
					throw new \Error("Start time has already been set");
			}

			self::$objects->timers->$name = [
				"start" => $time
			];
		}

		/**
		 * @throws \Error
		 */

		public static function stashMessages()
		{

			if (Application::globals()->DEBUG_ENABLED == false)
				return;

			if (self::hasMessages() == false)
				return;

			if( self::$verbosity < self::VERBOSITY_FULL )
				return;

			if( self::session() )
				$path = FileSystem::separate("data","cli", "debug", self::$session, "messages.json" );
			else
				$path = Application::globals()->DEBUG_MESSAGES_FILE;

			if (FileSystem::exists( $path ) == false)
				self::checkDirectory( $path );

			FileSystem::writeJson( $path, self::$objects->messages );
		}

		/**
		 * @return mixed
		 */

		public static function savedMessages()
		{

			if( self::session() )
				$path = FileSystem::separate("data","cli", "debug", self::$session, "messages.json" );
			else
				$path = Application::globals()->DEBUG_MESSAGES_FILE;

			if (FileSystem::exists( $path ) == false)
			{

				self::checkDirectory( $path );
				return( [] );
			}

			return( FileSystem::readJson( $path ) );
		}

		/**
		 * Stashes the output
		 */

		public static function stashOutput()
		{

			if (Application::globals()->DEBUG_ENABLED == false)
				return;

			if( empty( self::$buffer ) )
				return;

			if( self::$verbosity < self::VERBOSITY_ERRORS )
				return;

			if( self::session() )
				$path = FileSystem::separate("data","cli", "debug", self::$session, "output.txt" );
			else
				$path = "data/cli/output.txt";

			if (FileSystem::exists( $path ) == false)
				self::checkDirectory( $path );

			FileSystem::write( $path, self::$buffer );
		}

		/**
		 * @throws \Error
		 */

		public static function stashTimers()
		{

			if (Application::globals()->DEBUG_ENABLED == false)
				return;

			if (self::hasMessages() == false)
				return;

			if( self::$verbosity < self::VERBOSITY_FULL )
				return;

			if( self::session() )
				$path = FileSystem::separate("data","cli", "debug", self::$session, "timers.json" );
			else
				$path = Application::globals()->DEBUG_TIMERS_FILE;

			if (FileSystem::exists( $path ) == false)
				self::checkDirectory( $path );

			FileSystem::writeJson( $path, self::$objects->timers );
		}

		/**
		 * @param $name
		 * @param $time
		 *
		 * @throws \Error
		 */

		public static function setEndTime($name, $time = null)
		{

			if (Application::globals()->DEBUG_ENABLED == false)
				return;

			if ($time == null)
				$time = time();

			if (self::isInit() == false)
				throw new \Error('Please enable error debugging');

			if (isset(self::$objects->timers->$name))
			{

				if (isset(self::$objects->timers->$name["end"]))
					throw new \Error("End time has already been set");
			}
			else
				throw new \Error('Invalid timer');

			self::$objects->timers->$name['end'] = $time;
		}

		/**
		 * @param $name
		 *
		 * @return float
		 * @throws \Error
		 */

		public static function getDifference($name)
		{

			if (isset(self::$objects->timers->$name) == false)
				throw new \Error('Invalid timer');

			$times = self::$objects->timers->$name;

			return ($times['end'] - $times['start']);
		}


		/**
		 * @param $name
		 *
		 * @return bool
		 */

		public static function hasTimer($name)
		{

			return (isset(self::$objects->timers->$name));
		}

		/**
		 * @return mixed
		 */

		public static function getMessages()
		{

			if( empty( self::$objects->messages ) )
				return( self::savedMessages() );

			return (self::$objects->messages);
		}

		/**
		 * @return mixed
		 */

		public static function getTimers()
		{

			return (self::$objects->timers);
		}

		/**
		 * @return bool
		 */

		public static function hasMessages()
		{

			if (isset(self::$objects->messages) == false)
				return false;

			if (empty(self::$objects->messages))
				return false;

			return true;
		}

		/**
		 * @param string $prompt
		 *
		 * @return string
		 */

		public static function getLine($prompt = "Enter")
		{

			if( self::exit() )
				return false;

			$result = readline( $prompt . "\\\\:$" );

			if( empty( $result ) )
				return null;

			self::$buffer .= $prompt . "\\\\:$" . addslashes( $result ) . "\n";

			return ($result);
		}

		/**
		 * @return bool
		 */

		public static function exit()
		{

			if( defined("EXIT") )
				return true;

			return false;
		}

		/**
		 * @return bool
		 */

		public static function hasTimers()
		{

			if (isset(self::$objects->timers) == false)
				return false;

			if (empty(self::$objects->timers))
				return false;

			return true;
		}

		/**
		 * @return bool
		 */

		public static function isCMD()
		{

			return (defined("CMD"));
		}

		/**
		 * Sets CMD mode
		 */

		public static function setCMD()
		{

			if( defined("CMD") == false )
				define("CMD", true);
		}

		/**
		 * @return bool
		 */

		public static function isTest()
		{

			return (defined("TEST"));
		}

		/**
		 * Sets test mode
		 */

		public static function setTest()
		{

			define("TEST", true);
		}

		/**
		 * @param $message
		 * @param int $tabs
		 * @param bool $forceecho
		 */

		public static function echo($message, $tabs = 0, bool $forceecho=false)
		{

			//We don't want any straight up msg's to make their way onto the users HTML
			if (Debug::isCMD() == false && $forceecho === false )
				return;

			if (self::$supressed)
				return;

			@ob_start();

			if (is_array($message))
				foreach ($message as $key => $value)
					if (is_array($value))
					{
						@ob_end_flush();
						@self::echo( @$value, $tabs + 1, $forceecho );
					}
					else
					{
						@ob_end_flush();
						@self::echo(@$key . " => " . @$value, $tabs + 1, $forceecho );
					}
			else
			{

				if ($tabs == 0)
				{

					if (is_string($message) == false)
						$message = print_r($message);

					echo( strtolower( $message . "\n" ));
				}
				else
				{

					$prefix = "";

					for ($i = 0; $i < $tabs; $i++)
						$prefix = $prefix . " ";

					if ($tabs !== 1)
						$prefix .= " ";

					echo( strtolower( $prefix . " " . $message . "\n" ) );
				}
			}

			$contents = ob_get_contents();
			@ob_end_flush();

			self::$buffer .= $contents;
		}

		/**
		 * @param int $beeps
		 */

		public static function beep( $beeps = 1)
		{

			if( Debug::isCMD() == false )
				return;

			$string_beeps = "";
			for ($i = 0; $i < $beeps; $i++): $string_beeps .= "\x07"; endfor;
			isset ($_SERVER['SERVER_PROTOCOL']) ? null : Debug::echo( $string_beeps );
		}

		/**
		 * @return bool
		 */

		public static function isSuppressed()
		{

			return (self::$supressed);
		}

		/**
		 * @param bool $bool
		 */

		public static function setSupressed($bool = true)
		{

			self::$supressed = $bool;
		}

		/**
		 * @return bool
		 */

		private static function isInit()
		{

			if (self::$objects instanceof \stdClass == false)
				return false;

			return true;
		}

		/**
		 * @param string $path
		 */

		private static function checkDirectory( string $path )
		{

			$removed_filename = explode(DIRECTORY_SEPARATOR, $path );
			array_pop($removed_filename);

			$filename = implode(DIRECTORY_SEPARATOR, $removed_filename) . DIRECTORY_SEPARATOR;

			if (is_file(SYSCRACK_ROOT . $filename))
				throw new \Error('Returned path is not a directory');

			if (file_exists(SYSCRACK_ROOT . $filename) == false)
				mkdir(SYSCRACK_ROOT . $filename);
		}
	}
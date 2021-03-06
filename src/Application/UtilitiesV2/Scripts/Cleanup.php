<?php
	declare(strict_types=1);

	namespace Framework\Application\UtilitiesV2\Scripts;

	use Framework\Application\Utilities\FileSystem;
	use Framework\Syscrack\Game\Computer;
	use Framework\Syscrack\Game\Log;
	use Framework\Syscrack\Game\AccountDatabase;
	use Framework\Syscrack\Game\AddressDatabase;
	use Framework\Syscrack\Game\Inventory;
	use Framework\Syscrack\Game\Metadata;
	use Framework\Application\Settings;
	use Framework\Syscrack\User;
	use Framework\Application\UtilitiesV2\Debug;

	/**
	 * Class Cleanup
	 *
	 * Automatically created at: 2019-06-03 09:16:39
	 * @package Framework\Application\UtilitiesV2\Scripts
	 */

	class Cleanup extends Base
	{

		/**
		 * @var Inventory
		 */

		protected static $inventory;

		/**
		 * @var Log
		 */

		protected static $log;

		/**
		 * @var AddressDatabase
		 */

		protected static $addresses;

		/**
		 * @var AccountDatabase
		 */

		protected static $accounts;

		/**
		 * @var Metadata
		 */

		protected static $metadata;

		/**
		 * @var User
		 */

		protected static $user;

		/**
		 * @var Computer
		 */

		protected static $computer;

		/**
		 * Cleanup constructor.
		 */

		public static function setup()
		{

			if( isset( self::$inventory ) == false )
				self::$inventory = new Inventory();

			if( isset( self::$log ) == false )
				self::$log = new Log();

			if( isset( self::$addresses ) == false )
				self::$addresses = new AddressDatabase();

			if (isset( self::$accounts ) == false )
				self::$accounts = new AccountDatabase();

			if (isset( self::$computer ) == false )
				self::$computer = new Computer();

			if( isset( self::$metadata ) == false )
				self::$metadata = new Metadata();

			if( isset( self::$user ) == false )
				self::$user = new User();

			parent::setup();
		}

		/**
	     * The logic of your script goes in this function.
	     *
	     * @param $arguments
	     * @return bool
	     */

	    public function execute($arguments)
	    {

	    	if( is_numeric( $arguments["verbosity"] ) == false )
	    		throw new \Error("Please use a numerical value");
	    	else
		    {

		    	$verbosity = $arguments["verbosity"];
			    $users = self::$user->getAllUsers();
			    $computers = self::$computer->getAllComputers();

		    	if( $verbosity > 1 )
			    {

				    Debug::echo("Initiating File Cleanup");
			    	Debug::echo("Cleaning Inventory Files");
						$result = $this->cleanupInventory( $users );
					Debug::echo( "Cleaned up " . $result . " files" );

				    Debug::echo("Cleaning Address Files");
				        $result = $this->cleanupAddresses( $users );
				    Debug::echo( "Cleaned up " . $result . " files" );

				    Debug::echo("Cleaning Account Files");
				        $result = $this->cleanupAccounts( $users );
				    Debug::echo( "Cleaned up " . $result . " files" );

				    Debug::echo("Cleaning Log Files");
				    $result = $this->cleanupLogs( $computers );
				    Debug::echo( "Cleaned up " . $result . " files" );

				    Debug::echo("Cleaning Market Files");
				    $result = $this->cleanupMarkets( $computers );
				    Debug::echo( "Cleaned up " . $result . " files" );

				    Debug::echo("Cleaning Metadata Files");
				    $result = $this->cleanupMetadata( $computers );
				    Debug::echo( "Cleaned up " . $result . " files" );
			    }
		    }

	        return parent::execute($arguments); // TODO: Change the autogenerated stub
	    }

		/**
		 * @param \Illuminate\Support\Collection $users
		 *
		 * @return int
		 */

	    public function cleanupInventory(  \Illuminate\Support\Collection $users )
	    {

		    $files = FileSystem::getFilesInDirectory(Settings::setting("inventory_filepath"), "json");
		    $exists = [];

		    foreach ($users as $user)
				    if( FileSystem::exists( Settings::setting("inventory_filepath") . $user->userid . ".json" ) )
					    $exists[ FileSystem::getFilePath( Settings::setting("inventory_filepath") . $user->userid . ".json" ) ] = $user->userid;

		    $total = 0;

		    foreach( $files as $file )
			    if( isset( $exists[ $file ] ) == false )
			    {

				    $total += 1;
				    unlink( $file );
			    }

		    return( $total );
	    }

		/**
		 * @param \Illuminate\Support\Collection $users
		 *
		 * @return int
		 */

		public function cleanupAddresses(  \Illuminate\Support\Collection $users )
		{

			$files = FileSystem::getFilesInDirectory(Settings::setting("addresses_location"), "json");
			$exists = [];

			foreach ($users as $user)
				if( FileSystem::exists( Settings::setting("addresses_location") . $user->userid . ".json" ) )
					$exists[ FileSystem::getFilePath( Settings::setting("addresses_location") . $user->userid . ".json" ) ] = $user->userid;

			$total = 0;

			foreach( $files as $file )
				if( isset( $exists[ $file ] ) == false )
				{

					$total += 1;
					unlink( $file );
				}

			return( $total );
		}

		/**
		 * @param \Illuminate\Support\Collection $computers
		 *
		 * @return int
		 */

		public function cleanupMetadata(  \Illuminate\Support\Collection $computers  )
		{

			$files = FileSystem::getFilesInDirectory(Settings::setting("metadata_filepath") . "/", "db");
			$exists = [];

			foreach ($computers as $computer)
				if( FileSystem::exists( Settings::setting("metadata_filepath"). "/" . $computer->computerid . ".db" ) )
					$exists[ FileSystem::getFilePath( Settings::setting("metadata_filepath") . "/" . $computer->computerid . ".db" ) ] = $computer->computerid;

			if( empty( $files ) )
				return 0;

			$total = 0;

			foreach( $files as $file )
				if( isset( $exists[ $file ] ) == false )
				{

					$total += 1;
					unlink( $file );
				}

			return( $total );
		}

		/**
		 * @param \Illuminate\Support\Collection $users
		 *
		 * @return int
		 */

		public function cleanupAccounts(  \Illuminate\Support\Collection $users )
		{

			$files = FileSystem::getFilesInDirectory(Settings::setting("accounts_location"), "json");
			$exists = [];

			foreach ($users as $user)
				if( FileSystem::exists( Settings::setting("accounts_location") . $user->userid . ".json" ) )
					$exists[ FileSystem::getFilePath( Settings::setting("accounts_location") . $user->userid . ".json" ) ] = $user->userid;

			$total = 0;

			foreach( $files as $file )
				if( isset( $exists[ $file ] ) == false )
				{

					$total += 1;
					unlink( $file );
				}

			return( $total );
		}

		/**
		 * @param \Illuminate\Support\Collection $computers
		 *
		 * @return int
		 */

		public function cleanupLogs(  \Illuminate\Support\Collection $computers )
		{

			$files = FileSystem::getDirectories(Settings::setting("log_location"));
			$exists = [];

			foreach ( $computers  as $computer )
			{

				if( self::$user->userExists( $computer->userid ) == false )
					continue;

				if( FileSystem::exists( Settings::setting("log_location") . $computer->computerid . '/' ) )
					$exists[ $computer->computerid ] = $computer->computerid;
			}

			$total = 0;

			foreach( $files as $file )
				if( isset( $exists[ $file ] ) == false )
				{
					$total += 1;
					FileSystem::nukeDirectory( Settings::setting("log_location") . $file );
				}

			return( $total );
		}

		/**
		 * @param \Illuminate\Support\Collection $computers
		 *
		 * @return int
		 */

		public function cleanupMarkets(  \Illuminate\Support\Collection $computers )
		{

			$files = FileSystem::getDirectories(Settings::setting("market_location"));
			$exists = [];

			foreach ( $computers  as $computer )
			{

				if( self::$user->userExists( $computer->userid ) == false )
					continue;

				if( FileSystem::exists( Settings::setting("market_location") . $computer->computerid . '/' ) )
					$exists[ $computer->computerid ] = $computer->computerid;
			}

			$total = 0;

			if( empty( $files ) )
				return 0;

			foreach( $files as $file )
				if( isset( $exists[ $file ] ) == false )
				{
					$total += 1;
					FileSystem::nukeDirectory( Settings::setting("market_location") . $file );
				}

			return( $total );
		}

	    /**
	     * The help index can either be a string or an array containing a set of strings. You can only put strings in
	     * there.
	     *
	     * @return array
	     */

	    public function help()
	    {
	        return([
	            "arguments" => $this->requiredArguments(),
	            "help" => "Hello World"
	        ]);
	    }

	    /**
	     * Example:
	     *  [
	     *      "file"
	     *      "name"
	     *  ]
	     *
	     *  View from the console:
	     *      Cleanup file=myfile.php name=no_space
	     *
	     * @return array|null
	     */

	    public function requiredArguments()
	    {

	        return([
	        	"verbosity"
	        ]);
	    }
	}
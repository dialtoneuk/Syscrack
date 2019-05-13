<?php
	/**
	 * Created by PhpStorm.
	 * User: lewis
	 * Date: 22/07/2018
	 * Time: 01:01
	 */

	namespace Framework\Application\UtilitiesV2\Scripts;


	use Framework\Application\UtilitiesV2\Debug;
	use Framework\Application\UtilitiesV2\OpenSSL;

	class Deploy extends Base
	{

		/**
		 * @var OpenSSL|null
		 */

		protected $openssl = null;

		/**
		 * Deploy constructor.
		 * @throws \Error
		 */

		public function __construct()
		{

			if ($this->isEncrypted())
				$this->openssl = new OpenSSL();
		}

		/**
		 * @param $arguments
		 *
		 * @return bool
		 */

		public function execute($arguments)
		{

			if (isset($arguments["deploy"]))
				unset($arguments["deploy"]);

			if ($this->isEncrypted())
				$result = json_encode($this->encrypt($arguments));
			else
				$result = json_encode($arguments);

			if (file_exists(SYSCRACK_ROOT . DATABASE_CREDENTIALS))
				unlink(SYSCRACK_ROOT . DATABASE_CREDENTIALS);

			Debug::echo("Writing database file", 3);

			file_put_contents(SYSCRACK_ROOT . DATABASE_CREDENTIALS, $result);

			return (true);
		}

		/**
		 * @return bool
		 */

		private function isEncrypted()
		{

			return (DATABASE_ENCRYPTION);
		}

		/**
		 * @param $arguments
		 *
		 * @return array
		 */

		private function encrypt($arguments)
		{

			Debug::echo("Encrypting database file", 3);

			if (DATABSAE_ENCRYPTION_KEY == null)
				$key = $this->openssl->generateKey();
			else
				$key = DATABSAE_ENCRYPTION_KEY;

			return ($this->openssl->encrypt($arguments, $key, $this->openssl->iv(), true));
		}

		/**
		 * @return array|mixed
		 * @throws \Error
		 */

		public function requiredArguments()
		{

			if (file_exists(SYSCRACK_ROOT . DATABASE_MAP) == false)
				throw new \Error("Database map does not exist. Have you unpacked your resources?");

			$contents = file_get_contents(SYSCRACK_ROOT . DATABASE_MAP);

			return (json_decode($contents, true));
		}


		/**
		 * @return array
		 * @throws \Error
		 */

		public function help()
		{
			return ([
				"arguments" => $this->requiredArguments(),
				"help" => "Deploys database connection details (writes them to file). Use the set up script if you want a user friendly method of configuration."
			]);
		}
	}
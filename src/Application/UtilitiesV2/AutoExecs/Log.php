<?php
	declare(strict_types=1);
	/**
	 * Created by PhpStorm.
	 * User: lewis
	 * Date: 06/08/2018
	 * Time: 13:29
	 */

	namespace Framework\Application\UtilitiesV2\AutoExecs;


	use Framework\Application;
	use Framework\Application\UtilitiesV2\Container;
	use Framework\Application\UtilitiesV2\Debug;
	use Framework\Application\UtilitiesV2\Format;

	/**
	 * Class Log
	 * @package Framework\Application\UtilitiesV2\AutoExecs
	 */
	class Log extends Base
	{


		protected $cache = null;

		protected $config;

		/**
		 * Log constructor.
		 * @throws \Error
		 */

		public function __construct()
		{

			if (file_exists(SYSCRACK_ROOT . Application::globals()->AUTOEXEC_LOG_LOCATION) == false)
				throw new \Error("Please run auto migrate");

			if (file_exists(SYSCRACK_ROOT . Application::globals()->AUTOEXEC_LOG_LOCATION . "config.json") == false)
				throw new \Error("Please run auto migrate");

			$this->getConfig();

			if (file_exists(SYSCRACK_ROOT . Application::globals()->AUTOEXEC_LOG_LOCATION . $this->config["file"] . ".json") == false)
				file_put_contents(SYSCRACK_ROOT . Application::globals()->AUTOEXEC_LOG_LOCATION . $this->config["file"] . ".json", json_encode(["created" => time()]));

			parent::__construct();
		}

		/**
		 * @param array $data
		 *
		 * @return bool
		 * @throws \Error
		 */

		public function execute(array $data)
		{

			if (isset($data["message"]) == false)
				if (Container::exist("script"))
					$data["message"] = "from script: " . (string)Container::get("script");
				else
					$data["message"] = false;

			if (isset($data["type"]) == false)
				$data["type"] = Application::globals()->LOG_TYPE_DEFAULT;

			$values = $this->read();

			if (isset($_SERVER["REMOTE_ADDR"]) == false)
				$ipaddress = "localhost";
			else
				$ipaddress = $_SERVER["REMOTE_ADDR"];

			if (isset($data["userid"]))
				$userid = $data["userid"];
			else
				$userid = false;

			$values["log"][] = [
				"type" => $data["type"],
				"message" => $data["message"],
				"ipaddress" => $ipaddress,
				"userid" => $userid,
				"time" => Format::timestamp()
			];

			Debug::message("Creating a new log message for " . $ipaddress);

			$this->save($values);

			return( true );
		}

		/**
		 * @return mixed|null
		 */

		private function read()
		{

			if ($this->cache == null)
				$this->cache = json_decode(file_get_contents(SYSCRACK_ROOT . Application::globals()->AUTOEXEC_LOG_LOCATION . $this->config["file"] . ".json"), true);

			return ($this->cache);
		}

		/**
		 * @param $file
		 */

		private function updateConfig($file)
		{

			file_put_contents(SYSCRACK_ROOT . Application::globals()->AUTOEXEC_LOG_LOCATION . "config.json", json_encode(["file" => $file]));
		}

		/**
		 * Gets the config
		 */

		private function getConfig()
		{

			$this->config = json_decode(file_get_contents(SYSCRACK_ROOT . Application::globals()->AUTOEXEC_LOG_LOCATION . "config.json"), true);
		}

		/**
		 * @param array $values
		 */

		private function save(array $values)
		{

			if ($this->cache == null)
				$this->read();

			if ($this->cache["created"] < time() - (60 * 60 * Application::globals()->AUTOEXEC_LOG_REFRESH))
			{

				$this->updateConfig(time());
				$this->read();
			}

			file_put_contents(SYSCRACK_ROOT . Application::globals()->AUTOEXEC_LOG_LOCATION . $this->config["file"] . ".json", json_encode($values));
		}
	}
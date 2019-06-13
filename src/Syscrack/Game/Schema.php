<?php
	declare(strict_types=1);

	namespace Framework\Syscrack\Game;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class Schema
	 *
	 * @package Framework\Syscrack\Game
	 *
	 */

	use Framework\Application\Settings;
	use Framework\Application\Utilities\FileSystem;
	use Framework\Database\Tables\Computer;

	/**
	 * Class Schema
	 * @package Framework\Syscrack\Game
	 */
	class Schema
	{

		/**
		 * @var Computer
		 */

		protected $computer;

		/**
		 * NPC constructor.
		 */

		public function __construct()
		{

			$this->computer = new Computer();
		}

		/**
		 * Creates a new schema file
		 *
		 * @param $computerid
		 *
		 * @param string $name
		 *
		 * @param string $page
		 *
		 * @param array $riddles
		 *
		 * @param array $software
		 *
		 * @param array $hardware
		 */

		public function createSchema($computerid, $name = 'Default', $page = 'schema.default', array $riddles=[], array $software=[], array $hardware=[])
		{

			$schema = [
				'name' => $name,
				'page' => $page,
				'riddles' => $riddles,
				'software' => $software,
				'hardware' => $hardware
			];

			FileSystem::writeJson($this->getSchemaPath($computerid), $schema);
		}

		/**
		 * @param $computerid
		 * @param array $schema
		 */
		public function setSchema($computerid, $schema = [])
		{

			FileSystem::writeJson($this->getSchemaPath($computerid), $schema);
		}

		/**
		 * Returns true if we have an NPC Page
		 *
		 * @param $computerid
		 *
		 * @return bool
		 */

		public function hasSchemaPage($computerid)
		{

			$schema = $this->getSchema($computerid);

			if (isset($schema['page']) == false)
			{

				return false;
			}

			if ($this->SchemaPageExists($computerid) == false)
			{

				return false;
			}

			return true;
		}

		/**
		 * Returns true if an NPC Page exists
		 *
		 * @param $computerid
		 *
		 * @return bool
		 */

		public function SchemaPageExists($computerid)
		{

			if (FileSystem::exists('/themes/' . Settings::setting('theme_folder') . DIRECTORY_SEPARATOR . $this->getSchemaPageLocation($computerid)) == false)
			{

				return false;
			}

			return true;
		}

		/**
		 * Gets the location of this computer page
		 *
		 * @param $computerid
		 *
		 * @return string
		 */

		public function getSchemaPageLocation($computerid)
		{

			return Settings::setting('schema_pages') . $this->getSchema($computerid)['page'] . '.php';
		}

		/**
		 * Gets the NPC File tied to this computer
		 *
		 * @param $computerid
		 *
		 * @return mixed
		 */

		public function getSchema($computerid)
		{

			return FileSystem::readJson(Settings::setting('schema_filepath') . $computerid . '.json');
		}

		/**
		 * Gets the schemas path
		 *
		 * @param $computerid
		 *
		 * @return string
		 */

		public function getSchemaPath($computerid)
		{

			return Settings::setting('schema_filepath') . $computerid . '.json';
		}

		/**
		 * Returns true if we have an NPC file
		 *
		 * @param $computerid
		 *
		 * @return bool
		 */

		public function hasSchema($computerid)
		{

			if (FileSystem::exists($this->getSchemaPath($computerid)) == false)
			{

				return false;
			}

			return true;
		}
	}
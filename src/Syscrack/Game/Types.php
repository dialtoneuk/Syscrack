<?php
	/**
	 * Created by PhpStorm.
	 * User: newsy
	 * Date: 05/05/2019
	 * Time: 21:30
	 */

	namespace Framework\Syscrack\Game;

	use Framework\Application\Settings;
	use Framework\Application\Utilities\FileSystem;
	use Framework\Syscrack\Game\Interfaces\Computer;
	use Framework\Syscrack\Game\Computer as Controller;

	class Types
	{

		/**
		 * @var Computer
		 */

		protected static $computer;

		/**
		 * Types constructor.
		 */

		public function __construct()
		{

			if (isset(self::$computer) == false)
				self::$computer = new Controller();
		}

		/**
		 * @return mixed
		 */

		public function get()
		{

			if (FileSystem::exists(Settings::setting("computer_types_filepath")) == false)
				$this->generate();

			return (FileSystem::readJson(Settings::setting("computer_types_filepath")));
		}

		/**
		 * Generates the types
		 */

		public function generate()
		{

			$types = [];

			foreach (self::$computer->getComputerClasses() as $class)
			{

				if ($class instanceof Computer == false)
					continue;

				/**
				 * @var $class Computer
				 */
				$types[] = $class->configuration()["type"];
			}

			array_unique( $types );
			FileSystem::writeJson(Settings::setting("computer_types_filepath"), array_values( $types ) );
		}
	}
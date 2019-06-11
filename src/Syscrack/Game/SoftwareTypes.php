<?php
	declare(strict_types=1);
	/**
	 * Created by PhpStorm.
	 * User: newsy
	 * Date: 22/05/2019
	 * Time: 14:33
	 */

	namespace Framework\Syscrack\Game;

	use Framework\Application\Utilities\FileSystem;
	use Framework\Application\Settings;
	use Framework\Syscrack\Game\Interfaces\Software;
	use Framework\Syscrack\Game\Software as SoftwareManager;

	/**
	 * Class SoftwareTypes
	 * @package Framework\Syscrack\Game
	 */
	class SoftwareTypes
	{

		/**
		 * @var SoftwareManager
		 */

		protected static $software;

		/**
		 * SoftwareTypes constructor.
		 */

		public function __construct()
		{

			if( isset( self::$software ) == false )
				self::$software = new SoftwareManager();
		}

		/**
		 * @return mixed
		 */

		public function get()
		{

			if( FileSystem::exists( Settings::setting("software_types_filepath") ) == false )
				$this->generate();

			return( FileSystem::readJson( Settings::setting("software_types_filepath") ) );
		}

		/**
		 * Generates the types
		 */

		public function generate()
		{

			$types = [];

			foreach (self::$software->getAllClasses() as $class)
			{

				/**
				 * @var Software $class
				 */
				if ($class instanceof Software == false)
					continue;

				$type = $class->configuration()["type"];

				foreach( $types as $item )
					if( $item == $type )
						continue;

				$types[] = $type;
			}

			$types = array_unique( $types );
			FileSystem::writeJson(Settings::setting("software_types_filepath"), array_values( $types ));
		}
	}
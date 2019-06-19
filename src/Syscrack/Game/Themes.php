<?php
	declare(strict_types=1);
	/**
	 * Created by PhpStorm.
	 * User: newsy
	 * Date: 30/04/2019
	 * Time: 19:11
	 */

	namespace Framework\Syscrack\Game;

	use Framework\Application\Settings;
	use Framework\Application\Utilities\FileSystem;
	use Framework\Application\UtilitiesV2\Conventions\ThemeData;
	use Framework\Application\UtilitiesV2\Format;


	/**
	 * Class Themes
	 * @package Framework\Syscrack\Game
	 */
	class Themes
	{

		/**
		 * @var array
		 */

		protected $themes;

		/**
		 * Themes constructor.
		 *
		 * @param bool $autoread
		 */

		public function __construct($autoread = true)
		{

			if ($autoread)
				$this->getThemes();
		}

		/**
		 * @return mixed
		 */

		public function currentTheme()
		{

			return (Settings::setting("theme_folder"));
		}

		/**
		 * @param $theme
		 */

		public function set($theme)
		{

			if ($this->themeExists($theme) == false)
				throw new \Error("Theme does not exist: " . $theme);

			if ($this->currentTheme() == $theme)
				throw new \Error("Theme already set to: " . $theme);

			if( $this->hasBase() )
				Settings::updateSetting("theme_base", "");

			$this->processData( $theme );

			Settings::updateSetting("theme_folder", $theme);

		}

		/**
		 * @param $theme
		 */

		public function processData( $theme )
		{

			foreach( $this->getData( $theme ) as $key=>$value )
				switch( strtolower( $key ) )
				{

					case "mvc":
						if( is_bool( $value ) == false )
							Settings::updateSetting("theme_mvc_output", true );
						else
							Settings::updateSetting("theme_mvc_output", $value );
						break;
					case "json":
						if( is_bool( $value ) == false )
							Settings::updateSetting("theme_json_output", true );
						else
							Settings::updateSetting("theme_json_output", $value );
						break;
					case "array":
						if( is_bool( $value ) == false )
							Settings::updateSetting("theme_force_array", true );
						else
							Settings::updateSetting("theme_force_array", $value );
						break;
					case "object":
						if( is_bool( $value ) == false )
							Settings::updateSetting("theme_force_object", true );
						else
							Settings::updateSetting("theme_force_object", $value );
						break;
					case "base":
						if( is_string( $value ) == false )
							throw new \Error("Error in data: base is not a string. In theme " . $theme );
						else
						{

							if( $this->themeExists( $value ) == false )
								throw new \Error($theme . " would like to use " . $value . " as a base and you currently dont have " . $value . " installed in your theme folder" );

							Settings::updateSetting("theme_base", $value );
						}
						break;
				}
		}

		/**
		 * @return bool
		 */

		public function hasBase()
		{

			return(  Settings::setting("theme_base") !== "" );
		}

		/**
		 * @param $theme
		 *
		 * @return bool
		 */
		public function hasAssets($theme)
		{

			$data = $this->getData($theme);

			if (empty($data))
				return false;
			else if ( isset( $data["assets"] ) == false )
				return false;

			return true;
		}

		/**
		 * @param $theme
		 *
		 * @return mixed
		 */

		public function assets( $theme )
		{

			return( $this->getData( $theme )["assets"] );
		}

		/**
		 * @return ThemeData|mixed
		 */

		public function base()
		{

			return( $this->getTheme( Settings::setting('theme_base') ) );
		}

		/**
		 * @param $theme
		 *
		 * @return mixed
		 */

		public function getData($theme)
		{

			return ($this->themes[$theme]["data"]);
		}

		/**
		 * @param $theme
		 * @param ThemeData $object
		 */

		public function modifyInfo($theme, ThemeData $object)
		{

			FileSystem::writeJson($this->path($theme), $object->contents());
		}

		/**
		 * @param $theme
		 * @param bool $object
		 *
		 * @return ThemeData|mixed
		 */

		public function getTheme($theme, $object = true)
		{

			if ($this->themeExists($theme) == false)
				throw new \Error("Theme does not exist: " . $theme);

			$themes = $this->getThemes(false);

			if ($object)
				return self::dataInstance($themes[$theme]);
			else
				return ($themes[$theme]);
		}

		/**
		 * @param $theme
		 *
		 * @return bool
		 */

		public function themeExists($theme)
		{

			if( empty( $this->themes ) )
				$this->getThemes( true );

			return (isset($this->themes[$theme]));
		}

		/**
		 * @param bool $read
		 *
		 * @return array
		 */

		public function getThemes($read = true)
		{

			if ($read)
			{

				$result = $this->gather($this->getFolders());

				if (empty($result))
					return [];

				$this->themes = $result;

				return ($result);
			}
			else if (empty($this->themes))
				return [];
			else
				return ($this->themes);
		}

		/**
		 * @param $folders
		 *
		 * @return array
		 */

		public function gather($folders)
		{

			$info = [];

			foreach ($folders as $folder)
				$info[$folder] = FileSystem::readJson(
					$this->path($folder)
				);

			return ($info);
		}

		/**
		 * @return array|false|null
		 */

		public function getFolders()
		{

			if (FileSystem::directoryExists(Settings::setting("theme_location")) == false)
				throw new \Error("Themes folder does not exist");

			return (FileSystem::getDirectories(Settings::setting("theme_location")));
		}

		/**
		 * @param null $folder
		 *
		 * @return string
		 */

		public function path($folder = null)
		{

			return (FileSystem::separate(
				Settings::setting("theme_location"),
				$folder,
				Settings::setting("theme_info_file")
			));
		}

		/**
		 * @param array $values
		 *
		 * @return ThemeData
		 */

		public static function dataInstance($values)
		{

			return (new ThemeData($values));
		}
	}
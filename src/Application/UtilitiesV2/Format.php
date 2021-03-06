<?php
	declare(strict_types=1);

	namespace Framework\Application\UtilitiesV2;

	use Framework\Application;

	/**
	 * Class Format
	 * @package Framework\Application\UtilitiesV2
	 */
	class Format
	{

		/**
		 * @param null $time
		 * @param bool $small
		 *
		 * @return false|string
		 */

		public static function timestamp($time = null, $small=false )
		{

			if ($time == null)
				$time = time();

			if( $small )
				return (date('m-d H:i', $time));

			return (date('Y-m-d H:i:s', $time));
		}

		/**
		 * @param string $type
		 * @param $value
		 *
		 * @return float|int|string
		 */

		public static function cast( string $type, &$value )
		{

			switch( $type )
			{

				case 'int':
					if( is_numeric( $value ) == false )
						throw new \Error("Value " . $value . " is not numeric");
					else
						$value = ( (int) $value );
					break;
				case 'string':
					if( is_array( $value ) || is_object( $value ) )
						throw new \Error("Value is an object");
					else
						$value = ( (string) $value );
				case 'float':
					if( is_numeric( $value ) == false  )
						throw new \Error("Value " . $value . " is not numeric");
					else
						$vakue  = ( (float) $value );
			}
		}

		/**
		 * @param string $string
		 *
		 * @return string
		 */

		public static function strip( string $string ): string
		{

			return( strip_tags( $string ) );
		}

		/**
		 * @param string $string
		 * @param int $filter
		 */

		public static function filter( string &$string , $filter = -1 )
		{

			if( $filter === -1 )
				$filter = FILTER_SANITIZE_STRING;

			$string = filter_var($string, $filter);
		}

		/**
		 * @param mixed $value
		 * @param int $filter
		 *
		 * @return bool
		 */

		public static function validate( $value , $filter = -1): bool
		{

			if( $filter === -1 )
				$filter = FILTER_VALIDATE_INT;

			$result = filter_var( $value, $filter);

			if( $result === false )
				return false;

			return true;
		}

		/**
		 * @param string $string
		 *
		 * @return string
		 */

		public static function rc( string $string ): string
		{

			return( substr( $string, 0, strlen( $string ) - 1 ) );
		}

		/**
		 * @param null $time
		 *
		 * @return false|string
		 */

		public static function year($time = null )
		{

			if ($time == null)
				$time = time();

			return (date('Y', $time));
		}


		/**
		 * @param string $text
		 *
		 * @return string
		 */

		public static function largeText(string $text): string
		{

			return (base64_encode($text));
		}

		/**
		 * @param string $text
		 *
		 * @return bool|string
		 */

		public static function decodeLargeText(string $text)
		{

			return (base64_decode($text));
		}

		/**
		 * @param $mixed
		 * @param $pretty
		 *
		 * @return string
		 */

		public static function toJson($mixed, $pretty = false): string
		{

			if (is_array($mixed) == false && is_object($mixed) == false)
				throw new \Error("Invalid type");

			if ($pretty)
				return (json_encode($mixed, JSON_PRETTY_PRINT));
			else
				return (json_encode($mixed));
		}

		/**
		 * @param object $object
		 *
		 * @return array
		 */

		public static function toArray($object): array
		{

			return (json_decode(self::toJson($object), true));
		}

		/**
		 * @param array $array
		 *
		 * @return object
		 */

		public static function toObject(array $array): object
		{

			return (json_decode(self::toJson($array)));
		}

		/**
		 * @param $salt
		 * @param $password
		 *
		 * @return string
		 */

		public static function saltedPassword($salt, $password): string
		{

			return (sha1($salt . $password));
		}

		/**
		 * @param string $type
		 * @param $asset
		 *
		 * @return string
		 */

		public static function asset($type = "js", $asset=""): string
		{

			return ( Application::globals()->SYSCRACK_URL_ROOT . "assets/" . $type . "/" . $asset);
		}

		/**
		 * @param null $prefix
		 *
		 * @return string
		 */

		public static function filename($prefix = null): string
		{

			if ($prefix == null)
				$prefix = time();

			return ((string)$prefix . uniqid(rand(), true));
		}

		/**
		 * @return string
		 * @todo fix the hex output
		 */

		public static function colour()
		{

			return( Colours::generate( Application::globals()->COLOURS_OUTPUT_HEX ) );
		}
	}
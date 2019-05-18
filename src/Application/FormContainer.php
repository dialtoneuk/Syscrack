<?php
	/**
	 * Created by PhpStorm.
	 * User: newsy
	 * Date: 18/05/2019
	 * Time: 03:28
	 */

	namespace Framework\Application;


	use Framework\Application\UtilitiesV2\Interfaces\Response;

	class FormContainer
	{

		/**
		 * @var array
		 */

		protected static $array;

		/**
		 * @param Response $response
		 */

		public static function add( Response $response ): void
		{

			self::$array[] = $response;
		}

		/**
		 * @param $index
		 */

		public static function destroyIndex( $index ): void
		{

			if( isset( self::$array[ $index ] ) == false )
				return;

			unset( self::$array[ $index ] );
		}

		/**
		 * @return bool
		 */

		public static function empty(): bool
		{

			return( empty( self::$array ) );
		}

		/**
		 * @return array
		 */

		public static function contents(): array
		{

			return( self::$array );
		}
	}
<?php
	declare(strict_types=1);
	/**
	 * Created by PhpStorm.
	 * User: newsy
	 * Date: 18/05/2019
	 * Time: 03:28
	 */

	namespace Framework\Application;


	use Framework\Application\UtilitiesV2\Interfaces\Response;

	/**
	 * Class FormContainer
	 * @package Framework\Application
	 */
	class FormContainer
	{

		/**
		 * @var array
		 */

		protected static $array = [];

		/**
		 * @param Response $response
		 */

		public static function add( Response $response ): void
		{

			if( isset( $_SESSION["form"] ) )
				if( empty( $_SESSION["form"] ) )
					array_push( self::$array, $response->get() );
				else
				{

					/**
					$response = $response->get();

					try
					{

						foreach( $_SESSION["form"] as $error )
							if( $error["message"] == $response["message"] )
								return;
					}
					catch ( \Exception $error )
					{

						return;
					}
					**/


					array_push( self::$array, $response->get() );
				}
			else
				array_push( self::$array, $response->get() );
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
<?php
	/**
	 * Created by PhpStorm.
	 * User: newsy
	 * Date: 23/06/2019
	 * Time: 04:50
	 */

	namespace Framework\Syscrack\GameV2;

	use Framework\Database\Tables\Software as Database;

	class Software
	{

		/**
		 * @var Database
		 */

		protected static $database;

		/**
		 * Software constructor.
		 */

		public function __construct()
		{

			if( isset( self::$database ) == false )
				self::$database = new Database();
		}

		/**
		 * @param $softwareid
		 *
		 * @return mixed|null
		 */

		public function get( $softwareid )
		{

			return( self::$database->getSoftware( $softwareid ) );
		}

		/**
		 * @param $softwareid
		 *
		 * @return bool
		 */

		public function exists( $softwareid )
		{

			return( self::$database->getSoftware( $softwareid ) == null );
		}

		/**
		 * @param $softwareid
		 * @param array $values
		 */

		public function update( $softwareid, array $values )
		{

			self::$database->updateSoftware( $softwareid, $values );
		}

		/**
		 * @param $computerid
		 *
		 * @return \Illuminate\Support\Collection|null
		 */

		public function computer( $computerid )
		{

			return( self::$database->getByComputer( $computerid ) );
		}

		/**
		 * @param $userid
		 *
		 * @return mixed|null
		 */

		public function user( $userid )
		{

			return( self::$database->getUserSoftware( $userid ) );
		}

		/**
		 * @param $computerid
		 *
		 * @return \Illuminate\Support\Collection|null
		 */

		public function installed( $computerid )
		{

			return( self::$database->getInstalledSoftware( $computerid ) );
		}

		/**
		 * @param $softwareid
		 *
		 * @return mixed
		 */

		public function uniquename( $softwareid )
		{

			$result = self::$database->getSoftware( $softwareid );

			if( isset( $result->uniquename ) == false)
				throw new \Error("Unique name not set: " . $softwareid );
			else
				returN( $result->uniquename );
		}
	}
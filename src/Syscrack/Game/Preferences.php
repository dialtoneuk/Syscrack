<?php
	/**
	 * Created by PhpStorm.
	 * User: newsy
	 * Date: 31/05/2019
	 * Time: 03:56
	 */

	namespace Framework\Syscrack\Game;


	use Framework\Application\Utilities\FileSystem;

	class Preferences
	{

		/**
		 * @param int $userid
		 * @param int $computerid
		 * @param string $type
		 *
		 * @return null
		 */

		public function getSoftwarePreference( int $userid, int $computerid, string $type )
		{

			$data = $this->get( $userid );

			if( isset( $data[ $computerid ] ) == false )
				return null;

			foreach( $data[ $computerid ]  as $key=>$datum )
				if( $key == $type )
					return $datum;

			return( null );
		}

		/**
		 * @param int $userid
		 * @param int $computerid
		 * @param string $type
		 *
		 * @return bool
		 */

		public function hasSoftwarePreference( int $userid, int $computerid, string $type )
		{

			$data = $this->get( $userid );

			if( isset( $data[ $computerid ] ) == false )
				return false;

			foreach( $data[ $computerid ] as $key=>$datum )
				if( $key == $type )
					return true;

			return false;
		}

		/**
		 * @param int $userid
		 * @param int $computerid
		 * @param array $object
		 */

		public function add( int $userid, int $computerid, array $object )
		{

			if( $this->has( $userid ) == false )
				$data = [];
			else
				$data = $this->get( $userid );

			if( isset( $data[ $computerid ] ) == false )
				$data[ $computerid ] = [];

			$keys = array_keys( $object );
			$data[ $computerid ][ $keys[0] ] = reset( $object );
			$this->set( $userid, $data );
		}

		/**
		 * @param int $userid
		 * @param int $computerid
		 * @param string $type
		 */

		public function remove( int $userid, int $computerid, string $type )
		{

			$data = $this->get( $userid );

			if( isset( $data[ $computerid ] ) == false )
				return;

			foreach( $data[ $computerid ] as $key=>$datum )
				if( $key == $type )
					unset( $data[ $computerid ][ $key ] );

			$this->set( $userid, $data );
		}

		/**
		 * @param int $userid
		 * @param $data
		 */

		public function set( int $userid, $data )
		{

			if( is_array( $data ) == false && is_object( $data ) == false )
				throw new \Error("Invalid object passed as parameter");

			FileSystem::writeJson( $this->path( $userid), $data );
		}

		/**
		 * @param int $userid
		 *
		 * @return mixed
		 */

		public function get( int $userid )
		{

			return( FileSystem::readJson( $this->path( $userid ) ) );
		}

		/**
		 * @param int $userid
		 */

		public function wipe( int $userid )
		{

			FileSystem::delete( $this->path( $userid ) );
		}

		/**
		 * @param int $userid
		 *
		 * @return bool
		 */

		public function has( int $userid )
		{

			return( FileSystem::exists( $this->path( $userid) ) );
		}

		/**
		 * @param int $userid
		 *
		 * @return string
		 */

		private function path( int $userid=null )
		{

			if( $userid === null )
				return( FileSystem::separate("data","preferences") );
			else
				return( FileSystem::separate("data","preferences", $userid . ".json") );
		}
	}
<?php
	declare(strict_types=1);
	/**
	 * Created by PhpStorm.
	 * User: newsy
	 * Date: 15/05/2019
	 * Time: 00:33
	 */

	namespace Framework\Syscrack\Game;


	/**
	 * Class Tabs
	 * @package Framework\Syscrack\Game
	 */
	class Tabs
	{

		/**
		 * @var array
		 */

		protected $tabs = [];

		/**
		 * Tabs constructor.
		 *
		 * @param array $tabs
		 */

		public function __construct( $tabs = [] )
		{

			if( empty( $tabs ) == false )
				foreach( $tabs as $key => $tab )
					if( $tab instanceof Tab == false && is_string( $key ) == false )
						throw new \Error("Invalid data inside array at key" . $key . ": " . print_r( $tabs ) );

			$this->tabs = $tabs;
		}

		/**
		 * @param string $name
		 * @param Tab $tab
		 */

		public function add( Tab $tab, $name=null ): void
		{

			if( $name === null )
				$name = $tab->name();

			if( isset( $this->tabs[ $name ]) )
				throw new \Error("Tab already exists: " . $name );

			$this->tabs[ $name ] = $tab;
		}

		/**
		 * @param $name
		 *
		 * @return Tab
		 */

		public function find( $name )
		{

			return( $this->tabs[ $name ] );
		}

		/**
		 * @param $name
		 *
		 * @return bool
		 */

		public function has( $name )
		{

			return( isset( $this->tabs[ $name ] ) );
		}

		/**
		 * @param $name
		 * @param mixed ...$arguments
		 *
		 * @return bool
		 */

		public function data( $name, ...$arguments ): bool
		{

			$tab = $this->find( $name );

			if( $tab->canPost() == false )
				throw new \Error("Data method must be set");

			$data = $tab->get()["data"];

			if( is_callable( $data ) == false )
				throw new \Error("Data method invalid");

			return( @$data( $arguments ) );
		}

		/**
		 * @param $name
		 * @param mixed ...$arguments
		 *
		 * @return bool
		 */

		public function post( $name, ...$arguments ): bool
		{

			$tab = $this->find( $name );

			if( $tab->canPost() == false )
				throw new \Error("Post method must be set");

			$post = $tab->get()["post"];

			if( is_callable( $post ) == false )
				throw new \Error("Post method invalid");

			return( @$post( $arguments ) );
		}

		/**
		 * @return array
		 */

		public function get(): array
		{

			return( $this->tabs );
		}
	}
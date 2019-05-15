<?php
	/**
	 * Created by PhpStorm.
	 * User: newsy
	 * Date: 15/05/2019
	 * Time: 00:33
	 */

	namespace Framework\Syscrack\Game;


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
		 * @return array
		 */

		public function get(): array
		{

			return( $this->tabs );
		}
	}
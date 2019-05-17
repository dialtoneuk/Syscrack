<?php
	/**
	 * Created by PhpStorm.
	 * User: newsy
	 * Date: 14/05/2019
	 * Time: 01:25
	 */

	namespace Framework\Syscrack\Game;


	use Framework\Application\UtilitiesV2\Conventions\InputData;

	class Tab
	{

		/**
		 * @var array
		 */

		protected $information = [];

		/**
		 * Tab constructor.
		 *
		 * @param string $name
		 * @param bool $admin
		 */

		public function __construct( $name = "Default", $admin=false )
		{

			$this->information["name"] = $name;
			$this->information["admin"] = $admin;
		}

		/**
		 * @return array
		 */

		public function get()
		{

			return( $this->information );
		}

		/**
		 * @param string $file
		 * @param array $data
		 */

		public function render( string $file, array $data = [] ): void
		{

			$this->information["render"] = [
				"file" => $file,
				"data" => $data,
			];
		}

		/**
		 * @return bool
		 */

		public function hasRender(): bool
		{

			return( isset( $this->information["render"] ) && empty( $this->information["render"] ) == false );
		}

		/**
		 * Bypasses the tab builder ( no inputs/anything rendered )
		 */

		public function bypass()
		{

			$this->information["bypass"] = true;
		}

		/**
		 * @param callable $method
		 */

		public function postMethod( callable $method ): void
		{

			if( is_callable( $method ) == false )
				throw new \Error("Must be a callable method");

			$this->information["post"] = $method;
		}

		/**
		 * @param InputData $data
		 */

		public function add( InputData $data ): void
		{

			$this->information["inputs"][] = $data->contents();
		}

		/**
		 * @return bool
		 */

		public function hasInputs(): bool
		{

			return( isset( $this->information["inputs"] ) && empty( $this->information["inputs"] ) == false );
		}

		/**
		 * @return bool
		 */

		public function canPost(): bool
		{

			return( isset( $this->information["post"] ) );
		}

		/**
		 * @return bool
		 */

		public function hasDataMethod(): bool
		{

			return( isset( $this->information["data"] ) && is_callable( $this->information["data"] ) );
		}

		/**
		 * @param callable $method
		 */

		public function dataMethod( callable $method ): void
		{

			if( is_callable( $method ) == false )
				throw new \Error("Must be a callable method");

			$this->information["data"] = $method;
		}

		/**
		 * @return string
		 */

		public function name(): string
		{

			return( $this->information["name"] );
		}

		/**
		 * @param array $contents
		 *
		 * @return InputData
		 */

		public static function InputData( array $contents )
		{

			return( new InputData( $contents ) );
		}
	}
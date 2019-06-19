<?php
	declare(strict_types=1);
	/**
	 * Created by PhpStorm.
	 * User: newsy
	 * Date: 12/06/2019
	 * Time: 01:15
	 */

	namespace Framework\Application\UtilitiesV2;

	/**
	 * Class Request
	 * @package Framework\Application\UtilitiesV2
	 */
	class Request extends \stdClass
	{

		/**
		 * @var array
		 */

		protected $data = [];

		protected $original = [];

		/**
		 * Request constructor.
		 */

		public function __construct()
		{

			if( empty( $_POST ) )
				return;
			else
				$this->process();

		}

		/**
		 * @param array $values
		 *
		 * @return bool
		 */

		public function compare( ...$values )
		{

			if( $this->empty() )
				return false;

			if( isset( $values[0] ) && is_array( $values[0] ) )
				$values = $values[0];

			foreach( $values as $value )
				if( isset( $this->data[ $value ] ) == false )
					return false;
				elseif( $this->data[ $value ] === null || $this->data[ $value ] === "" || empty( $this->data[ $value ]) )
					return false;

			return true;
		}

		/**
		 * @return bool
		 */

		public function empty()
		{

			return( empty( $this->data ) );
		}

		/**
		 * @param $name
		 *
		 * @return bool
		 */

		public function __isset($name)
		{

			if( $this->empty() )
				return false;

			return( isset( $this->data[ $name ] ) );
		}

		/**
		 * @param $name
		 *
		 * @return mixed
		 */

		public function __get($name)
		{

			if( isset( $this->data[ $name ] ) == false )
				return null;

			return( $this->data[ $name ] );
		}

		/**
		 * @param $name
		 * @param $value
		 */

		public function __set($name, $value)
		{


			$this->data[ $name ] = $value;
		}

		/**
		 * @return array
		 */

		public function original()
		{

			return( $this->original );
		}

		/**
		 * @return array
		 */

		public function debug()
		{

			return( array_map(function ($data)
			{
				return [
					'type' => gettype($data),
					'value' => $data
				];
			}, $this->data) );
		}

		/**
		 * Processes
		 */

		private function process()
		{

			foreach( $_POST as $key=>$value )
				if( is_numeric( $value ) )
					if( strpos( $value, ".") !== false )
						$this->data[ $key ] = (float)$value;
					else
						$this->data[ $key ] = (int)$value;
				else
					$this->data[ $key ] = (string)$value;

			$this->original = $this->data;
		}
	}
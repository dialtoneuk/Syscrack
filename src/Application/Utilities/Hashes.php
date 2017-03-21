<?php
namespace Framework\Application\Utilities;

/**
 * Lewis Lancaster 2016
 *
 * Class Hashes
 *
 * @package Framework\Application\Utilities
 */

use Framework\Exceptions\ApplicationException;

class Hashes
{

	/**
	 * Returns a set of random bytes
	 *
	 * @param int $size
	 *
	 * @return string
	 */

	public static function randomBytes( $size=32 )
	{

		$bytes = openssl_random_pseudo_bytes ( $size );

		if( empty( $bytes ) )
		{

			throw new ApplicationException();
		}

		return base64_encode( $bytes );
	}

	/**
	 * MD5 encrypts a piece of data, can take a string
	 *
	 * @param $data
	 *
	 * @param null $salt
	 *
	 * @return array|null|string
	 */

	public static function md5( $data, $salt=null )
	{

		if( is_string( $data ) )
		{

			if( $salt != null )
			{

				return md5( $data . $salt );
			}

			return md5( $data );
		}

		if( is_array( $data ) )
		{

			$sub = function( $argument, $salt=null )
			{

				if( is_array( $argument ) )
				{

					$array = array();

					foreach( $argument as $key=>$value )
					{

						if( $salt != null )
						{

							$array[ $key ] = md5( $value . $salt );
						}
						else
						{

							$array[ $key ] = md5( $value );
						}
					}

					return $array;
				}

				return md5( $argument );
			};

			$return = array();

			foreach( $data as $key=>$value )
			{

				if( is_array( $value ) )
				{

					$return[ $key ] = $sub( $value, $salt  );
				}
				else
				{

					if( $salt != null )
					{

						$array[ $key ] = md5( $value . $salt );
					}
					else
					{

						$return[ $key ] = md5( $value );
					}
				}
			}

			if( empty( $return ) )
			{

				throw new ApplicationException();
			}

			return $return;
		}

		return null;
	}

	/**
	 * Does SHA1 encryption
	 *
	 * @param $data
	 *
	 * @param null $salt
	 *
	 * @return array|null|string
	 */

	public static function sha1( $data, $salt=null )
	{

		if( is_string( $data ) )
		{

			if( $salt != null )
			{

				return sha1( $data . $salt );
			}

			return sha1( $data );
		}
	}
}
<?php

	namespace Framework\Application\Utilities;

	/**
	 * Lewis Lancaster 2016
	 *
	 * Class Cyphers
	 *
	 * @package Framework\Application\Utilities
	 */

	use Framework\Exceptions\ApplicationException;

	class Cyphers
	{

		/**
		 * Gets the IV, takes an IV size
		 *
		 * @param $iv_size
		 *
		 * @return string
		 */

		public static function getIV($iv_size)
		{

			return mcrypt_create_iv($iv_size, MCRYPT_RAND);
		}

		/**
		 * Gets the size
		 *
		 * @return int
		 */

		public static function getSize()
		{

			return mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
		}

		/**
		 * Encrypts a set of data
		 *
		 * @param $data
		 *
		 * @param $key
		 *
		 * @param $iv
		 *
		 * @return string
		 */

		public static function encryptData($data, $key, $iv)
		{

			$cipher = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $data, MCRYPT_MODE_CBC, $iv);

			if (empty($cipher))
			{

				throw new ApplicationException();
			}

			return base64_encode($iv . $cipher);
		}

		/**
		 * Encrypts an array
		 *
		 * @param array $array
		 *
		 * @param null $key
		 *
		 * @return array|string
		 */

		public static function encryptArray(array $array, $key = null, $json = true)
		{

			if ($key == null)
			{

				$key = self::randomKey();
			}

			if (empty($array))
			{

				throw new ApplicationException();
			}

			$iv_size = self::getSize();

			if (empty($iv_size))
			{

				throw new ApplicationException();
			}

			$iv = self::getIV($iv_size);

			if (empty($iv))
			{

				throw new ApplicationException();
			}

			$result = array();

			foreach ($array as $index => $value)
			{

				$result['array'][self::encryptData($index, $key, $iv)] = self::encryptData($value, $key, $iv);
			}

			$result['key'] = base64_encode($key);

			$result['iv_size'] = base64_encode($iv_size);

			if ($json == true)
			{

				return json_encode($result, JSON_PRETTY_PRINT);
			}

			return $result;
		}

		/**
		 * Decrypts Json into array
		 *
		 * @param $json
		 *
		 * @return array
		 */

		public static function decryptJsonToArray($json)
		{

			$array = json_decode($json, true);

			if (empty($array))
			{

				throw new ApplicationException();
			}

			if (isset($array['key']) == false || isset($array['iv_size']) == false || isset($array['array']) == false)
			{

				throw new ApplicationException();
			}

			$result = array();

			foreach ($array['array'] as $key => $value)
			{

				$result[self::decryptData($key, base64_decode($array['iv_size']), base64_decode($array['key']))] =
					self::decryptData($value, base64_decode($array['iv_size']), base64_decode($array['key']));
			}

			if (empty($result))
			{

				throw new ApplicationException();
			}

			return $result;
		}

		/**
		 * Decrypts a set of data
		 *
		 * @param $data
		 *
		 * @param $iv_size
		 *
		 * @param $key
		 *
		 * @return string
		 */

		public static function decryptData($data, $iv_size, $key)
		{

			if (self::isBase64($data) == false)
			{

				throw new ApplicationException();
			}

			$data = base64_decode($data);

			if (empty($data))
			{

				throw new ApplicationException();
			}

			return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, self::getEncryptedData($data, $iv_size), MCRYPT_MODE_CBC, self::getEncryptedIV($data, $iv_size)));
		}

		/**
		 * Gets the encrypted IV
		 *
		 * @param $data
		 *
		 * @param $iv_size
		 *
		 * @return string
		 */

		private static function getEncryptedIV($data, $iv_size)
		{

			if (self::isBase64($data) == false)
			{

				throw new ApplicationException();
			}


			return substr($data, 0, $iv_size);
		}

		/**
		 * Gets the decrypted data
		 *
		 * @param $data
		 *
		 * @param $iv_size
		 *
		 * @return string
		 */

		private static function getEncryptedData($data, $iv_size)
		{

			if (self::isBase64($data) == false)
			{

				throw new ApplicationException();
			}


			return substr($data, $iv_size);
		}

		/**
		 * Checks if a string is base 64
		 *
		 * @param $data
		 *
		 * @return bool
		 */

		private static function isBase64($data)
		{

			if (base64_decode($data) == false)
			{

				return false;
			}

			return true;
		}

		/**
		 * Generates a truly random key
		 *
		 * @return string
		 */

		private static function randomKey()
		{

			return openssl_random_pseudo_bytes(32);
		}
	}
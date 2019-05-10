<?php

	namespace Framework\Application\Utilities;

	use Framework\Exceptions\ApplicationException;

	/**
	 * Class OpenSSL
	 *
	 * @package Framework\Application\Utilities
	 */
	class OpenSSL
	{

		public function encrypt($data, $key, $iv, $cipher = 'AES-128-CBC')
		{

			return base64_encode(openssl_encrypt($data, $cipher, $key, 0, $iv));
		}

		public function encryptArray($array = [], $key, $iv, $cipher = 'AES-128-CBC')
		{

			if (empty($array))
			{

				throw new ApplicationException('Array must not be empty');
			}

			$encrypted = [];

			foreach ($array as $item => $value)
			{

				if (is_array($value))
				{

					$encrypted[$this->encrypt($item, $key, $iv, $cipher)] = $this->encryptArray($value, $key, $iv, $cipher);
				}
				else if (is_string($value))
				{

					$encrypted[$this->encrypt($item, $key, $iv, $cipher)] = $this->encrypt($value, $key, $iv, $cipher);
				}
				else
				{

					throw new ApplicationException('Unknown type');
				}
			}

			return $encrypted;
		}

		public function getKey($length = 32)
		{

			return base64_encode(openssl_random_pseudo_bytes($length));
		}

		public function getIV($cipher = 'AES-128-CBC')
		{

			return openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher));
		}

		public function decrypt($data, $key, $iv, $cipher = 'AES-128-CBC')
		{

			return openssl_decrypt(base64_decode($data), $cipher, $key, $options = OPENSSL_RAW_DATA, $iv);
		}

		public function decryptArray($array = [], $key, $iv, $cipher = 'AES-128-CBC')
		{

			if (empty($array))
			{

				throw new ApplicationException('Array must not be empty');
			}

			$decrypted = [];

			foreach ($array as $item => $value)
			{

				if (is_array($value))
				{

					$decrypted[$this->decrypt($item, $key, $iv, $cipher)] = $this->decryptArray($value, $key, $iv, $cipher);
				}
				else if (is_string($value))
				{

					$decrypted[$this->decrypt($item, $key, $iv, $cipher)] = $this->decrypt($value, $key, $iv, $cipher);
				}
				else
				{

					throw new ApplicationException('Unknown type');
				}
			}

			return $decrypted;
		}
	}
<?php
	declare(strict_types=1);

	namespace Framework\Application\UtilitiesV2;


	/**
	 * Class OpenSSL
	 * @package Framework\Application\UtilitiesV2
	 */
	class OpenSSL
	{

		protected $cipher;

		/**
		 * OpenSSL constructor.
		 *
		 * @param string $cipher
		 *
		 * @throws \Error
		 */

		public function __construct($cipher = "AES-128-CBC")
		{

			if ($this->check($cipher) == false)
				throw new \Error("Cipher invalid");

			$this->cipher = $cipher;
		}

		/**
		 * @param array $json
		 * @param $key
		 * @param $iv
		 * @param bool $add_decrypt_info
		 *
		 * @return array
		 */

		public function encrypt(array $json, $key, $iv, $add_decrypt_info = true)
		{

			$array = [];

			foreach ($json as $index => $value)
			{

				$array[$this->encryptText($index, $key, $iv)] = $this->encryptText($value, $key, $iv);
			}

			if ($add_decrypt_info)
				$array["info"] = [
					"key" => $key,
					"iv" => base64_encode($iv)
				];

			return $array;
		}

		/**
		 * @param array $json
		 * @param $key
		 * @param $iv
		 *
		 * @return array
		 */

		public function decrypt(array $json, $key, $iv)
		{

			$array = [];

			foreach ($json as $index => $value)
			{


				if ($index == "info")
					continue;

				$array[$this->decryptText($index, $key, $iv)] = $this->decryptText($value, $key, $iv);
			}

			return $array;
		}

		/**
		 * @param string $text
		 * @param string $key
		 * @param $iv
		 *
		 * @return string
		 */

		private function encryptText(string $text, string $key, $iv)
		{

			return (openssl_encrypt($text, $this->cipher, $key, $options = 0, $iv));
		}

		/**
		 * @param string $text
		 * @param string $key
		 * @param $iv
		 *
		 * @return string
		 */

		private function decryptText(string $text, string $key, $iv)
		{

			return (openssl_decrypt($text, $this->cipher, $key, OPENSSL_RAW_DATA, $iv));
		}

		/**
		 * @return string
		 */

		public function iv()
		{

			return (openssl_random_pseudo_bytes($this->getLength()));
		}

		/**
		 * @return int
		 */

		private function getLength()
		{

			return (openssl_cipher_iv_length($this->cipher));
		}

		/**
		 * @return string
		 */

		public function generateKey()
		{

			return (base64_encode(openssl_random_pseudo_bytes(32)));
		}

		/**
		 * @param $cipher
		 *
		 * @return bool
		 */

		private function check($cipher)
		{

			if (in_array($cipher, openssl_get_cipher_methods()))
				return true;

			return false;
		}
	}
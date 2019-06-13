<?php
	declare(strict_types=1);

	namespace Framework\Syscrack\Game;

	/**
	 * Lewis Lancaster 2016
	 *
	 * Class Hardware
	 *
	 * @package Framework\Syscrack\Game
	 */

	use Framework\Application\Settings;
	use Framework\Database\Tables\Computer;

	/**
	 * Class Hardware
	 * @package Framework\Syscrack\Game
	 */
	class Hardware
	{

		protected $computers;

		public function __construct()
		{

			$this->computers = new Computer();
		}

		/**
		 * @param $computerid
		 *
		 * @return mixed
		 */
		public function getDownloadSpeed($computerid)
		{

			return $this->getHardware($computerid)[Settings::setting('internet_download_type')]['value'];
		}

		/**
		 * @param $computerid
		 *
		 * @return mixed
		 */
		public function getUploadSpeed($computerid)
		{

			return $this->getHardware($computerid)[Settings::setting('internet_upload_type')]['value'];
		}

		/**
		 * @param $computerid
		 *
		 * @return mixed
		 */
		public function getCPUSpeed($computerid)
		{

			return $this->getHardware($computerid)[Settings::setting('hardware_type_cpu')]['value'];
		}

		/**
		 * @param $computerid
		 *
		 * @return mixed
		 */
		public function getGPUSpeed($computerid)
		{

			return $this->getHardware($computerid)[Settings::setting('gpu_type')]['value'];
		}

		/**
		 * @param $computerid
		 * @param $type
		 * @param $value
		 */
		public function updateHardware($computerid, $type, $value)
		{

			$hardware = $this->getHardware($computerid);

			if (isset($hardware[$type]) == false)
			{

				throw new \Error();
			}

			$hardware[$type] = [
				'value' => $value
			];

			$this->computers->updateComputer($computerid, [
				'hardware' => json_encode($hardware, JSON_PRETTY_PRINT)
			]);
		}

		/**
		 * @param $computerid
		 * @param $type
		 * @param $value
		 */
		public function addHardware($computerid, $type, $value)
		{

			$hardware = $this->getHardware($computerid);

			if (isset($hardware[$type]))
			{

				throw new \Error();
			}

			$hardware[$type] = [
				'value' => $value
			];

			$this->computers->updateComputer($computerid, [
				'hardware' => json_encode($hardware, JSON_PRETTY_PRINT)
			]);
		}

		/**
		 * @param $computerid
		 * @param $type
		 *
		 * @return null
		 */
		public function getHardwareType($computerid, $type)
		{

			if (isset($this->getHardware($computerid)[$type]) == false)
			{

				return null;
			}

			return $this->getHardware($computerid)[$type];
		}

		/**
		 * @param $computerid
		 * @param $type
		 *
		 * @return bool
		 */
		public function hasHardwareType($computerid, $type)
		{

			$hardware = $this->getHardware($computerid);

			if (empty($hardware))
			{

				return false;
			}

			if (isset($hardware[$type]) == false)
			{

				return false;
			}

			return true;
		}

		/**
		 * @param $computerid
		 *
		 * @return mixed
		 */
		public function getHardware($computerid)
		{

			return json_decode($this->computers->getComputer($computerid)->hardware, true);
		}
	}
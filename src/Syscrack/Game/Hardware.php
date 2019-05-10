<?php

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
	use Framework\Exceptions\SyscrackException;

	class Hardware
	{

		protected $computers;

		public function __construct()
		{

			$this->computers = new Computer();
		}

		public function getDownloadSpeed($computerid)
		{

			return $this->getHardware($computerid)[Settings::setting('syscrack_internet_download_type')]['value'];
		}

		public function getUploadSpeed($computerid)
		{

			return $this->getHardware($computerid)[Settings::setting('syscrack_internet_upload_type')]['value'];
		}

		public function getCPUSpeed($computerid)
		{

			return $this->getHardware($computerid)[Settings::setting('syscrack_hardware_cpu_type')]['value'];
		}

		public function getGPUSpeed($computerid)
		{

			return $this->getHardware($computerid)[Settings::setting('syscrack_gpu_type')]['value'];
		}

		public function updateHardware($computerid, $type, $value)
		{

			$hardware = $this->getHardware($computerid);

			if (isset($hardware[$type]) == false)
			{

				throw new SyscrackException();
			}

			$hardware[$type] = array(
				'value' => $value
			);

			$this->computers->updateComputer($computerid, array(
				'hardware' => json_encode($hardware, JSON_PRETTY_PRINT)
			));
		}

		public function addHardware($computerid, $type, $value)
		{

			$hardware = $this->getHardware($computerid);

			if (isset($hardware[$type]))
			{

				throw new SyscrackException();
			}

			$hardware[$type] = array(
				'value' => $value
			);

			$this->computers->updateComputer($computerid, array(
				'hardware' => json_encode($hardware, JSON_PRETTY_PRINT)
			));
		}

		public function getHardwareType($computerid, $type)
		{

			if (isset($this->getHardware($computerid)[$type]) == false)
			{

				return null;
			}

			return $this->getHardware($computerid)[$type];
		}

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

		public function getHardware($computerid)
		{

			return json_decode($this->computers->getComputer($computerid)->hardware, true);
		}
	}
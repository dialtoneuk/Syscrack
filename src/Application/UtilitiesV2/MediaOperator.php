<?php
	/**
	 * Created by PhpStorm.
	 * User: lewis
	 * Date: 10/07/2018
	 * Time: 21:29
	 */

	namespace Framework\Application\UtilitiesV2;

	use FFMpeg\FFMpeg;
	use FFMpeg\Format\Audio\Mp3;
	use FFMpeg\Format\Audio\Wav;
	use Framework\Application;

	class MediaOperator
	{

		/**
		 * @var FFMpeg
		 */

		protected $ffmpeg;

		/**
		 * @var
		 */

		protected $filepath;

		/**
		 * @var \stdClass
		 */

		protected $config;

		/**
		 * MediaOperator constructor.
		 *
		 * @param $filepath
		 *
		 * @throws \Error
		 */

		public function __construct($filepath)
		{

			if (file_exists(SYSCRACK_ROOT . $filepath) == false)
				throw new \Error("File does not exist");

			$this->config = $this->read();

			if ($this->check() == false)
				throw new \Error("Invalid ffmeg file structure");

			if ($this->config->real)
				$path = $this->config->root;
			else
				$path = SYSCRACK_ROOT . $this->config->root;

			$this->ffmpeg = FFMpeg::create(array(
				'ffmpeg.binaries' => $path . $this->config->files->ffmpeg,
				'ffprobe.binaries' => $path . $this->config->files->ffprobe,
				'timeout' => $this->config->timeout,
				'ffmpeg.threads' => $this->config->threads
			));

			$this->filepath = $filepath;
		}

		/**
		 * @param int $width
		 * @param int $height
		 *
		 * @return \FFMpeg\Media\Waveform
		 */

		public function getWaveform($width = 1024, $height = 248)
		{

			$audio = $this->ffmpeg->open(SYSCRACK_ROOT . $this->filepath);
			return ($audio->waveform($width, $height));
		}

		/**
		 * Desctruct
		 */

		public function __destruct()
		{

			unset($this->ffmpeg);
		}

		/**
		 * @param $filepath
		 *
		 * @throws \Error
		 */

		public function toMP3($filepath)
		{

			if ($this->getExtension() == "mp3")
				throw new \Error("File already a MP3");

			$audio = $this->ffmpeg->open(SYSCRACK_ROOT . $this->filepath);
			$audio->save(new Mp3, $filepath);
		}

		/**
		 * @param $filepath
		 *
		 * @throws \Error
		 */

		public function toWAV($filepath)
		{

			if ($this->getExtension() == "wav")
				throw new \Error("File already a wav");

			$audio = $this->ffmpeg->open(SYSCRACK_ROOT . $this->filepath);
			$audio->save(new Wav, $filepath);
		}

		/**
		 * @return mixed
		 */

		public function getExtension()
		{

			$parts = pathinfo(SYSCRACK_ROOT . $this->filepath);

			return ($parts["extension"]);
		}

		/**
		 * @return bool
		 */
#
		private function check()
		{

			$requirements = [
				"root",
				"real",
				"timeout",
				"threads",
				"files",
			];

			foreach ($requirements as $requirement)
				if (isset($this->config->$requirement) == false)
					return false;

			return true;
		}

		/**
		 * @return mixed
		 * @throws \Error
		 */

		private function read()
		{

			if (file_exists(SYSCRACK_ROOT . Application::globals()->FFMPEG_CONFIG_FILE) == false)
				throw new \Error("FFmpeg file invalid");

			return (json_decode(file_get_contents(SYSCRACK_ROOT . Application::globals()->FFMPEG_CONFIG_FILE)));
		}
	}
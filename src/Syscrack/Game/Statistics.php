<?php

	namespace Framework\Syscrack\Game;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class Statistics
	 *
	 * @package Framework\Syscrack\Game
	 */

	use Framework\Application\Settings;
	use Framework\Application\Utilities\FileSystem;

	class Statistics
	{

		/**
		 * @var array
		 */

		protected $statistics = [];

		/**
		 * Statistics constructor.
		 *
		 * @param bool $autoread
		 */

		public function __construct($autoread = true)
		{

			if ($autoread == true)
			{

				$this->readStatistics();
			}
		}

		/**
		 * Gets a statistic
		 *
		 * @param $statistic
		 *
		 * @return mixed
		 */

		public function getStatistic($statistic)
		{

			if (isset($this->statistics[$statistic]) == false)
			{

				return 0;
			}

			return $this->statistics[$statistic];
		}

		/**
		 * Adds a statistic
		 *
		 * @param $statistic
		 */

		public function addStatistic($statistic, $value = null)
		{

			if (isset($this->statistics[$statistic]) == false)
			{

				$this->statistics[$statistic] = 0;
			}

			if ($value == null)
			{

				$this->statistics[$statistic] = $this->statistics[$statistic] + 1;
			}
			else
			{


				$this->statistics[$statistic] = $this->statistics[$statistic] + $value;
			}

			$this->saveStatistics();
		}

		/**
		 * Saves our statistics to file
		 */

		public function saveStatistics()
		{

			FileSystem::writeJson(Settings::setting('syscrack_statistics_file'), $this->statistics);
		}

		/**
		 * Returns true if we have statistics
		 *
		 * @return bool
		 */

		public function hasStatistics()
		{

			if (FileSystem::exists(Settings::setting('syscrack_statistics_file')) == false)
			{

				return false;
			}

			if (empty($this->readStatistics()))
			{

				return false;
			}

			return true;
		}

		/**
		 * Reads the statistics from the file
		 *
		 * @return array|mixed
		 */

		private function readStatistics()
		{

			if (empty($this->statistics) == false)
			{

				return $this->statistics;
			}

			$this->statistics = FileSystem::readJson(Settings::setting('syscrack_statistics_file'));

			return $this->statistics;
		}
	}
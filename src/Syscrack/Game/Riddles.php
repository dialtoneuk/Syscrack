<?php

	namespace Framework\Syscrack\Game;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class Riddles
	 *
	 * @package Framework\Syscrack\Game
	 */

	use Framework\Application\Settings;
	use Framework\Application\Utilities\FileSystem;
	use Framework\Exceptions\SyscrackException;

	class Riddles
	{

		/**
		 * @var array|mixed
		 */

		protected $riddles = array();

		/**
		 * Riddles constructor.
		 *
		 * @param bool $autoread
		 */

		public function __construct($autoread = true)
		{

			if ($autoread == true)
			{

				if ($this->riddleFileExists() == false)
				{

					throw new SyscrackException('Riddle file does not exist');
				}

				$this->riddles = $this->getRiddles();
			}
		}

		/**
		 * Gets all the riddles
		 *
		 * @return array|mixed
		 */

		public function getAllRiddles()
		{

			if ($this->hasRiddles() == false)
			{

				return $this->getRiddles();
			}

			return $this->riddles;
		}

		/**
		 * Gets the riddles
		 *
		 * @param $riddleid
		 *
		 * @return mixed
		 */

		public function getRiddle($riddleid)
		{

			if ($this->hasRiddles() == false)
			{

				return $this->getRiddles()[$riddleid];
			}

			return $this->riddles[$riddleid];
		}

		/**
		 * Returns true if we have this riddle
		 *
		 * @param $riddleid
		 *
		 * @return bool
		 */

		public function hasRiddle($riddleid)
		{

			if ($this->hasRiddles() == false)
			{

				if (isset($this->getRiddles()[$riddleid]) == false)
				{

					return false;
				}

				return true;
			}

			if (isset($this->riddles[$riddleid]) == false)
			{

				return false;
			}

			return true;
		}

		/**
		 * Checks the riddle answer
		 *
		 * @param $riddleid
		 *
		 * @param $answer
		 *
		 * @return bool
		 */

		public function checkRiddleAnswer($riddleid, $answer)
		{

			$riddle = $this->getRiddle($riddleid);

			if (strtolower($riddle['answer']) == strtolower($answer))
			{

				return true;
			}

			return false;
		}

		/**
		 * Deletes a riddle
		 *
		 * @param $riddleid
		 */

		public function deleteRiddle($riddleid)
		{

			if ($this->hasRiddles() == false)
			{

				$riddles = $this->getRiddles();

				if (isset($riddles[$riddleid]))
				{

					unset($riddles[$riddleid]);
				}

				$this->saveRiddles($riddles);
			}
			else
			{

				if (isset($this->riddles[$riddleid]))
				{

					unset($this->riddles[$riddleid]);
				}

				$this->saveRiddles( $this->riddles );
			}
		}

		/**
		 * Adds a riddle
		 *
		 * @param string $question
		 *
		 * @param string $answer
		 */

		public function addRiddle(string $question, string $answer)
		{

			$riddles = $this->getRiddles();

			$riddles[] = array(
				'question' => $question,
				'answer' => $answer
			);

			$this->saveRiddles($riddles);
		}

		/**
		 * Saves the riddles
		 *
		 * @param array $data
		 */

		private function saveRiddles($data)
		{

			if ($data != null)
			{

				FileSystem::writeJson(Settings::setting('syscrack_riddle_location'), $data);
			}
			else
			{

				FileSystem::writeJson(Settings::setting('sycrack_riddle_location'), $this->riddles);
			}
		}

		/**
		 * Returns true if we have riddles
		 *
		 * @return bool
		 */

		private function hasRiddles()
		{

			if (empty($this->riddles) == true)
			{

				return false;
			}

			return true;
		}

		/**
		 * Return true if this riddle exists
		 *
		 * @return bool
		 */

		private function riddleFileExists()
		{

			if (FileSystem::exists(Settings::setting('syscrack_riddle_location')) == false)
			{

				return false;
			}

			return true;
		}

		/**
		 * Gets the riddles
		 *
		 * @return mixed
		 */

		private function getRiddles()
		{

			return FileSystem::readJson(Settings::setting('syscrack_riddle_location'));
		}
	}
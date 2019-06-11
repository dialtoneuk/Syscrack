<?php
	declare(strict_types=1);

	namespace Framework\Syscrack\Game\Softwares;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class Riddle
	 *
	 * @package Framework\Syscrack\Game\Softwares
	 */

	use Framework\Application\Render;
	use Framework\Application\Utilities\PostHelper;
	use Framework\Syscrack\Game\Bases\BaseSoftware;
	use Framework\Syscrack\Game\Riddles;


	/**
	 * Class Riddle
	 * @package Framework\Syscrack\Game\Softwares
	 */
	class Riddle extends BaseSoftware
	{

		/**
		 * The configuration of this Structure
		 *
		 * @return array
		 */

		public function configuration()
		{

			return [
				'uniquename' => 'riddle',
				'extension' => '.exe',
				'type' => 'riddle',
				'viewable' => true,
				'removable' => true,
				'installable' => true,
				'executable' => true,
				'keepdata' => true
			];
		}

		/**
		 * @param $softwareid
		 * @param $userid
		 * @param $computerid
		 *
		 * @return mixed|void
		 */

		public function onExecuted($softwareid, $userid, $computerid)
		{

			if (PostHelper::hasPostData() == false)
			{

				$software = parent::$software->getSoftware($softwareid);

				if (empty($software->data))
				{

					$this->redirectError('Sorry, this riddle is invalid and corrupted.');
				}

				$data = json_decode($software->data, true);

				if (isset($data['riddleid']) == false || isset($data['computerid']) == false)
				{

					$this->redirectError('Sorry, this riddle is invalid and corrupted.');
				}

				if (self::$computer->computerExists($data['computerid']) == false)
				{

					$this->redirectError('Sorry, this riddle is invalid and corrupted.');
				}

				Render::view('syscrack/operations/operations.riddle', ['riddleid' => $data['riddleid']]);

				exit;
			}
			else
			{

				$riddles = new Riddles();

				if (PostHelper::checkForRequirements(['answer', 'riddleid']) == false)
				{

					$this->redirectError('Missing information');
				}
				else
				{

					if ($riddles->hasRiddle(PostHelper::getPostData('riddleid', true)) == false)
					{

						$this->redirectError('Incorrect information');
					}
					else
					{

						if ($riddles->checkRiddleAnswer(PostHelper::getPostData('riddleid', true), PostHelper::getPostData('answer')))
						{


							$software = parent::$software->getSoftware($softwareid);
							$data = json_decode($software->data, true);
							$computer = self::$computer->getComputer($data['computerid']);

							$this->redirectSuccess('game/internet/' . $computer->ipaddress);
						}
						else
						{

							$this->redirectError('Sorry, that was the incorrect answer!');
						}
					}
				}
			}
		}
	}
<?php

	namespace Framework\Views\Pages;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class Game
	 *
	 * @package Framework\Views\Pages
	 */

	use Framework\Application\Container;
	use Framework\Application\Render;
	use Framework\Application\Settings;
	use Framework\Application\Utilities\PostHelper;

	use Framework\Syscrack\Game\Bases\BaseComputer;
	use Framework\Syscrack\Game\Finance;
	use Framework\Syscrack\Game\Log;
	use Framework\Syscrack\Game\Metadata;
	use Framework\Syscrack\Game\Operations;
	use Framework\Syscrack\Game\SoftwareTypes;
	use Framework\Syscrack\Game\Types;
	use Framework\Views\BaseClasses\Page as BaseClass;

	class Game extends BaseClass
	{


		/**
		 * @var Operations
		 */

		protected static $operations;

		/**
		 * @var Metadata
		 */

		protected static $metadata;

		/**
		 * @var Log;
		 */

		protected static $log;

		/**
		 * @var Finance
		 */

		protected static $finance;

		/**
		 * @var Types
		 */

		protected static $types;

		/**
		 * @var SoftwareTypes
		 */

		protected static $softwaretypes;


		/**
		 * Game constructor.
		 */

		public function __construct()
		{

			if (isset(self::$operations) == false)
				self::$operations = new Operations();

			if (isset(self::$metadata) == false)
				self::$metadata = new Metadata();

			if (isset(self::$log) == false)
				self::$log = new Log();

			if (isset(self::$finance) == false)
				self::$finance = new Finance();

			if (isset(self::$softwaretypes) == false )
				self::$softwaretypes = new SoftwareTypes();

			if (isset(self::$types) == false )
				self::$types = new Types();

			parent::__construct(true, true, true, false);
		}

		/**
		 * Returns the pages mapping
		 *
		 * @return array
		 */

		public function mapping()
		{

			return array(
				[
					'GET /game/', 'page'
				],
				[
					'POST /game/', 'pageProcess'
				],
				[
					'GET /game/computer/', 'computer'
				],
				[
					'POST /game/computer/switch/@computerid', 'switchProcess'
				],
				[
					'POST /game/computer/', 'computerPurchase'
				],
				[
					'/game/internet/', 'internetBrowser'
				],
				[
					'/game/addressbook/', 'addressBook'
				],
				[
					'/game/accountbook/', 'accountBook'
				],
				[
					'/game/internet/@ipaddress/', 'viewAddress'
				],
				[
					'/game/internet/@ipaddress/@process', 'process'
				],
				[
					'/game/internet/@ipaddress/@process/@softwareid', 'processSoftware'
				]
			);
		}

		/**
		 * Switches your computer
		 */

		public function pageProcess()
		{

			if (PostHelper::hasPostData() == false || PostHelper::checkForRequirements(['action', 'computerid']) == false)
				$this->page();
			else
			{

				$action = PostHelper::getPostData('action');
				$computerid = PostHelper::getPostData('computerid');

				if (self::$computer->computerExists($computerid) == false)
					$this->page();
				else
				{

					if ($action == "switch")
					{

						if (self::$computer->getComputer($computerid)->userid != Container::getObject('session')->userid())
							$this->page();
						else
						{

							self::$computer->setCurrentUserComputer($computerid);
							self::$internet->setCurrentConnectedAddress(null);
							Render::redirect('/game/');
						}
					}
				}
			}
		}

		/**
		 * Game Index
		 */

		public function page()
		{

			$this->getRender('syscrack/page.game', [], true, self::$session->userid(), self::$computer->computerid() );
		}

		/**
		 * BaseComputer
		 */

		public function computer()
		{

			$computer = self::$computer->getComputer(self::$computer->computerid());

			$this->getRender('syscrack/page.game.computer', ['computer' => $computer, 'accounts' => self::$finance->getUserBankAccounts(self::$session->userid())], true, self::$session->userid(), self::$computer->computerid());
		}

		/**
		 *
		 */

		public function computerPurchase()
		{

			if (PostHelper::hasPostData() == false)
				$this->formError('Missing Information', 'game/computer/');
			else
			{

				if (PostHelper::checkForRequirements(['accountnumber']) == false)
					$this->formError('Account number is invalid', 'game/computer/');
				else
				{

					if (self::$finance->accountNumberExists(PostHelper::getPostData('accountnumber', true)) == false)
						$this->formError('Account number is invalid', 'game/computer/');
					else
					{

						$account = self::$finance->getByAccountNumber(PostHelper::getPostData('accountnumber', true));

						if ($account->userid !== self::$session->userid())
							$this->formError('Invalid', 'game/computer/');
						else if (self::$finance->canAfford($account->computerid, $account->userid, $this->getVPCPrice(self::$session->userid())) == false)
							$this->formError('Sorry, you cannot afford this transaction!', 'game/computer/');
						else
						{

							$computerid = self::$computer->createComputer(self::$session->userid(), Settings::setting('syscrack_startup_default_computer'), self::$internet->getIP(), [], Settings::setting('syscrack_default_hardware'));

							if (empty($computerid))
								throw new \Error();

							/**
							 * @var $class \Framework\Syscrack\Game\Interfaces\Computer
							 */

							$class = self::$computer->getComputerClass(Settings::setting('syscrack_startup_default_computer'));
							$class->onStartup($computerid, self::$session->userid(), [], Settings::setting('syscrack_default_hardware'));
							self::$finance->withdraw($account->computerid, $account->userid, $this->getVPCPrice(self::$session->userid()));
							$this->formSuccess('game/computer/');
						}
					}
				}
			}
		}

		/**
		 * @param $userid
		 *
		 * @return float|int
		 */

		private function getVPCPrice($userid)
		{

			$computer = self::$computer->getUserComputers($userid);

			if (empty($computer))
				return 0;

			return (count($computer) * (Settings::setting('syscrack_vpc_purchase_price') * Settings::setting('syscrack_vpc_purchase_increase')));
		}

		/**
		 * Switches a users computer
		 *
		 * @param $computerid
		 */

		public function switchProcess($computerid)
		{

			if (self::$computer->computerExists($computerid) == false)
				$this->formError('Invalid computer', 'game/computer/');
			else
			{

				if (self::$computer->getComputer($computerid)->userid != Container::getObject('session')->userid())
					$this->page();
				else
				{

					self::$computer->setCurrentUserComputer($computerid);
					self::$internet->setCurrentConnectedAddress(null);
					$this->formSuccess('game/computer/');
				}
			}
		}

		/**
		 * The address book
		 */

		public function addressBook()
		{

			$this->getRender('syscrack/page.game.addressbook');
		}

		/**
		 * The address book
		 */

		public function accountBook()
		{

			$this->getRender('syscrack/page.game.accountbook');
		}

		/**
		 * Default page
		 */

		public function internetBrowser()
		{

			if (PostHelper::hasPostData())
			{

				if ($this->validAddress() == false)
					$this->formError('404 Not Found', $this->getRedirect() . '/internet');
				else
					$this->redirect($this->getRedirect(PostHelper::getPostData('ipaddress')));
			}
			else
				$this->redirect($this->getRedirect(self::$internet->getComputerAddress(Settings::setting('syscrack_whois_computer'))));
		}

		/**
		 * Views a specific address
		 *
		 * @param $ipaddress
		 */

		public function viewAddress($ipaddress)
		{

			if ($this->validAddress($ipaddress) == false)
				$this->formError('404 Not Found', $this->getRedirect() . '/internet');
			else
			{

				$computer = $this->getComputerByAddress($ipaddress);
				$metadata = [];

				if (self::$metadata->exists($computer->computerid))
					$metadata = self::$metadata->get($computer->computerid);

				if (self::$internet->hasCurrentConnection())
					$connection = self::$internet->getCurrentConnectedAddress();
				else
					$connection = null;

				if ($computer->type == Settings::setting("syscrack_computers_download_type"))
					$downloads = self::$software->getAnonDownloads($computer->computerid);
				else
					$downloads = [];

				/**
				 * @var $class BaseComputer
				 */

				$class = self::$computer->getComputerClass($computer->type);

				if ($class->hasData())
					$data = $class->data($computer->computerid, $computer->userid);
				else
					$data = [];

				if (self::$internet->hasCurrentConnection())
				{

					$tools_software = $this->tools(self::$session->userid(), $computer->computerid, true);
					$softwares = self::$software->getSoftwareOnComputer($computer->computerid);
				}
				else
				{

					$tools_software = [];

					if( self::$user->isAdmin( self::$session->userid() ) )
						$softwares = self::$software->getSoftwareOnComputer($computer->computerid);
					else
						$softwares = [];
				}

				if( self::$user->isAdmin( self::$session->userid() ) )
					$softwaretypes = self::$softwaretypes->get();
				else
					$softwaretypes = [];

				if( self::$user->isAdmin( self::$session->userid() ) )
					$types = self::$types->get();
				else
					$types = [];

				$data = array_merge($data, array('ipaddress' => $ipaddress,
					'connection'        => $connection,
					'computer'          => $computer,
					'metadata'          => $metadata,
					'downloads'         => $downloads,
					'tools_software'    => $tools_software,
					'softwares'         => $softwares,
					'types'             => $types,
					'softwaretypes'     => $softwaretypes,
					'localsoftwares'    => self::$software->getSoftwareOnComputer(self::$computer->computerid())
				));

				if( empty( $metadata ) == false )
					if( isset( $metadata->custom["browserpage"] ) )
						@\Flight::render("resources/browser/" .  $metadata->custom["browserpage"], $data, 'browser_page');

				$this->getRender('syscrack/page.game.internet',
					$data,
					true,
					self::$session->userid(),
					$computer->computerid);
			}
		}

		/**
		 * returns true if the IP address is valid
		 *
		 * @param null $ipaddress
		 *
		 * @return bool
		 */

		private function validAddress($ipaddress = null)
		{

			if ($ipaddress == null)
			{

				if (PostHelper::checkForRequirements(['ipaddress']) == false)
					return false;

				$ipaddress = PostHelper::getPostData('ipaddress');
			}

			if (filter_var($ipaddress, FILTER_VALIDATE_IP) == false)
				return false;

			if (self::$internet->ipExists($ipaddress) == false)
				return false;

			return true;
		}

		/**
		 * @param $ipaddress
		 * @param $process
		 *
		 * @return bool
		 */

		public function process($ipaddress, $process)
		{

			if ($this->validAddress($ipaddress) == false)
				$this->formError('404 Not Found', $this->getRedirect() . '/internet');

			if (self::$operations->hasProcessClass($process) == false)
				$this->formError('Invalid action', $this->getRedirect($ipaddress));
			else
			{

				$computer = self::$computer->getComputer(self::$computer->computerid());

				if (self::$operations->hasProcess($computer->computerid, $process, $ipaddress) == true)
					$this->formError('You already have an action of this nature processing', $this->getRedirect($ipaddress));
				else if ($ipaddress == $computer->ipaddress && self::$operations->allowLocal($process) == false)
					$this->formError('This action must be ran on a remote computer', $this->getRedirect($ipaddress));
				else if (self::$operations->localOnly($process) && $ipaddress !== $computer->ipaddress)
					$this->formError('This action can only be ran locally', $this->getRedirect($ipaddress));
				else if (self::$operations->requireSoftware($process))
					$this->formError('A software is required to preform this action', $this->getRedirect($ipaddress));
				else
				{

					$result = null;
					$class = self::$operations->findProcessClass($process);

					if (self::$operations->allowPost($process) == true && PostHelper::hasPostData() == true)
					{

						if (self::$operations->hasPostRequirements($process) == true)
						{

							if (PostHelper::checkForRequirements(self::$operations->getPostRequirements($process)) == false)
								$this->formError('Missing information', $this->getRedirect($ipaddress));
							else
								$result = $class->onPost(PostHelper::getPost(), $ipaddress, self::$session->userid());
						}
						else
							$result = $class->onPost(PostHelper::getPost(), $ipaddress, self::$session->userid());
					}
					else
						$result = true;

					if ($result === false)
						$this->formError('Post invalid process', $this->getRedirect($ipaddress));
					else if ($result === null)
						return true;
					else
					{

						$class = self::$operations->findProcessClass($process);

						if (self::$operations->allowCustomData($process) == true)
							$data = $this->getCustomData($process, $ipaddress, self::$session->userid());
						else
							$data = [];

						if ($class->onCreation(time(), $computer->computerid, self::$session->userid(), $process, array(
								'ipaddress' => $ipaddress,
								'custom' => $data
							)) == false)
							$this->formError("Error creating process", $class->url($ipaddress));
						else
						{

							$time = $class->getCompletionSpeed($computer->computerid, $process);

							if ($time === null)
							{

								$result = $class->onCompletion(time(), time(), $computer->computerid, self::$session->userid(), $process, array(
									'ipaddress' => $ipaddress,
									'custom' => $data
								));

								if (is_string($result))
									$this->formSuccess($result);
								else if ($result === null)
									exit;
								else if (is_bool($result) && $result == false)
									$this->formError("Error creating process", $class->url($ipaddress));
								else if (is_bool($result) && $result == true)
									$this->formSuccess($class->url($ipaddress));
								else
									throw new \Error("Unknown result from process: " . $process . " => " . print_r($result));
							}
							else
								$this->redirect('processes/' . self::$operations->createProcess($time, $computer->computerid, self::$session->userid(), $process, array(
										'ipaddress' => $ipaddress,
										'custom' => $data
									)));
						}
					}
				}
			}

			return true;
		}

		/**
		 * @param $ipaddress
		 * @param $process
		 * @param $softwareid
		 *
		 * @return bool
		 */

		public function processSoftware($ipaddress, $process, $softwareid)
		{

			if ($this->validAddress($ipaddress) == false)
				$this->formError('404 Not Found', $this->getRedirect($ipaddress));
			else
			{

				$computer = self::$computer->getComputer( self::$computer->computerid() );

				if (self::$operations->hasProcessClass($process) == false)
					$this->formError('Error in action', $this->getRedirect($ipaddress));
				else if (self::$operations->hasProcess($computer->computerid, $process, $ipaddress, $softwareid))
					$this->formError('Multiple actions running', $this->getRedirect($ipaddress));
				else if (self::$operations->allowSoftware($process) == false)
					$this->redirect($this->getRedirect($ipaddress) . $process);
				else if ($ipaddress != self::$internet->getCurrentConnectedAddress() && self::$operations->allowAnonymous($process) == false && self::$operations->allowLocal($process) == false)
					$this->formError('Connection Invalid', $this->getRedirect($ipaddress));
				else if (self::$operations->localOnly($process) && $ipaddress !== $computer->ipaddress)
					$this->formError('Invalid permissions action', $this->getRedirect($ipaddress));
				else if (self::$operations->requireLoggedIn($process) && self::$internet->getCurrentConnectedAddress() != $ipaddress)
					$this->formError('Invalid credentials', $this->getRedirect($ipaddress));
				else if (self::$software->softwareExists($softwareid) == false)
				{
					if (self::$operations->allowAnonymous($process))
						$this->formError("Invalid action", $this->getRedirect($ipaddress));
					else
						$this->formError("Invalid software", $this->getRedirect($ipaddress));
				}
				else
				{

					$target = self::$internet->computer($ipaddress);
					$software = self::$software->getSoftware($softwareid);
					$class = self::$operations->findProcessClass($process);

					if ($target->computerid !== $software->computerid)
						$this->formError("Invalid software", $this->getRedirect($ipaddress));
					else if (self::$software->isEditable($softwareid) == false && self::$operations->isElevatedProcess($process) == false)
						$this->formError("File locked", $this->getRedirect($ipaddress));
					else if (self::$operations->allowPost($process))
					{

						$keys = [];

						if (self::$operations->hasPostRequirements($process))
							if (PostHelper::checkForRequirements(self::$operations->getPostRequirements($process)))
								$keys = PostHelper::returnRequirements(self::$operations->getPostRequirements($process));
							else
								return false;

						$result = $class->onPost($keys, $ipaddress, self::$session->userid());;

						if ($result == false)
							return false;
					}

					$data = [];

					if (self::$operations->allowCustomData($process))
						$data = $this->getCustomData($process, $ipaddress, self::$session->userid());

					$result = $class->onCreation(time(), self::$computer->computerid(), self::$session->userid(), $process, array(
						'ipaddress' => $ipaddress,
						'softwareid' => $softwareid,
						'custom' => $data
					));


					if ($result == false)
						$this->formError("Error creating process", $class->url($ipaddress));
					else
					{

						$time = @$class->getCompletionSpeed(self::$computer->computerid(), $process, $softwareid);

						if ($time === null || $time == false)
						{

							$result = $class->onCompletion(time(), time(), $computer->computerid, self::$session->userid(), $process, array(
								'ipaddress' => $ipaddress,
								'softwareid' => $softwareid,
								'custom' => $data
							));

							if (is_string($result))
								$this->formSuccess($result);
							else if ($result === null)
								exit;
							else if (is_bool($result) && $result == false)
								$this->formError("Error creating process", $class->url($ipaddress));
							else if (is_bool($result) && $result == true)
								$this->formSuccess($class->url($ipaddress));
							else
								throw new \Error("Unknown result from process: " . $process . " => " . print_r($result));
						}
						else
							$this->redirect('processes/' . self::$operations->createProcess($time, $computer->computerid, self::$session->userid(), $process, array(
									'ipaddress' => $ipaddress,
									'softwareid' => $softwareid,
									'custom' => $data
								)));
					}
				}
			}

			return true;
		}

		/**
		 * @param $file
		 * @param array|null $array
		 * @param bool $obclean
		 * @param null $userid
		 * @param null $computerid
		 */

		public function getRender($file, array $array = null, $obclean = false, $userid = null, $computerid = null)
		{

			if (isset($array["tools"]) == false && $userid !== null && $computerid !== null)
				$array["tools"] = $this->tools($userid, $computerid, false);

			iF (isset($array['computers']) == false && $userid !== null)
				$array['computers'] = self::$computer->getUserComputers($userid);

			if (isset($array["accounts"]) == false && $userid !== null)
				$array["accounts"] = self::$finance->getUserBankAccounts($userid);

			if (isset($array["log"]) == false && $computerid !== null)
				$array["log"] = self::$log->getCurrentLog($computerid);

			parent::getRender($file, $array, $obclean, $userid, $computerid);
		}

		/**
		 * Gets the custom data for this operation
		 *
		 * @param $process
		 *
		 * @param $ipaddress
		 *
		 * @param $userid
		 *
		 * @return array|null
		 */

		private function getCustomData($process, $ipaddress, $userid)
		{

			if (self::$operations->allowCustomData($process) == false)
			{

				return null;
			}

			$data = self::$operations->getCustomData($process, $ipaddress, $userid);

			if (empty($data) || $data == null)
			{

				return null;
			}

			if (is_array($data) == false)
			{

				throw new \Error();
			}

			return $data;
		}

		/**
		 * Gets the game page to redirect too
		 *
		 * @param null $ipaddress
		 *
		 * @return string
		 */

		private function getRedirect($ipaddress = null)
		{

			if ($ipaddress)
			{

				return Settings::setting('syscrack_game_page') . '/' . Settings::setting('syscrack_internet_page') . '/' . $ipaddress;
			}

			return Settings::setting('syscrack_game_page');
		}
	}
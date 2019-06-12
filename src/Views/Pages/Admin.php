<?php
	declare(strict_types=1);

	namespace Framework\Views\Pages;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class Admin
	 *
	 * @package Framework\Views\Pages
	 */

	use Framework\Application\Settings;
	use Framework\Application\Utilities\FileSystem;
	use Framework\Application\Utilities\PostHelper;
	use Framework\Application\UtilitiesV2\Conventions\CreatorData;
	use Framework\Syscrack\Game\BrowserPages;
	use Framework\Syscrack\Game\Finance;
	use Framework\Syscrack\Game\Inventory;
	use Framework\Syscrack\Game\Market;
	use Framework\Syscrack\Game\Metadata;
	use Framework\Syscrack\Game\Riddles;
	use Framework\Syscrack\Game\Interfaces\Computer;
	use Framework\Syscrack\Game\Themes;
	use Framework\Syscrack\Game\Types;
	use Framework\Views\BaseClasses\Page as BaseClass;

	/**
	 * Class Admin
	 * @package Framework\Views\Pages
	 */
	class Admin extends BaseClass
	{

		/**
		 * @var Finance
		 */

		protected static $finance;

		/**
		 * @var Themes
		 */

		protected static $themes;

		/**
		 * @var Metadata
		 */

		protected static $metadata;

		/**
		 * @var Types
		 */

		protected static $types;

		/**
		 * @var BrowserPages
		 */

		protected static $browserpages;

		/**
		 * @var Inventory
		 */

		protected static $inventory;

		/**
		 * Admin Error constructor.
		 */

		public function __construct()
		{

			if (isset(self::$finance) == false)
				self::$finance = new Finance();

			if (isset(self::$themes) == false)
				self::$themes = new Themes(true);

			if (isset(self::$metadata) == false)
				self::$metadata = new Metadata();

			if (isset(self::$types) == false)
				self::$types = new Types();

			if (isset(self::$browserpages) == false)
				self::$browserpages = new BrowserPages();

			if( isset( self::$inventory ) == false )
				self::$inventory = new Inventory();

			parent::__construct(true, true, true, false, true);
		}

		/**
		 * Returns the pages mapping
		 *
		 * @return array
		 */

		public function mapping()
		{

			return [
				[
					'/admin/', 'page'
				],
				[
					'GET /admin/computer/', 'computerViewer'
				],
				[
					'POST /admin/computer/', 'computerViewerProcess'
				],
				[
					'GET /admin/computer/edit/@computerid/', 'computerEditor'
				],
				[
					'POST /admin/computer/edit/@computerid/', 'computerEditorProcess'
				],
				[
					'GET /admin/users/', 'usersViewer'
				],
				[
					'POST /admin/users/', 'usersSearch'
				],
				[
					'GET /admin/users/edit/@userid/', 'usersEdit'
				],
				[
					'POST /admin/users/edit/@userid/', 'usersEditProcess'
				],
				[
					'GET /admin/riddles/', 'riddlesViewer'
				],
				[
					'POST /admin/riddles', 'riddlesViewerProcess'
				],
				[
					'GET /admin/riddles/creator/', 'riddlesCreator'
				],
				[
					'POST /admin/riddles/creator', 'riddlesCreatorProcess'
				],
				[
					'GET /admin/computer/creator/', 'computerCreator'
				],
				[
					'POST /admin/computer/creator/', 'computerCreatorProcess'
				],
				[
					'GET /admin/reset/', 'reset'
				],
				[
					'POST /admin/reset/', 'resetProcess'
				],
				[
					'GET /admin/themes/', 'themes'
				],
				[
					'POST /admin/themes/', 'themesProcess'
				],
				[
					'GET /admin/settings/', 'settings'
				],
				[
					'POST /admin/settings/', 'settingsProcess'
				],
				[
					'GET /admin/test/', 'test'
				],
				[
					'POST /admin/test/', 'testProcess'
				]
			];
		}

		/**
		 * Default page
		 */

		public function page()
		{

			$this->getRender('syscrack/page.admin', ['userscount' => count( self::$user->getAllUsers() ),
				'activesessions' => count( self::$session->getActiveSessions() )]);
		}

		/**
		 *
		 */

		public function usersViewer()
		{

			$this->getRender('syscrack/page.admin.users', ['users' => self::$user->getAllUsers()]);
		}

		/**
		 *
		 */

		public function usersSearch()
		{


		}

		/**
		 * @param $userid
		 */

		public function usersEdit($userid)
		{

			if ($this->isUser($userid))
				$this->getRender('syscrack/page.admin.users.edit', ['inventory' => self::$inventory->get( $userid ),
					'computers' => self::$computer->getUserComputers( $userid  )], true, $userid );
			else
				$this->formError('This user does not exist, please try another', 'admin/users/');
		}

		/**
		 * @param $userid
		 */

		public function usersEditProcess($userid)
		{

			if ($this->isUser($userid) == false)
				$this->formError('This user does not exist', "admin/users/edit/" . $userid . "/");
			else
			{

				if ( self::$request->empty()  == false)
					$this->formError('Post data is false', "admin/users/edit/" . $userid);
				else
				{

					if (self::$request->compare(['action']) == false)
						$this->formError('Post data is false', "admin/users/edit/" . $userid);
					else
					{

						$action = self::$request->action;

						if ($action == "group")
							if (self::$request->compare(['group']) == false)
								$this->formError('Post data is false', "admin/users/edit/" . $userid);
							else
							{

								self::$user->updateGroup($userid, self::$request->group);
								$this->formSuccess('admin/users/edit/' . $userid);
							}
					}
				}
			}
		}

		/**
		 * Themes View
		 */

		public function themes()
		{

			$this->getRender('syscrack/page.admin.themes', ["themes" => self::$themes->getThemes(false)], $this->model());
		}

		/**
		 * Test view
		 */

		public function test()
		{

			$this->getRender('syscrack/page.admin.test', ["themes" => self::$themes->getThemes(false), "request" => self::$request->debug()], $this->model());
		}

		/**
		 * Processes test
		 */

		public function testProcess()
		{

			if( self::$request->compare("form") )
				$this->test();
			else
			{
				if( self::$request->compare( "type" ) )
					$type = self::$request->theme;
				else
					$type = "error";

				if( $type == "error" )
					$this->formError("Made error", 'admin/test');
				else
					$this->formSuccess('admin/test' );
			}
		}

		/**
		 * Processes themes
		 */

		public function themesProcess()
		{

			if (self::$request->compare(['theme']) == false)
				$this->formError("This theme does not exist");
			else
			{

				$theme = self::$request->theme;

				if (self::$themes->themeExists($theme) == false)
					$this->formError("This theme does not exist");
				else
					self::$themes->set($theme);
			}

			$this->redirect('admin/themes');
		}

		/**
		 * @param $computerid
		 */

		public function computerEditor($computerid)
		{

			if (parent::$computer->computerExists($computerid) == false)
				$this->formError('This computer does not exist, please try another');
			else
			{
				$computer = parent::$computer->getComputer($computerid);

				$softwares = parent::$software->getSoftwareOnComputer($computer->computerid);

				if (empty($softwares))
					$softwares = [];

				$this->getRender('syscrack/page.admin.computer.edit', [
					'computer'          => $computer,
					'softwares'         => $softwares,
					'localsoftwares'    => parent::$software->getSoftwareOnComputer(self::$computer->computerid()),
					'ipaddress'         => $computer->ipaddress,
					'tools_admin'       => $this->tools()
				], true, self::$session->userid(), self::$computer->computerid());
			}
		}

		/**
		 * @param $computerid
		 */

		public function computerEditorProcess($computerid)
		{

			if (parent::$computer->computerExists($computerid) == false)
				$this->formError('This computer does not exist, please try another', "admin/computer/edit/" . $computerid);

			if (self::$request->empty() == false)
				$this->redirect('admin/computer');
			else
			{

				if (isset( self::$request->action ) == false)
					$this->formError('Incomplete Data', "admin/computer/edit/" . $computerid);
				else
				{

					$action = self::$request->action;

					if ($action == "add")
					{

						$requirements = [
							'name',
							'level',
							'uniquename',
							'size'
						];

						if (self::$request->compare($requirements) == false)
							$this->formError('Incomplete Data', "admin/computer/edit/" . $computerid);
						else
						{

							if (isset( self::$request->text ) && empty( self::$request->text ) == false)
							{

								$customdata = [];
								$customdata["text"] = self::$request->text;
							}
							else
								$customdata = [];

							if (isset($_POST['editable']))
								$customdata['editable'] = true;
							else
								$customdata['editable'] = false;

							if (isset($_POST['anondownloads']))
								$customdata['allowanondownloads'] = true;

							$softwareid = self::$software->createSoftware(
								self::$software->getNameFromClass(
									self::$software->findSoftwareByUniqueName( self::$request->uniquename )
								),
								parent::$computer->getComputer($computerid)->userid,
								$computerid,
								self::$request->name,
								self::$request->level,
								self::$request->size,
								$customdata
							);

							$software = self::$software->getSoftware($softwareid);

							parent::$computer->addSoftware($computerid, $software->softwareid, $software->type);

							if (isset( self::$request->schema ) )
								$this->addToSchema($computerid, $software);

							$this->formSuccess('admin/computer/edit/' . $computerid);
						}
					}
					else if ($action == "stall")
					{

						$requirements = [
							'softwareid',
							'task'
						];

						if (self::$request->compare($requirements) == false)
						{

							$this->formError('Incomplete Data', "admin/computer/edit/" . $computerid);
						}
						else
						{

							if (self::$software->softwareExists(self::$request->softwareid) == false)
							{

								$this->formError('Invalid Software', "admin/computer/edit/" . $computerid);
							}

							if (self::$request->task == 'install')
							{

								self::$software->installSoftware(self::$request->softwareid,
									parent::$computer->getComputer($computerid)->userid);

								parent::$computer->installSoftware($computerid, self::$request->softwareid);
							}
							else
							{

								self::$software->uninstallSoftware(self::$request->softwareid);

								parent::$computer->uninstallSoftware($computerid, self::$request->softwareid);
							}

							$this->formSuccess('admin/computer/edit/' . $computerid);
						}
					}
					else if ($action == "delete")
					{

						$requirements = [
							'softwareid',
						];

						if (self::$request->compare($requirements) == false)
						{

							$this->formError('Incomplete Data', "admin/computer/edit/" . $computerid);
						}
						else
						{

							if (self::$software->softwareExists(self::$request->softwareid) == false)
							{

								$this->formError('Invalid Software', "admin/computer/edit/" . $computerid);
							}

							self::$software->deleteSoftware(self::$request->softwareid);

							parent::$computer->removeSoftware($computerid, self::$request->softwareid);

							$this->formSuccess('admin/computer/edit/' . $computerid);
						}
					}
					else if ($action == "stock")
					{

						$requirements = [
							'name',
							'type',
							'cost',
							'quantity'
						];

						if (self::$request->compare($requirements) == false)
						{

							$this->formError('Incomplete Data', "admin/computer/edit/" . $computerid);
						}
						else
						{

							$market = new Market();

							if (parent::$computer->isMarket($computerid) == false)
							{

								$this->formError('Wrong computer type', "admin/computer/edit/" . $computerid);
							}
							else
							{

								if (self::$request->type == 'hardware')
								{

									if (empty($_POST['value']) || empty($_POST['hardware']))
									{

										$this->formError('Incomplete Data', "admin/computer/edit/" . $computerid);
									}
									else
									{

										$stock = [
											'name'      => self::$request->name,
											'type'      => self::$request->type,
											'price'     => self::$request->price,
											'quantity'  => self::$request->quantity,
											'hardware'  => self::$request->hardware,
											'value'     => self::$request->value
										];

										$market->addStockItem($computerid, base64_encode(openssl_random_pseudo_bytes(16)), $stock);

										$this->formSuccess("admin/computer/edit/" . $computerid);
									}
								}
							}
						}
					}
				}
			}
		}

		/**
		 *
		 */

		public function riddlesViewer()
		{

			$this->getRender('syscrack/page.admin.riddles', []);
		}

		/**
		 *
		 */

		public function riddlesViewerProcess()
		{


		}

		/**
		 *
		 */

		public function riddlesCreator()
		{

			$this->getRender('syscrack/page.admin.riddles.creator', []);
		}

		/**
		 *
		 */

		public function riddlesCreatorProcess()
		{

			if (self::$request->empty() == false)
			{

				$this->formError();
			}
			else
			{

				if (self::$request->compare(['question', 'answer']) == false)
				{

					$this->formError();
				}
				else
				{

					$riddles = new Riddles();

					$riddles->addRiddle(PostHelper::getPostData('question', true), PostHelper::getPostData('answer', true));

					$this->formSuccess();
				}
			}
		}

		/**
		 *
		 */

		public function reset()
		{

			$this->getRender('syscrack/page.admin.reset', []);
		}

		/**
		 *
		 */

		public function resetProcess()
		{

			if (self::$request->empty() == false)
			{

				$this->reset();
			}
			else
			{

				if (self::$request->compare(['resetip']) == true)
				{

					$computer = parent::$computer->getAllComputers();

					foreach ($computer as $computers)
					{

						self::$internet->changeAddress($computers->computerid);
					}
				}

				$this->resetComputer();

				if (self::$request->compare(['clearfinance']) == true)
				{

					$this->cleanAccounts();
				}

				$this->formSuccess('admin/reset');
			}
		}

		/**
		 *
		 */

		public function computerViewer()
		{

			$this->getRender('syscrack/page.admin.computer', []);
		}

		/**
		 *
		 */

		public function computerViewerProcess()
		{

			if (self::$request->empty() == false)
			{

				$this->computerViewer();
			}
			else
			{

				if (self::$request->compare(['query']) == false)
				{

					$this->formError('Please enter a search query', 'admin/computer');
				}

				$query = PostHelper::getPostData('query');

				if (filter_var($query, FILTER_VALIDATE_IP))
				{

					if (self::$internet->ipExists($query) == false)
					{

						$this->formError('Address is invalid', 'admin/computer');
					}

					$this->redirect('admin/computer/' . self::$internet->computer($query)->computerid);
				}
				else
				{

					if (is_numeric($query) == false)
					{

						$this->formError('Invalid query', 'admin/computer');
					}

					if (parent::$computer->computerExists($query) == false)
					{

						$this->formError('BaseComputer not found', 'admin/computer');
					}

					$this->redirect('admin/computer/' . parent::$computer->getComputer($query)->computerid);
				}
			}
		}

		/**
		 * Renders the computer creator page
		 */

		private $path = 'admin/computer/creator';

		/**
		 * @return array
		 */

		private function custom()
		{

			$array = [];

			if (self::$request->compare(["name"]))
				$array["name"] = PostHelper::getPostData("name");

			if (self::$request->compare(["browserpages"]))
				$array["browserpage"] = PostHelper::getPostData("browserpages");

			return ($array);
		}

		/**
		 *
		 */

		public function computerCreator()
		{

			$this->getRender('syscrack/page.admin.computer.creator',
				[
					"types" => self::$types->get(),
					"random_address" => self::$internet->getIP(),
					"browserpages" => self::$browserpages->get()]
			);
		}

		/**
		 * Creates a new computer
		 */

		public function computerCreatorProcess()
		{

			if ( self::$request->empty() )
				$this->formError('Missing information', $this->path);
			else if (self::$request->compare(['userid', 'ipaddress', 'type', 'hardware', 'software']) == false)
				$this->formError('Missing information', $this->path);
			else
			{

				$values = [
					'userid'    => self::$request->userid,
					'ipaddress' => self::$request->ipaddress,
					'type'      => self::$request->type,
					'hardware'  => self::$request->hardware,
					'software'  => self::$request->software
				];

				if ($this->isValidJson($values["hardware"]) == false || $this->isValidJson($values["software"]) == false)
					$this->formError("Json is invalid");
				else
				{

					if (is_numeric($values["userid"]) == false)
						$this->formError("Invalid userid must be numerical");
					else
					{

						$values["userid"] = (int)$values["userid"];
						$values["hardware"] = json_decode($values["hardware"], true);
						$values["software"] = json_decode($values["software"], true);

						$object = new CreatorData($values);

						if ($this->isUser($object->userid) == false)
							$this->formError("Userid is invalid", $this->path);
						else if ($this->validAddress($object->ipaddress) == false)
							$this->formError("Address is invalid", $this->path);
						else if (parent::$computer->hasComputerClass($object->type) == false)
							$this->formError("Unknown type of computer");

						$computerid = parent::$computer->createComputer($object->userid, $object->type, $object->ipaddress, $object->software, $object->hardware);

						/**
						 * @var Computer $class
						 */
						$class = parent::$computer->getComputerClass($object->type);

						if ($class instanceof Computer == false)
							throw new \Error("Instanceof check returned false");

						$class->onStartup($computerid, $object->userid, $object->software, $object->hardware, $this->custom());
						$this->formSuccess('admin/computer/edit/' . $computerid);
					}
				}
			}
		}

		/**
		 * Settings
		 */

		public function settings()
		{

			$this->getRender("syscrack/page.admin.settings", ["admin_settings" => $this->getSettings()], $this->model());
		}

		/**
		 * Settings process
		 */

		public function settingsProcess()
		{

			if (self::$request->compare(["setting"]) == false)
				$this->formError("Missing information", 'admin/settings/');
			else
			{

				$value = @self::$request->value;

				if ($this->isValidJson($value))
					$value = json_decode($value, true);

				Settings::updateSetting( self::$request->setting, $value);
			}

			$this->formSuccess('admin/settings/');
		}

		/**
		 * @return array
		 */

		private function getSettings(): array
		{

			if (FileSystem::exists(Settings::setting("admin_settings_filepath")) == false)
				return [];

			$settings = FileSystem::readJson(Settings::setting("admin_settings_filepath"));
			$results = [];

			foreach ($settings as $setting)
				if (Settings::hasSetting($setting))
					$results[$setting] = Settings::setting($setting);

			return ($results);
		}

		/**
		 * Resets all the computer using the schema file if it is found
		 */

		private function resetComputer()
		{

			$computer = parent::$computer->getAllComputers();

			foreach ($computer as $computers)
			{

				if (parent::$computer->hasComputerClass($computers->type) == false)
					continue;

				/**
				 * @var Computer $class
				 */

				$class = parent::$computer->getComputerClass($computers->type);

				if ($class instanceof Computer == false)
					throw new \Error();


				$class->onReset($computers->computerid);
			}
		}

		/**
		 * Cleans all the bank accounts in the game
		 */

		private function cleanAccounts()
		{

			$accounts = self::$finance->getAllAccounts();

			foreach ($accounts as $account)
				self::$finance->removeAccount($account->computerid, $account->userid);
		}

		/**
		 * @param $computerid
		 * @param $software
		 */

		private function addToSchema($computerid, $software)
		{

			$object = self::$metadata->get($computerid)->contents();
			$object["software"][] = [
				"installed" => $software->installed,
				"level" => $software->level,
				"name" => $software->softwarename,
				"size" => $software->size,
				"uniquename" => $software->uniquename,
				"data" => json_decode($software->data, true)
			];


			self::$metadata->update($computerid, ["software" => $object["software"]]);
		}

		/**
		 * Retuns true the address given is valid
		 *
		 * @param $ipaddress
		 *
		 * @return bool
		 */

		private function validAddress($ipaddress)
		{

			if (filter_var($ipaddress, FILTER_VALIDATE_IP) == false)
				return false;
			else if (self::$internet->ipExists($ipaddress) == true)
				return false;

			return true;
		}

		/**
		 * Returns true if this is valid json
		 *
		 * @param $data
		 *
		 * @return bool
		 */

		private function isValidJson($data)
		{

			json_decode($data, true);

			if (json_last_error() !== JSON_ERROR_NONE)
				return false;


			return true;
		}
	}
<?php
	declare(strict_types=1);

	namespace Framework\Views\BaseClasses;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class Page
	 *
	 * @package Framework\Views
	 */
	
	use Framework\Application\FormContainer;
	use Framework\Application\Render;
	use Framework\Application\Session;
	use Framework\Application\Settings;
	use Framework\Application\UtilitiesV2\Controller\FormMessage;
	use Framework\Application\UtilitiesV2\Debug;
	use Framework\Application\UtilitiesV2\Format;
	use Framework\Syscrack\Game\AddressDatabase;
	use Framework\Syscrack\Game\Computer;
	use Framework\Syscrack\Game\Internet;
	use Framework\Syscrack\Game\Preferences;
	use Framework\Syscrack\Game\Software;
	use Framework\Syscrack\Game\Tool;
	use Framework\Syscrack\Game\Finance;
	use Framework\Views\Structures\Page as Structure;
	use Framework\Syscrack\User;
	use Illuminate\Support\Collection;
	use Framework\Application\UtilitiesV2\Container;
	use Framework\Application;

	/**
	 * Class Page
	 * @package Framework\Views\BaseClasses
	 */
	class Page implements Structure
	{

		/**
		 * @var \stdClass
		 */

		public static $model;

		/**
		 * Model
		 *
		 * @var \stdClass
		 */

		public $_model;

		/**
		 * @var bool
		 */

		private $cleanerrors;

		/**
		 * @var Software
		 */

		protected static $software;

		/**
		 * @var Internet
		 */

		protected static $internet;

		/**
		 * @var Computer
		 */

		protected static $computer;

		/**
		 * @var Session
		 */

		protected static $session;

		/**
		 * @var User
		 */

		protected static $user;

		/**
		 * @var AddressDatabase;
		 */

		protected static $addressbook;

		/**
		 * @var Finance
		 */

		protected static $finance;

		/**
		 * @var Preferences
		 */

		protected static $preferences;

		/**
		 * Page constructor.
		 *
		 * @param bool $autoload
		 * @param bool $session
		 * @param bool $requirelogin
		 * @param bool $clearerrors
		 * @param bool $admin_only
		 * @param bool $global_model
		 */

		public function __construct(bool $autoload = true, bool $session = false, bool $requirelogin = false, bool $clearerrors = true, bool $admin_only = false, $global_model = true )
		{

			if ($session)
			{

				if (session_status() !== PHP_SESSION_ACTIVE)
					session_start();

				if( isset( self::$session ) == false )
					self::$session = new Session();

				if( Container::exist('session') == false )
					Container::add('session', self::$session);
			}

			if ($autoload)
			{

				if (isset(self::$software) == false)
					self::$software = new Software();

				if (isset(self::$internet) == false)
					self::$internet = new Internet();

				if (isset(self::$computer) == false)
					self::$computer = new Computer();

				if (isset(self::$addressbook) == false)
					self::$addressbook = new AddressDatabase();

				if (isset(self::$user) == false)
					self::$user = new User();

				if( isset( self::$finance ) == false )
					self::$finance = new Finance();

				if( isset( self::$preferences ) == false )
					self::$preferences = new Preferences();
			}

			if (Settings::setting('render_mvc_output'))
				$this->_model = new \stdClass();

			if( Settings::setting('render_mvc_output') && $global_model )
				self::$model = $this->model();

			if ($requirelogin && $session)
				if ($this->isLoggedIn() == false)
				{

					Render::redirect(Settings::setting('controller_index_root') . Settings::setting('controller_index_page'));
					exit;
				}
				else
					self::$session->updateLastAction();

			if( $clearerrors )
				$this->cleanerrors = $clearerrors;

			if ($admin_only && $session)
				if ($this->isAdmin() == false)
					Render::redirect(Settings::setting('controller_index_root') . Settings::setting('controller_index_page'));
		}

		/**
		 * @return array|mixed
		 */

		public function mapping()
		{

			return([ __CLASS__ => 'default']);
		}

		/**
		 * @return bool
		 */

		public function default()
		{

			return( true );
		}

		/**
		 * @return bool
		 */

		public function isLoggedIn()
		{

			return (Container::get('session')->isLoggedIn());
		}

		/**
		 * @param $ipaddress
		 *
		 * @return Collection|\stdClass
		 */

		public function getComputerByAddress($ipaddress)
		{

			return (self::$internet->computer($ipaddress));
		}

		/**
		 * @return bool
		 */

		public function isAdmin()
		{

			if (Container::get('session')->isLoggedIn())
				if (self::$user->isAdmin(Container::get('session')->userid()))
					return true;

			return false;
		}

		/**
		 * @param $userid
		 *
		 * @return bool
		 */

		public function isUser($userid)
		{

			if (is_numeric($userid) == false)
				return false;

			return (self::$user->userExists($userid));
		}

		/**
		 * Redirects the user to a page
		 *
		 * @param $path
		 *
		 * @param bool $exit
		 */

		public function redirect($path, $exit = true)
		{

			if (Debug::isPHPUnitTest())
				Debug::echo("Redirecting to: " . $path);
			else
			{

				Render::redirect(Settings::setting('controller_index_root') . $path);
				if( $exit ) exit;
			}
		}

		/**
		 * Creates a new model object
		 *
		 * @return \stdClass
		 */

		public function model()
		{

			if (Settings::setting('render_mvc_output') == false)
				return null;


			if (Container::exist('session') == false)
			{

				$this->_model->session = [
					'active' => false,
					'loggedin' => false,
				];

				$this->_model->userid = null;
			}
			else if (Container::get('session')->isLoggedIn() == false)
			{
				$this->_model->session = [
					'active' => Container::get('session')->sessionActive(),
					'loggedin' => false,
				];


				$this->_model->userid = null;
			}
			else
			{

				$this->_model->session = [
					'active' => Container::get('session')->sessionActive(),
					'loggedin' => Container::get('session')->isLoggedIn(),
					'data' => $_SESSION
				];

				if( isset( self::$user ) )
				{

					$this->_model->userid = Container::get('session')->userid();

					if ( self::$user->isAdmin($this->_model->userid))
						$this->_model->admin = true;

					$this->_model->user = [
						'username'  => htmlspecialchars( self::$user->getUsername( $this->_model->userid ) ),
						'email'     => htmlspecialchars(  self::$user->getEmail( $this->_model->userid ) )
					];

				}

				if( isset( self::$computer ) )
					@$this->_model->computer = @self::$computer->getComputer( @self::$computer->computerid() );
			}

			return $this->_model;
		}

		/**
		 * Redirects the user to an error
		 *
		 * @param string $message
		 *
		 * @param string $path
		 */

		public function formError($message = '', $path = '')
		{


			if( $path == '' )
				$path = $this->getCurrentPage();

			FormContainer::add( new FormMessage( Application::globals()->FORM_ERROR_GENERAL, $message, false ) );
			$contents = FormContainer::contents();

			if( Settings::setting('error_use_session') && empty( $contents ) == false  )
			{

				if( isset( $_SESSION["form"][ $path ] ) == false )
					$_SESSION["form"][ $path ] = [];

				$_SESSION["form"][ $path ] = $contents;
			}

			$this->redirect( $path );
		}

		/**
		 * @param string $path
		 * @param string $optional_message
		 */

		public function formSuccess($path = '', $optional_message = '')
		{

			if( $path == '' )
				$path = $this->getCurrentPage();

			FormContainer::add( new FormMessage( Application::globals()->FORM_MESSAGE_SUCCESS, $optional_message, true ) );
			$contents = FormContainer::contents();

			if( Settings::setting('error_use_session') && empty( $contents ) == false  )
			{

				if( isset( $_SESSION["form"][ $path ] ) == false )
					$_SESSION["form"][ $path ] = [];

				$_SESSION["form"][ $path ] = $contents;
			}

			$this->redirect( $path );
		}

		/**
		 * @param $file
		 * @param array|null $array
		 * @param bool $obclean
		 * @param null $userid
		 * @param null $computerid
		 */

		public function getRender($file, array $array = null, $obclean = true, $userid = null, $computerid = null)
		{

			if (isset($array["softwares"]) == false && $computerid !== null)
				$array["softwares"] = self::$software->getSoftwareOnComputer($computerid);

			if( $userid === null )
				$userid = self::$session->userid();

			if( $computerid == null )
				$computerid = @self::$computer->computerid();

			if (isset($array["user"]) == false && $userid !== null )
			{

				$array["user"] = Format::toArray( self::$user->getUser($userid) );

				if( isset( $array["user"]["password"] ) )
					unset( $array["user"]["password"] );

				if( isset( $array["user"]["salt"] ) )
					unset( $array["user"]["salt"] );

				$array["user"] = Format::toObject( $array["user"] );
			}

			if( isset( $array["cash"] ) == false && $userid !== null )
				$array["cash"] = self::$finance->getTotalUserCash( $userid );

			if( isset( $array["preferences"] ) == false && $userid !== null )
				if( self::$preferences->has( $userid ) )
				{

					$preferences = self::$preferences->get( $userid );

					if( isset( $preferences[ self::$computer->computerid() ] ) )
						$array["preferences"] = $preferences[ self::$computer->computerid() ];
				}

			if( isset( $array["connection"] ) == false )
				$array["connection"] = self::$internet->getCurrentConnectedAddress();

			if( isset( $array["computer"] ) == false && $computerid !== null )
				$array["computer"] = self::$computer->getComputer( $computerid );

			if( isset( $array["currentcomputer"] ) == false )
				if( self::$computer->hasCurrentComputer() )
					$array["currentcomputer"] = self::$computer->getComputer( self::$computer->computerid() );

			if( $this->cleanerrors )
				if( Settings::setting("error_use_session") )
					if( isset( $_SESSION["form"]["drawn"] ) )
						foreach( $_SESSION["form"]["drawn"] as $page=>$contents )
							if( isset( $_SESSION["form"][ $page ][ $contents["key"] ] ) )
								if( time() - ( 60 * 60 * 2  ) < $contents["modified"] )
								{

									unset( $_SESSION["form"]["drawn"][ $page ] );
									unset( $_SESSION["form"][ $page ][ $contents["key"] ] );
								}


			if (isset($array["localsoftwares"]) == false)
				$array["localsoftwares"] = self::$software->getSoftwareOnComputer(self::$computer->computerid());

			if ($obclean)
				ob_clean();

			Render::view($file, $array, $this->model());
		}

		/**
		 * @param null $userid
		 * @param null $computerid
		 * @param bool $software_action
		 *
		 * @return array
		 */

		public function tools($userid = null, $computerid = null, $software_action = false)
		{

			$tools = self::$software->tools();

			if ($userid == null && $computerid == null)
			{

				/** @var Tool $tool */
				$results = array_map(function ($tool)
				{
					return [
						'inputs' => $tool->getInputs(),
						'requirements' => $tool->getRequirements(),
						'action' => $tool->getAction(),
						'description' => @$tool->description,
						'class' => @$tool->class,
						'icon' => @$tool->icon
					];
				}, $tools);

				return ($results);
			}

			$computer = self::$computer->getComputer(self::$computer->computerid());
			$target = self::$computer->getComputer($computerid);
			$results = [];

			/**
			 * @var Tool $tool
			 */

			foreach ($tools as $key => $tool)
			{

				$requirements = $tool->getRequirements();

				if (isset($requirements["empty"]) && $requirements["empty"])
					continue;

				if ($software_action)
				{

					if (isset($requirements["software_action"]) == false)
						continue;
				}
				else
				{
					if (isset($requirements["software_action"]))
						continue;
				}

				if (isset($requirements["connected"]))
					if (self::$internet->hasCurrentConnection() == false)
						continue;
					else if ($target->ipaddress !== self::$internet->getCurrentConnectedAddress())
						continue;

				if ($computer->ipaddress == $target->ipaddress)
					if (@$requirements["local"] == false)
						continue;

				if (isset($requirements["admin"]))
					if (self::$user->isAdmin($userid) == false)
						continue;

				if (isset($requirements['type']))
					if ($target->type !== $requirements['type'])
						continue;

				if (isset($requirements['software']))
					if (self::$computer->hasType($computer->computerid, $requirements['software']) == false)
						continue;

				if (isset($requirements['hide']))
					if (self::$internet->hasCurrentConnection() && self::$internet->getCurrentConnectedAddress() == $target->ipaddress)
						continue;

				if (isset($requirements['hacked']))
					if (self::$addressbook->hasAddress($target->ipaddress, $userid) == $requirements['hacked'])
						continue;

				if (isset($requirements["external"]) && $target->ipaddress == $computer->ipaddress)
					continue;

				$results[$key] = [
					'inputs' => $tool->getInputs(),
					'requirements' => $tool->getRequirements(),
					'action' => $tool->getAction(),
					'description' => @$tool->description,
					'class' => @$tool->class,
					'icon' => @$tool->icon
				];
			}

			return ($results);
		}

		/**
		 * Gets the current page
		 *
		 * @return string
		 */

		public function getCurrentPage()
		{

			$page = $this->getPageSplat();

			if (empty($page))
			{

				return Settings::setting('controller_index_page');
			}

			if (empty(explode('?', $page[0])) == false)
			{

				return explode('?', $page[0])[0];
			}

			return $page[0];
		}

		/**
		 * Gets the entire path in the form of an array
		 *
		 * @return array
		 */

		private function getPageSplat()
		{

			return array_values(array_filter(explode('/', strip_tags($_SERVER['REQUEST_URI']))));
		}
	}
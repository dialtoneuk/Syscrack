<?php

	namespace Framework\Views\BaseClasses;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class Page
	 *
	 * @package Framework\Views
	 */

	use Framework\Application\Container;
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
	use Framework\Syscrack\Game\Software;
	use Framework\Syscrack\Game\Tool;
	use Framework\Syscrack\Game\Utilities\PageHelper;
	use Framework\Syscrack\User;
	use Illuminate\Support\Collection;

	class Page
	{

		/**
		 * Model
		 *
		 * @var \stdClass
		 */

		public $model;

		/**
		 * @var Software
		 */

		public static $software;

		/**
		 * @var Internet
		 */

		public static $internet;

		/**
		 * @var Computer
		 */

		public static $computer;

		/**
		 * @var Session
		 */

		public static $session;

		/**
		 * @var User
		 */

		public static $user;

		/**
		 * @var AddressDatabase;
		 */

		public static $addressbook;

		/**
		 * Page constructor.
		 *
		 * @param bool $autoload
		 * @param bool $session
		 * @param bool $requirelogin
		 * @param bool $clearerrors
		 * @param bool $admin_only
		 */

		public function __construct($autoload = true, $session = false, $requirelogin = false, $clearerrors = true, $admin_only = false)
		{

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
			}

			if (Settings::setting('render_mvc_output'))
				$this->model = new \stdClass();

			if ($session)
			{

				if (session_status() !== PHP_SESSION_ACTIVE)
					session_start();

				self::$session = new Session();
				Container::setObject('session', self::$session);
			}

			if ($requirelogin && $session)
				if ($this->isLoggedIn() == false)
				{

					Render::redirect(Settings::setting('controller_index_root') . Settings::setting('controller_index_page'));
					exit;
				}
				else
					self::$session->updateLastAction();

			/**
			 * if( $clearerrors && $session )
			 * if( $this->isLoggedIn() && isset( $_SESSION["error_time"]) )
			 * if( ( microtime( true ) - $_SESSION["error_time"] ) <= microtime( true ) + ( 60 * 60 * 2 ) )
			 * {
			 * $_SESSION["error"] = null;
			 * $_SESSION["error_page"] = null;
			 * $_SESSION["error_time"] = null;
			 * }
			 **/

			if ($admin_only && $session)
				if ($this->isAdmin() == false)
					Render::redirect(Settings::setting('controller_index_root') . Settings::setting('controller_index_page'));
		}

		/**
		 * @return bool
		 */

		public function isLoggedIn()
		{

			return (Container::getObject('session')->isLoggedIn());
		}

		/**
		 * @param $ipaddress
		 *
		 * @return Collection
		 */

		public function getComputerByAddress($ipaddress)
		{

			return (self::$internet->getComputer($ipaddress));
		}

		/**
		 * @return bool
		 */

		public function isAdmin()
		{

			if (Container::getObject('session')->isLoggedIn())
				if (self::$user->isAdmin(Container::getObject('session')->userid()))
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


			if (Container::hasObject('session') == false)
			{

				$this->model->session = [
					'active' => false,
					'loggedin' => false,
				];

				$this->model->userid = null;
			}
			else if (Container::getObject('session')->isLoggedIn() == false)
			{
				$this->model->session = [
					'active' => Container::getObject('session')->sessionActive(),
					'loggedin' => false,
				];


				$this->model->userid = null;
			}
			else
			{

				$this->model->session = [
					'active' => Container::getObject('session')->sessionActive(),
					'loggedin' => Container::getObject('session')->isLoggedIn(),
					'data' => $_SESSION
				];

				if( isset( self::$user ) )
				{

					$this->model->userid = Container::getObject('session')->userid();

					if ( self::$user->isAdmin($this->model->userid))
						$this->model->admin = true;

					$this->model->user = [
						'username'  => self::$user->getUsername( $this->model->userid ),
						'email'     => self::$user->getEmail( $this->model->userid )
					];

				}

				if( isset( self::$computer ) )
					@$this->model->computer = @self::$computer->getComputer( @self::$computer->computerid() );
			}

			return $this->model;
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

			FormContainer::add( new FormMessage( FORM_ERROR_GENERAL, $message, false ) );
			$contents = FormContainer::contents();

			if( Settings::setting('error_use_session') && empty( $contents ) == false  )
				$_SESSION["form"][] = $contents;

			if( $path == '' )
				$path = "/" . $this->getCurrentPage();

			$this->redirect( $path );
		}

		/**
		 * @param string $path
		 * @param string $optional_message
		 */

		public function formSuccess($path = '', $optional_message = '')
		{

			FormContainer::add( new FormMessage( FORM_MESSAGE_SUCCESS, $optional_message, true ) );
			$contents = FormContainer::contents();

			if( Settings::setting('error_use_session') && empty( $contents ) == false  )
				$_SESSION["form"][] = $contents;

			if( $path == '' )
				$path = "/" . $this->getCurrentPage();

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

			if (isset($array["user"]) == false && $userid !== null)
			{

				$array["user"] = Format::toArray( self::$user->getUser($userid) );

				if( isset( $array["user"]["password"] ) )
					unset( $array["user"]["password"] );

				if( isset( $array["user"]["salt"] ) )
					unset( $array["user"]["salt"] );

				$array["user"] = Format::toObject( $array["user"] );
			}


			if (isset($array["localsoftwares"]) == false)
				$array["localsoftwares"] = self::$software->getSoftwareOnComputer(self::$computer->computerid());

			if ($obclean)
				ob_clean();

			Render::view($file, $array, $this->model());
		}

		public function tools($userid = null, $computerid = null, $software_action = false)
		{

			$tools = self::$software->tools();

			if ($userid == null && $computerid == null)
			{

				$results = [];

				foreach ($tools as $key => $tool) /** @var Tool $tool */
					$results[$key] = [
						'inputs' => $tool->getInputs(),
						'requirements' => $tool->getRequirements(),
						'action' => $tool->getAction(),
						'description' => @$tool->description,
						'class' => @$tool->class,
						'icon' => @$tool->icon
					];

				return ($results);
			}

			$computer = self::$computer->getComputer(self::$computer->computerid());
			$target = self::$computer->getComputer($computerid);
			$results = [];

			/**
			 * @var $tool Tool
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
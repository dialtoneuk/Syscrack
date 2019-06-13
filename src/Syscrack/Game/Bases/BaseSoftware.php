<?php
	declare(strict_types=1);

	namespace Framework\Syscrack\Game\Bases;

	/**
	 * Lewis Lancaster 2017
	 *
	 * Class Software
	 *
	 * @package Framework\Syscrack\Game
	 */

	use Flight;
	use Framework\Application;
	use Framework\Application\UtilitiesV2\Controller\FormMessage;
	use Framework\Application\FormContainer;
	use Framework\Application\UtilitiesV2\Container;
	use Framework\Application\Render;
	use Framework\Application\Settings;
	use Framework\Syscrack\Game\Computer;
	use Framework\Syscrack\Game\Hardware;
	use Framework\Syscrack\Game\Internet;
	use Framework\Syscrack\Game\Log;
	use Framework\Syscrack\Game\Software as Database;
	use Framework\Syscrack\Game\Interfaces\Software;
	use Framework\Syscrack\Game\Tool;
	use Framework\Syscrack\Game\Tab;
	use Framework\Syscrack\Game\Utilities\EmptyTool;
	use Framework\Syscrack\User;
	use Illuminate\Support\Collection;

	/**
	 * Class BaseSoftware
	 * @package Framework\Syscrack\Game\Bases
	 */
	class BaseSoftware implements Software
	{

		/**
		 * @var Database
		 */

		protected static $software;

		/**
		 * @var Hardware
		 */

		protected static $hardware;

		/**
		 * @var BaseComputer
		 */

		protected static $computer;

		/**
		 * @var Log
		 */

		protected static $log;

		/**
		 * @var Internet
		 */

		protected static $internet;

		/**
		 * @var User
		 */

		protected static $user;

		/**
		 * Software constructor.
		 *
		 * @param bool $createclasses
		 */

		public function __construct($createclasses = true)
		{

			if ($createclasses && isset(self::$software) == false)
				self::$software = new Database();

			if ($createclasses && isset(self::$hardware) == false)
				self::$hardware = new Hardware();

			if ($createclasses && isset(self::$computer) == false)
				self::$computer = new computer();

			if ($createclasses && isset(self::$log) == false)
				self::$log = new Log();

			if ($createclasses && isset(self::$internet) == false)
				self::$internet = new Internet();

			if ($createclasses && isset(self::$user) == false)
				self::$user = new User();
		}

		/**
		 * @return array
		 */

		public function configuration()
		{

			return [
				'uniquename' => 'vspam',
				'extension' => '.vspam',
				'type' => 'virus',
				'installable' => true,
				'uninstallable' => true,
				'executable' => false,
				'removable' => false,
				'logins' => false,
			];
		}

		/**
		 * @param $softwareid
		 * @param $userid
		 * @param $computerid
		 *
		 * @return mixed
		 */

		public function onExecuted($softwareid, $userid, $computerid)
		{

			$computer = self::$computer->getComputer($computerid);

			if ($computer->ipaddress == self::$computer->getComputer(self::$computer->computerid())->ipaddress)
				$this->redirect('computer?success');
			else
				$this->redirect('game/internet/' . $this->currentAddress() . '?success');

			return true;
		}

		/**
		 * @param $softwareid
		 * @param $userid
		 * @param $comptuerid
		 *
		 * @return mixed|null
		 */

		public function onInstalled($softwareid, $userid, $comptuerid)
		{

			return true;
		}

		/**
		 * @param $softwareid
		 * @param $userid
		 * @param $computerid
		 *
		 * @return mixed|null
		 */

		public function onUninstalled($softwareid, $userid, $computerid)
		{

			return true;
		}

		/**
		 * @param $softwareid
		 * @param $userid
		 * @param $computerid
		 *
		 * @return bool|mixed
		 */

		public function onLogin($softwareid, $userid, $computerid)
		{

			return true;
		}

		/**
		 * @param $softwareid
		 * @param $userid
		 * @param $computerid
		 * @param $timeran
		 *
		 * @return float
		 */

		public function onCollect($softwareid, $userid, $computerid, $timeran)
		{

			return 0.0;
		}

		/**
		 * @param $softwareid
		 * @param $computerid
		 *
		 * @return mixed|null
		 */

		public function getExecuteCompletionTime($softwareid, $computerid)
		{

			return null;
		}

		/**
		 * @param $computerid
		 * @param $userid
		 * @param $softwareid
		 *
		 * @return array
		 */

		public function data( $computerid = null, $userid = null, $softwareid = null ): array
		{

			return([]);
		}

		/**
		 * @param null $userid
		 * @param null $sofwareid
		 * @param null $computerid
		 *
		 * @return EmptyTool
		 */

		public function tool($userid = null, $sofwareid = null, $computerid = null): Tool
		{

			return (new EmptyTool());
		}

		/**
		 * @param null $userid
		 * @param null $sofwareid
		 * @param null $computerid
		 *
		 * @return Tab
		 */

		public function tab($userid = null, $sofwareid = null, $computerid = null): Tab
		{

			return (new Tab());
		}

		/**
		 * @param $computerid
		 * @param null $userid
		 * @param array $data
		 *
		 * @return int
		 */

		public function addSoftware( $computerid, $userid, array $data )
		{

			if( $userid === null )
				if( Container::exist('session') )
					$userid = Container::get('session')->userid();
				else
					throw new \Error("Session does not exist in container when trying to add software");

			if( is_numeric( $userid ) == false )
				throw new \Error("Userid must be numeric");

			$userid = (int)$userid;

			if( isset( $data["uniquename"] ) == false )
				throw new \Error("Cannot add software with out unique name");

			$class = self::$software->findSoftwareByUniqueName( $data["uniquename"] );
			$softwareid = self::$software->createSoftware( $class,
				$userid, $computerid, @$data["name"],@$data["level"],
				@$data["size"], @$data["data"] );

			$software = self::$software->getSoftware( $softwareid );
			self::$computer->addSoftware( $software->computerid, $software->softwareid, $software->type );

			return( $softwareid );
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

			Flight::redirect(Application::globals()->CONTROLLER_INDEX_ROOT . $path);

			if ($exit)
				exit;
		}

		/**
		 * @param string $path
		 * @param string $message
		 * @param bool $redirect
		 */

		public function formError($message = '', $path='', bool $redirect=true )
		{

			if( $path == "" )
				$path = $this->path();

			FormContainer::add( new FormMessage( Application::globals()->FORM_ERROR_GENERAL, $message, false ) );

			if( $redirect )
				$this->redirect( $path );
		}

		/**
		 * @param string $path
		 * @param string $optional_message
		 * @param bool $redirect
		 */

		public function formSuccess( string $path = '', string $optional_message = '', bool $redirect=true )
		{

			if( $path == "" )
				$path = $this->path();

			FormContainer::add( new FormMessage( Application::globals()->FORM_MESSAGE_SUCCESS, $optional_message, true ) );

			if( $redirect )
				$this->redirect($path, true);
		}

		/**
		 * Gets the page the operation should redirect too
		 *
		 * @param null $ipaddress
		 *
		 * @param bool $local
		 *
		 * @return string
		 */

		public function getRedirect($ipaddress = null, $local = false)
		{

			if ($ipaddress == $this->currentAddress())
				return Settings::setting('computer_page');

			if ($local)
				return Settings::setting('computer_page');

			if ($ipaddress)
				return Settings::setting('game_page')
					. '/'
					. Settings::setting('internet_page')
					. '/'
					. $ipaddress;

			return Settings::setting('game_page');
		}

		/**
		 * @param $file
		 * @param array|null $array
		 * @param bool $default_sets
		 * @param bool $cleanob
		 */

		public function render($file, array $array = null, $default_sets = false, $cleanob = true)
		{

			if ($array !== null)
			{

				if ($default_sets !== false)
				{

					array_merge($array, [
						'software' => self::$software->getSoftwareOnComputer(@$array["computer"]->computerid),
						'user' => self::$user->getUser(Container::get('session')->userid()),
						'computer' => $this->currentComputer()
					]);
				}
			}

			if ($cleanob)
				ob_clean();

			Render::view('syscrack/' . $file, $array);
		}

		/**
		 * Gets the current computer ip address
		 *
		 * @return string
		 */

		public function currentAddress()
		{

			return $this->currentComputer()->ipaddress;
		}

		/**
		 * @var Collection
		 */

		protected static $cache;

		/**
		 * @return Collection|\stdClass
		 */

		public function currentComputer()
		{

			if (isset(self::$cache) == false)
				self::$cache = self::$computer->getComputer(self::$computer->computerid());

			return (self::$cache);
		}

		/**
		 * @param null $computerid
		 *
		 * @return string
		 */

		public function path( $computerid=null )
		{

			if( $computerid !== null )
				if( self::$computer->computerExists( $computerid ) )
				{

					$computer = self::$computer->getComputer( $computerid );

					if( self::$computer->hasCurrentComputer() )
						if( $computer->computerid == self::$computer->computerid() )
							$path = "computer/";
						else
							$path = "game/" . $computer->ipaddress . "/";
					else
						$path = "game/" . $computer->ipaddress . "/";
				}
				else
					$path = $_SERVER["REQUEST_URI"];
			else
				$path = $_SERVER["REQUEST_URI"];

			return $path;
		}
	}
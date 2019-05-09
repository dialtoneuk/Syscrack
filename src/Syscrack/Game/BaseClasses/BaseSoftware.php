<?php
    namespace Framework\Syscrack\Game\BaseClasses;

    /**
     * Lewis Lancaster 2017
     *
     * Class Software
     *
     * @package Framework\Syscrack\Game
     */

    use Flight;
    use Framework\Application\Container;
    use Framework\Application\Render;
    use Framework\Application\Settings;
    use Framework\Syscrack\Game\computer;
    use Framework\Syscrack\Game\Hardware;
    use Framework\Syscrack\Game\Internet;
    use Framework\Syscrack\Game\Log;
    use Framework\Syscrack\Game\Software as Database;
    use Framework\Syscrack\Game\Structures\Software;
    use Framework\Syscrack\Game\Tool;
    use Framework\Syscrack\Game\Utilities\EmptyTool;
    use Framework\Syscrack\User;
    use Illuminate\Support\Collection;

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
         * @var Computer
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

        public function __construct($createclasses=true )
        {

            if( $createclasses && isset(  self::$software ) == false )
                self::$software = new Database();

            if( $createclasses && isset( self::$hardware ) == false )
                self::$hardware = new Hardware();

            if( $createclasses && isset( self::$computer ) == false )
                self::$computer = new computer();

            if( $createclasses && isset( self::$log ) == false )
                self::$log = new Log();

            if( $createclasses && isset( self::$internet ) == false )
                self::$internet = new Internet();

            if( $createclasses && isset( self::$user ) == false )
                self::$user = new User();
        }

        /**
         * @return array
         */

        public function configuration()
        {

            return array(
                'uniquename'    => 'vspam',
                'extension'     => '.vspam',
                'type'          => 'virus',
                'installable'   => true,
                'uninstallable' => true,
                'executable'    => false,
                'removable'     => false,
                'logins'        => false,
            );
        }

        /**
         * @param $softwareid
         * @param $userid
         * @param $computerid
         * @return mixed
         */

        public function onExecuted($softwareid, $userid, $computerid)
        {

            $computer = self::$computer->getComputer( $computerid );

            if( $computer->ipaddress == self::$computer->getComputer( self::$computer->computerid() )->ipaddress )
                $this->redirect('computer?success');
            else
                $this->redirect('game/internet/' . $this->currentAddress() . '?success' );
        }

        /**
         * @param $softwareid
         * @param $userid
         * @param $comptuerid
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
         * @return float
         */

        public function onCollect($softwareid, $userid, $computerid, $timeran)
        {

            return 0.0;
        }

        /**
         * @param $softwareid
         * @param $computerid
         * @return mixed|null
         */

        public function getExecuteCompletionTime($softwareid, $computerid)
        {

            return null;
        }

        /**
         * @param null $userid
         * @param null $sofwareid
         * @param null $computerid
         * @return EmptyTool
         */

        public function tool($userid=null, $sofwareid=null, $computerid=null): Tool
        {

            return( new EmptyTool() );
        }

        /**
         * Redirects the user to a page
         *
         * @param $path
         *
         * @param bool $exit
         */

        public function redirect( $path, $exit=true )
        {

            Flight::redirect( Settings::setting('controller_index_root') . $path );

            if( $exit )
                exit;
        }

        /**
         * Redirects the user to an error
         *
         * @param string $message
         *
         * @param string $path
         */

        public function redirectError($message = '', $path = '')
        {

            if( Settings::setting('error_use_session') )
            {

                $_SESSION['error'] = $message;

                if( $path !== '' )
                {
                    if (empty(explode('/', $path)))
                        $_SESSION['error_page'] = explode('/', $path)[0];
                    else
                        if (substr($path, 0, 1) == '/')
                            $_SESSION['error_page'] = substr($path, 1);
                        else
                            $_SESSION['error_page'] = $path;
                }
                else
                    $_SESSION['error_page'] = $this->page();

                if ($path !== '')
                    $this->redirect( $path . '?error' );
                else
                    $this->redirect( $this->page() . '?error' );
            }
            else
            {

                if ($path !== '')
                    $this->redirect( $path . '?error=' . $message );

                else
                    $this->redirect( $this->page() . '?error=' . $message );
            }
        }

        /**
         * Redirects the user to a success
         *
         * @param string $path
         */

        public function redirectSuccess($path = '')
        {

            if ($path !== '')
                $this->redirect( $path . '?success' );

            $this->redirect( $this->page() . '?success', true );
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

        public function getRedirect( $ipaddress=null, $local=false )
        {

            if( $ipaddress == $this->currentAddress() )
                return Settings::setting('syscrack_computers_page');

            if( $local )
                return Settings::setting('syscrack_computers_page');

            if( $ipaddress )
                return Settings::setting('syscrack_game_page')
                    . '/'
                    . Settings::setting('syscrack_internet_page')
                    . '/'
                    . $ipaddress;

            return Settings::setting('syscrack_game_page');
        }

        /**
         * @param $file
         * @param array|null $array
         * @param bool $default_sets
         * @param bool $cleanob
         */

        public function render( $file, array $array = null, $default_sets = false, $cleanob=true  )
        {

            if( $array !== null )
            {

                if( $default_sets !== false )
                {

                    array_merge( $array, [
                        'software' => self::$software->getSoftwareOnComputer( @$array["computer"]->computerid ),
                        'user'      => self::$user->getUser( Container::getObject('session')->userid()),
                        'computer'  => $this->currentComputer()
                    ]);
                }
            }

            if( $cleanob )
                ob_clean();

            Render::view( 'syscrack/' . $file, $array);
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
         * @return Collection
         */

        public function currentComputer()
        {

            if( isset( self::$cache ) == false )
                self::$cache = self::$computer->getComputer( self::$computer->computerid() );

            return( self::$cache );
        }

        /**
         * Gets the current page
         *
         * @return string
         */

        private function page()
        {

            $page = array_values(array_filter(explode('/', strip_tags( $_SERVER['REQUEST_URI'] ))));

            if( empty( $page ) )
                return Settings::setting('controller_index_page');


            return $page[0];
        }
    }
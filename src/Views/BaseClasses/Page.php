<?php
    namespace Framework\Views\BaseClasses;

    /**
     * Lewis Lancaster 2017
     *
     * Class Page
     *
     * @package Framework\Views
     */

    use Flight;
    use Framework\Application\Container;
    use Framework\Application\Render;
    use Framework\Application\Session;
    use Framework\Application\Settings;
    use Framework\Syscrack\Game\Computer;
    use Framework\Syscrack\Game\Internet;
    use Framework\Syscrack\Game\Software;
    use Framework\Syscrack\Game\Utilities\PageHelper;
    use Framework\Syscrack\User;

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

        public $software;

        /**
         * @var Internet
         */

        public $internet;

        /**
         * @var Computer
         */

        public $computer;

        /**
         * @var Session
         */

        public $session;

        /**
         * @var User
         */

        public $user;

        /**
         * Page constructor.
         * @param bool $autoload
         * @param bool $session
         * @param bool $requirelogin
         * @param bool $clearerrors
         * @param bool $admin_only
         */

        public function __construct($autoload = true, $session=false, $requirelogin=false, $clearerrors=true, $admin_only=false )
        {

            if ($autoload )
            {

                $this->software = new Software();
                $this->internet = new Internet();
                $this->computer = new Computer();
                $this->user = new User();
            }

            if ( Settings::getSetting('render_mvc_output') )
                $this->model = new \stdClass();

            if( $session )
            {

                if (session_status() !== PHP_SESSION_ACTIVE )
                    session_start();

                $this->session = new Session();
                Container::setObject('session', $this->session );
            }

            if( $requirelogin && $session )
                if ( $this->isLoggedIn()  == false)
                    Render::redirect( Settings::getSetting('controller_index_root') . Settings::getSetting('controller_index_page') );
                else
                    $this->session->updateLastAction();

            /**
            if( $clearerrors && $session )
                if( $this->isLoggedIn() && isset( $_SESSION["error_time"]) )
                    if( ( microtime( true ) - $_SESSION["error_time"] ) <= microtime( true ) + ( 60 * 60 * 2 ) )
                    {
                        $_SESSION["error"] = null;
                        $_SESSION["error_page"] = null;
                        $_SESSION["error_time"] = null;
                    }
            **/

            if( $admin_only && $session )
                if( $this->isAdmin() == false )
                    Render::redirect( Settings::getSetting('controller_index_root') . Settings::getSetting('controller_index_page') );
        }

        /**
         * @return bool
         */

        public function isLoggedIn()
        {

            return( Container::getObject('session')->isLoggedIn() );
        }

        /**
         * @return bool
         */

        public function isAdmin()
        {

            if( Container::getObject('session')->isLoggedIn() )
                if( $this->user->isAdmin( Container::getObject('session')->getSessionUser() ) )
                    return true;

            return false;
        }

        /**
         * @param $userid
         * @return bool
         */

        public function isUser( $userid )
        {

            if( is_numeric( $userid ) == false )
                return false;

            return( $this->user->userExists( $userid ) );
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

            Render::redirect( Settings::getSetting('controller_index_root') . $path );

            if( $exit == true )
            {

                exit;
            }
        }

        /**
         * Creates a new model object
         *
         * @return \stdClass
         */

        public function model()
        {

            if ( Settings::getSetting('render_mvc_output') == false )
            {

                return null;
            }

            if ( Container::hasObject('session') == false )
            {

                $this->model->session = [
                    'active' => false,
                    'loggedin' => false,
                ];

                $this->model->userid = null;
            }
            elseif ( Container::getObject('session')->isLoggedIn() == false )
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

                $this->model->userid = Container::getObject('session')->getSessionUser();

                $user = new User();

                if ( $user->isAdmin( $this->model->userid ) )
                {

                    $this->model->admin = true;
                }

                $this->model->user = [
                    'username' => $user->getUsername( $this->model->userid ),
                    'email' => $user->getUsername( $this->model->userid )
                ];

                $this->model->pagehelper = new PageHelper();
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

        public function redirectError($message = '', $path = '')
        {

            if( Settings::getSetting('error_use_session') )
            {

                $_SESSION['error'] = $message;
                $_SESSION['error_time'] = microtime( true );

                if( $path !== '' )
                {

                    if( empty( explode('/', $path ) ) )
                    {

                        $_SESSION['error_page'] = explode('/', $path)[0];
                    }
                    else
                    {

                        if( substr( $path, 0, 1 ) == '/' )
                        {

                            $_SESSION['error_page'] = substr( $path, 1);
                        }
                        else
                        {

                            $_SESSION['error_page'] = $path;
                        }
                    }
                }
                else
                {

                    $_SESSION['error_page'] = $this->getCurrentPage();
                }

                if ($path !== '')
                {

                    $this->redirect( $path . '?error' );
                }
                else
                {

                    $this->redirect( $this->getCurrentPage() . '?error' );
                }
            }
            else
            {

                if ($path !== '')
                {

                    $this->redirect( $path . '?error=' . $message );
                }
                else
                {

                    $this->redirect( $this->getCurrentPage() . '?error=' . $message );
                }
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
            {

                $this->redirect( $path . '?success' );
            }

            $this->redirect( $this->getCurrentPage() . '?success' );
        }

        /**
         * Renders a page
         *
         * @param $file
         *
         * @param array|null $array
         */

        public function getRender($file, array $array = null, $obclean=true )
        {

            if( $obclean )
            {

                ob_clean();
            }

            if( isset( $array["computer_controller"] ) == false )
                $array["computer_controller"] = $this->computer;

            Render::view($file, $array, $this->model() );
        }

        /**
         * Gets the current page
         *
         * @return string
         */

        public function getCurrentPage()
        {

            $page = $this->getPageSplat();

            if( empty( $page ) )
            {

                return Settings::getSetting('controller_index_page');
            }

            if( empty( explode('?', $page[0] ) ) == false )
            {

                return explode('?', $page[0] )[0];
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

            return array_values(array_filter(explode('/', strip_tags( $_SERVER['REQUEST_URI'] ))));
        }
    }
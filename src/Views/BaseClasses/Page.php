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
    use Framework\Syscrack\Game\Computers;
    use Framework\Syscrack\Game\Internet;
    use Framework\Syscrack\Game\Softwares;
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
         * @var Softwares
         */

        public $softwares;

        /**
         * @var Internet
         */

        public $internet;

        /**
         * @var Computers
         */

        public $computers;

        /**
         * Page constructor.
         *
         * @param bool $autoload
         */

        public function __construct($autoload = true, $session=false, $requirelogin=false, $clearerrors=true )
        {

            if ( Settings::getSetting('render_mvc_output') )
            {

                $this->model = new \stdClass();
            }

            if ($autoload == true)
            {

                $this->softwares = new Softwares();

                $this->internet = new Internet();

                $this->computers = new Computers();
            }

            if( $session )
            {

                if (session_status() !== PHP_SESSION_ACTIVE )
                {

                    session_start();
                }

                Container::setObject('session', new Session());

                if( $requirelogin )
                {

                    if (Container::getObject('session')->isLoggedIn() == false)
                    {

                        Render::redirect( Settings::getSetting('controller_index_root') . Settings::getSetting('controller_index_page') );

                        exit;
                    }

                    Container::getObject('session')->updateLastAction();
                }

                if( $clearerrors )
                {

                    if( isset( $_GET['error'] ) == true )
                    {

                        if( isset( $_SESSION['error_page'] ) == false )
                        {

                            //Container::getObject('session')->clearError();
                        }
                    }
                    else
                    {

                        //Container::getObject('session')->clearError();
                    }
                }
            }
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

            Render::view(Settings::getSetting('syscrack_view_location') . $file, $array, $this->model() );
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
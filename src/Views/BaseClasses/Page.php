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
    use Framework\Application\Session;
    use Framework\Application\Settings;
    use Framework\Syscrack\Game\Computer;
    use Framework\Syscrack\Game\Internet;
    use Framework\Syscrack\Game\Softwares;

    class Page
    {

        /**
         * @var Softwares
         */

        public $softwares;

        /**
         * @var Internet
         */

        public $internet;

        /**
         * @var Computer
         */

        public $computer;

        /**
         * Page constructor.
         *
         * @param bool $autoload
         */

        public function __construct($autoload = true, $session=false, $requirelogin=false, $clearerrors=true )
        {

            if ($autoload == true)
            {

                $this->softwares = new Softwares();

                $this->internet = new Internet();

                $this->computer = new Computer();
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

                        Flight::redirect( Settings::getSetting('controller_index_root') . Settings::getSetting('controller_index_page') );

                        exit;
                    }
                }

                if( $clearerrors )
                {

                    if( isset( $_GET['error'] ) == false )
                    {

                        if( isset( $_SESSION['error_page'] ) == false )
                        {

                            Container::getObject('session')->clearError();
                        }
                        else
                        {

                            if( $_SESSION['error_page'] !== $this->getCurrentPage() )
                            {

                                Container::getObject('session')->clearError();
                            }
                        }
                    }
                }
            }
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

                    Flight::redirect( Settings::getSetting('controller_index_root') . $path . '?error');

                    exit;
                }
                else
                {

                    Flight::redirect( Settings::getSetting('controller_index_root') .  $this->getCurrentPage() . '?error' );

                    exit;
                }
            }
            else
            {

                if ($path !== '')
                {

                    Flight::redirect( Settings::getSetting('controller_index_root') . $path . '?error=' . $message );

                    exit;
                }
                else
                {

                    Flight::redirect( Settings::getSetting('controller_index_root') .  $this->getCurrentPage() . '?error=' . $message );

                    exit;
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

                Flight::redirect( Settings::getSetting('controller_index_root') . $path . "?success");
                exit;
            }

            Flight::redirect( Settings::getSetting('controller_index_root') . $this->getCurrentPage() . '?success');
            exit;
        }

        /**
         * Gets the current page
         *
         * @return string
         */

        public function getCurrentPage()
        {

            $page = array_values(array_filter(explode('/', strip_tags( $_SERVER['REQUEST_URI'] ))));

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
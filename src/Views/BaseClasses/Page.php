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

            Flight::redirect( Settings::getSetting('controller_index_root') . $path );

            if( $exit == true )
            {

                exit;
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

            Flight::render(Settings::getSetting('syscrack_view_location') . $file, $array);
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
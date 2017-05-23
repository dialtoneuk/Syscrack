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
    use Framework\Application\Settings;
    use Framework\Syscrack\Game\Computers;
    use Framework\Syscrack\Game\Hardware;
    use Framework\Syscrack\Game\Internet;
    use Framework\Syscrack\Game\Softwares;

    class Software
    {

        /**
         * @var Softwares
         */

        public $softwares;

        /**
         * @var Hardware
         */

        public $hardware;

        /**
         * @var Computers
         */

        public $computer;

        /**
         * @var Internet
         */

        public $internet;

        /**
         * Software constructor.
         *
         * @param bool $createclasses
         */

        public function __construct($createclasses=true )
        {

            if( $createclasses )
            {

                $this->softwares = new Softwares();

                $this->hardware = new Hardware();

                $this->computers = new Computers();

                $this->internet = new Internet();
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

            $this->redirect( $this->getCurrentPage() . '?success', true );
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

            if( $ipaddress == $this->computers->getComputer( $this->computers->getCurrentUserComputer() )->ipaddress )
            {

                return Settings::getSetting('syscrack_computers_page');
            }

            if( $local )
            {

                return Settings::getSetting('syscrack_computers_page');
            }

            if( $ipaddress )
            {

                return Settings::getSetting('syscrack_game_page') . '/' . Settings::getSetting('syscrack_internet_page') . '/' . $ipaddress;
            }

            return Settings::getSetting('syscrack_game_page');
        }

        /**
         * Gets the current computers ip address
         *
         * @return mixed
         */

        public function getCurrentAddress()
        {

            return $this->computers->getComputer( $this->computers->getCurrentUserComputer() )->ipaddress;
        }

        /**
         * Gets the current page
         *
         * @return string
         */

        private function getCurrentPage()
        {

            $page = array_values(array_filter(explode('/', strip_tags( $_SERVER['REQUEST_URI'] ))));

            if( empty( $page ) )
            {

                return Settings::getSetting('controller_index_page');
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
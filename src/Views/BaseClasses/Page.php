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
    use Framework\Syscrack\Game\Computer;
    use Framework\Syscrack\Game\Internet;
    use Framework\Syscrack\Game\Softwares;
    use Framework\Application\Settings;

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

        public function __construct($autoload = true)
        {

            if ($autoload == true)
            {

                $this->softwares = new Softwares();

                $this->internet = new Internet();

                $this->computer = new Computer();
            }
        }

        /**
         * Redirects the user to an error page
         *
         * @param string $message
         *
         * @param string $ipaddress
         */

        public function redirectError($message = '', $path = '')
        {

            if ($path !== '')
            {

                Flight::redirect('/' . $this->getCurrentPage() . $path . "?success");
                exit;
            }

            Flight::redirect('/' .  $this->getCurrentPage() . '?error=' . $message);
            exit;
        }

        /**
         * Redirects the user to a success page
         *
         * @param string $ipaddress
         */

        public function redirectSuccess($path = '')
        {


            if ($path !== '')
            {

                Flight::redirect('/' . $this->getCurrentPage() . $path . "?success");
                exit;
            }

            Flight::redirect('/' . $this->getCurrentPage() . '?success');
            exit;
        }

        /**
         * Gets the current page
         *
         * @return array
         */

        public function getCurrentPage()
        {

            $page = array_values(array_filter(explode('/', $_SERVER['REQUEST_URI'])));

            if( empty( $page ) )
            {

                return Settings::getSetting('controller_index_page');
            }

            return $page[0];
        }
    }
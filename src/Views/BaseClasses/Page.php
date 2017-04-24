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
         * Redirects the user to an error
         *
         * @param string $message
         *
         * @param string $path
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
         * Redirects the user to a success
         *
         * @param string $path
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
         * @return string
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

        /**
         * Gets the entire path in the form of an array
         *
         * @return array
         */

        public function getPageSplat()
        {

            return array_values(array_filter(explode('/', $_SERVER['REQUEST_URI'])));
        }
    }
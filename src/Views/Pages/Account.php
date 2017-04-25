<?php
    namespace Framework\Views\Pages;

    /**
     * Lewis Lancaster 2016
     *
     * Class Account
     *
     * @package Framework\Views\Pages
     */

    use Flight;
    use Framework\Application\Container;
    use Framework\Application\Session;
    use Framework\Application\Settings;
    use Framework\Syscrack\User;
    use Framework\Views\BaseClasses\Page as BaseClass;
    use Framework\Views\Structures\Page;

    class Account extends BaseClass implements Page
    {

        protected $user;

        /**
         * Error constructor.
         */

        public function __construct()
        {

            parent::__construct( false );

            if (session_status() !== PHP_SESSION_ACTIVE)
            {

                session_start();
            }

            if (Container::hasObject('session') == false)
            {

                Container::setObject('session', new Session());
            }

            if (Container::getObject('session')->isLoggedIn() == false)
            {

                Flight::redirect( Settings::getSetting('controller_index_root') . Settings::getSetting('controller_index_page') );

                exit;
            }

            $this->user = new User();
        }

        /**
         * The index page has a special algorithm which allows it to access the root. Only the index can do this.
         *
         * @return array
         */

        public function mapping()
        {

            return array(
                [
                    '/account/logout/', 'logout'
                ]
            );
        }

        /**
         * Default page
         */

        public function logout()
        {

            Container::getObject('session')->cleanupSession( Container::getObject('session')->getSessionUser() );

            session_regenerate_id( true );

            session_destroy();

            unset($_SESSION);

            Flight::redirect( Settings::getSetting('controller_index_root') . Settings::getSetting('controller_index_page') );
        }
    }
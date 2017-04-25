<?php
    namespace Framework\Views\Pages;

    /**
     * Lewis Lancaster 2016
     *
     * Class Admin
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

    class Admin extends BaseClass implements Page
    {

        /**
         * @var User
         */

        protected $user;

        /**
         * Error constructor.
         */

        public function __construct()
        {

            parent::__construct( true );

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

            if( $this->user->isAdmin( Container::getObject('session')->getSessionUser() ) == false )
            {

                Flight::redirect( Settings::getSetting('controller_index_root') . Settings::getSetting('controller_index_page') );

                exit;
            }
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
                    '/admin/', 'page'
                ],
                [
                    '/admin/npcreator/', 'npcCreator'
                ]
            );
        }

        /**
         * Default page
         */

        public function page()
        {

            Flight::render('views/syscrack/page.admin.php');
        }

        /**
         * Renders the NPC Creator page
         */

        public function npcCreator()
        {

            Flight::render('views/syscrack/page.admin.npcreator.php');
        }
    }
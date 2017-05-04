<?php
    namespace Framework\Views\Pages;

    /**
     * Lewis Lancaster 2017
     *
     * Class Admin
     *
     * @package Framework\Views\Pages
     */

    use Flight;
    use Framework\Application\Container;
    use Framework\Application\Settings;
    use Framework\Syscrack\User;
    use Framework\Views\BaseClasses\Page as BaseClass;
    use Framework\Views\Structures\Page as Structure;

    class Admin extends BaseClass implements Structure
    {

        /**
         * @var User
         */

        protected $user;

        /**
         * Admin Error constructor.
         */

        public function __construct()
        {

            parent::__construct( true, true, true, true  );

            if( isset( $this->user ) == false )
            {

                $this->user = new User();
            }

            if( $this->user->isAdmin( Container::getObject('session')->getSessionUser() ) == false )
            {

                Flight::redirect( Settings::getSetting('controller_index_root') . Settings::getSetting('controller_index_page') );

                exit;
            }
        }

        /**
         * Returns the pages mapping
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

            Flight::render('syscrack/page.admin.php');
        }

        /**
         * Renders the NPC Creator page
         */

        public function npcCreator()
        {

            Flight::render('syscrack/page.admin.npcreator.php');
        }
    }
<?php
    namespace Framework\Views\Pages;

    /**
     * Lewis Lancaster 2017
     *
     * Class Account
     *
     * @package Framework\Views\Pages
     */

    use Flight;
    use Framework\Application\Container;
    use Framework\Application\Settings;
    use Framework\Syscrack\User;
    use Framework\Views\BaseClasses\Page as BaseClass;
    use Framework\Views\Structures\Page as Structure;

    class Account extends BaseClass implements Structure
    {

        protected $user;

        /**
         * Account constructor.
         */

        public function __construct()
        {

            parent::__construct( false, true, true, true );

            if( isset( $this->user ) == false )
            {

                $this->user = new User();
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
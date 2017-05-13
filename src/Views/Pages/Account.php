<?php
    namespace Framework\Views\Pages;

    /**
     * Lewis Lancaster 2017
     *
     * Class Account
     *
     * @package Framework\Views\Pages
     */

    use Framework\Application\Container;
    use Framework\Application\Session;
    use Framework\Syscrack\User;
    use Framework\Views\BaseClasses\Page as BaseClass;
    use Framework\Views\Structures\Page as Structure;

    class Account extends BaseClass implements Structure
    {

        /**
         * @var User
         */

        protected $user;

        /**
         * @var Session
         */

        protected $session;

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

            if( isset( $this->session ) == false )
            {

                if( Container::hasObject('session') == false )
                {

                    Container::setObject('session', new Session() );
                }

                $this->session = Container::getObject('session');
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

            $this->session->cleanupSession( $this->session->getSessionUser() );

            $this->session->destroySession( true );

            $this->redirectSuccess('login');
        }
    }
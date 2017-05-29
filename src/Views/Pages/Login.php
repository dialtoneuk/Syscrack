<?php
    namespace Framework\Views\Pages;

    /**
     * Lewis Lancaster 2017
     *
     * Class Login
     *
     * @package Framework\Views\Pages
     */

    use Flight;
    use Framework\Application\Container;
    use Framework\Application\Session;
    use Framework\Application\Settings;
    use Framework\Application\Utilities\PostHelper;
    use Framework\Exceptions\ViewException;
    use Framework\Syscrack\Login\Account;
    use Framework\Syscrack\User;
    use Framework\Views\BaseClasses\Page as BaseClass;
    use Framework\Views\Structures\Page as Structure;

    class Login extends BaseClass implements Structure
    {

        /**
         * @var Session
         */

        protected $session;

        /**
         * @var User
         */

        protected $user;

        /**
         * @var Account
         */

        protected $login;

        /**
         * Login constructor.
         */

        public function __construct()
        {

            parent::__construct( true, true, false, true );

            if( isset( $this->session ) == false )
            {

                $this->session = Container::getObject('session');
            }

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
                    'GET /login/', 'page'
                ],
                [
                    'POST /login/', 'process'
                ],
                [
                    'POST /login/facebook/', 'facebook'
                ]
            );
        }

        /**
         * Default page
         */

        public function page()
        {

            Flight::render('syscrack/page.login');
        }

        /**
         * Processes a login request
         */

        public function process()
        {

            if( isset( $this->login ) == false )
            {

                $this->login = new Account();
            }

            if (PostHelper::hasPostData() == false)
            {

                $this->redirectError('Blank Form');
            }

            if (PostHelper::checkForRequirements(['username', 'password']) == false)
            {

                $this->redirectError('Missing Information');
            }

            $username = PostHelper::getPostData('username');

            $password = PostHelper::getPostData('password');

            try
            {

                if ($this->login->loginAccount($username, $password) == false)
                {

                    $this->redirectError('Failed to login');
                }
            } catch (\Exception $error)
            {

                $this->redirectError($error->getMessage());
            }

            $userid = $this->login->getUserID( $username );

            if( $this->user->userExists( $userid ) == false )
            {

                $this->redirectError('Your userid is invalid, please tell a developer');
            }

            if( Settings::getSetting('login_cleanup_old_sessions') == true )
            {

                $this->session->cleanupSession( $userid );
            }

            $this->session->insertSession( $userid );

            $this->addConnectedComputer( $userid );

            $this->redirect('game', false );
        }

        public function facebook()
        {


        }

        /**
         * Adds the current connected computer to the session
         *
         * @param $userid
         */

        private function addConnectedComputer($userid)
        {

            if ($this->computers->userHasComputers($userid) == false)
            {

                throw new ViewException('User has no computer');
            }

            $this->computers->setCurrentUserComputer($this->computers->getUserMainComputer($userid)->computerid);
        }
    }
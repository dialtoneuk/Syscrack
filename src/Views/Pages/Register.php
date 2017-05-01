<?php
    namespace Framework\Views\Pages;

    /**
     * Lewis Lancaster 2016
     *
     * Class Register
     *
     * @package Framework\Views\Pages
     */

    use Flight;
    use Framework\Application\Container;
    use Framework\Application\Session;
    use Framework\Application\Settings;
    use Framework\Application\Utilities\PostHelper;
    use Framework\Exceptions\SyscrackException;
    use Framework\Syscrack\BetaKeys;
    use Framework\Syscrack\Register as Account;
    use Framework\Views\Structures\Page;

    class Register implements Page
    {

        /**
         * Login constructor.
         */

        public function __construct()
        {

            if (session_status() !== PHP_SESSION_ACTIVE)
            {

                session_start();
            }

            Container::setObject('session', new Session());

            if (Container::getObject('session')->isLoggedIn())
            {

                Flight::redirect( Settings::getSetting('controller_index_root') . Settings::getSetting('controller_index_page') );
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
                    'GET /register/', 'page'
                ],
                [
                    'POST /register/', 'process'
                ]
            );
        }

        /**
         * Default page
         */

        public function page()
        {

            Flight::render('syscrack/page.register');
        }

        /**
         * Processes the register request
         */

        public function process()
        {

            if (PostHelper::hasPostData() == false)
            {

                $this->redirectError('Blank Form');
            }

            if (Settings::getSetting('user_allow_registrations') == false)
            {

                $this->redirectError('Registration is currently disabled, sorry...');
            }

            if (PostHelper::checkForRequirements(['username', 'password', 'email']) == false)
            {

                $this->redirectError('Missing Information');
            }

            $username = PostHelper::getPostData('username');

            $password = PostHelper::getPostData('password');

            $email = PostHelper::getPostData('email');

            if (empty($username) || empty($password) || empty($email))
            {

                $this->redirectError('Failed to register');
            }

            $register = new Account();

            if (strlen($password) < Settings::getSetting('registration_password_length'))
            {

                $this->redirectError('Your password is too small, it needs to be longer than ' . Settings::getSetting('registration_password_length') . ' characters');
            }

            if( Settings::getSetting('user_require_betakey') == true )
            {

                $betakeys = new BetaKeys();

                if( PostHelper::checkForRequirements(['betakey'] ) == false )
                {

                    $this->redirectError('A beta-key is required to signup');
                }

                $key = PostHelper::getPostData('betakey');

                if( $betakeys->hasBetaKey( $key ) == false )
                {

                    $this->redirectError('Sorry, that key is invalid or has already been used');
                }

                try
                {

                    $result = $register->register($username, $password, $email);
                }
                catch( SyscrackException $error )
                {

                    $this->redirectError( $error->getMessage() );
                }

                $betakeys->removeBetaKey( $key );

                Flight::redirect('/verify/?token=' . $result);
            }
            else
            {

                try
                {

                    $result = $register->register($username, $password, $email);
                }
                catch( SyscrackException $error )
                {

                    $this->redirectError( $error->getMessage() );
                }

                Flight::redirect('/verify/?token=' . $result);
            }
        }

        /**
         * Display an error
         *
         * @param $error
         */

        private function redirectError($error)
        {

            Flight::redirect('/register/?error=' . $error);
        }
    }
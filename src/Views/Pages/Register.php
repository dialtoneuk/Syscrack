<?php
    namespace Framework\Views\Pages;

    /**
     * Lewis Lancaster 2017
     *
     * Class Register
     *
     * @package Framework\Views\Pages
     */

    use Framework\Application\Render;
    use Framework\Application\Container;
    use Framework\Application\Mailer;
    use Framework\Application\Settings;
    use Framework\Application\Utilities\PostHelper;
    use Framework\Exceptions\SyscrackException;
    use Framework\Syscrack\BetaKeys;
    use Framework\Syscrack\Register as Account;
    use Framework\Views\BaseClasses\Page as BaseClass;
    use Framework\Views\Structures\Page as Structure;

    class Register extends BaseClass implements Structure
    {

        /**
         * @var Mailer
         */

        protected static $mailer;

        /**
         * @var BetaKeys
         */

        protected static $betakeys;

        /**
         * Register constructor.
         */

        public function __construct()
        {

            if( isset( self::$mailer ) == false )
                self::$mailer = new Mailer();

            if( Settings::getSetting('user_require_betakey') )
                self::$betakeys = new BetaKeys();

            parent::__construct( false, true, false, true );
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

            if (Container::getObject('session')->isLoggedIn())
                Render::redirect( Settings::getSetting('controller_index_root') . Settings::getSetting('controller_index_page') );

            Render::view('syscrack/page.register');
        }

        /**
         * Processes the register request
         */

        public function process()
        {

            if (Container::getObject('session')->isLoggedIn())
                Render::redirect( Settings::getSetting('controller_index_root') . Settings::getSetting('controller_index_page') );

            if (PostHelper::hasPostData() == false)
                $this->redirectError('Missing Information');
            elseif (Settings::getSetting('user_allow_registrations') == false)
                $this->redirectError('Registration is currently disabled, sorry...');
            elseif (PostHelper::checkForRequirements(['username', 'password', 'email']) == false)
                $this->redirectError('Missing Information');

            $username = PostHelper::getPostData('username');
            $password = PostHelper::getPostData('password');
            $email = PostHelper::getPostData('email');

            if (empty($username) || empty($password) || empty($email))
                $this->redirectError('Missing Information');
            elseif (strlen($password) < Settings::getSetting('registration_password_length'))
                $this->redirectError('Your password is too small, it needs to be longer than ' . Settings::getSetting('registration_password_length') . ' characters');
            else
            {

                $register = new Account();

                if ( Settings::getSetting('user_require_betakey') && PostHelper::checkForRequirements(['betakey']) == false
                    && self::$betakeys->hasBetaKey(PostHelper::getPostData('betakey')) == false )
                    $this->redirectError('Invalid key, please check for any white spaces or errors in the key and try again');
                else
                {

                    if( Settings::getSetting('user_require_betakey') )
                        self::$betakeys->removeBetaKey( PostHelper::getPostData('betakey') );

                    $result = @$register->register($username, $password, $email);

                    if( $result === false )
                        $this->redirectError("An error occured while trying to create your account. Its been logged and we are on it. Please try again later.");
                    else
                    {

                        if( Settings::getSetting('registration_verification') )
                        {
                            $this->sendEmail( $email, array('token' => $result ) );
                            $this->redirect('verify');
                        }
                        else
                            $this->redirect('verify?token=' . $result );
                    }
                }
            }
        }

        /**
         * @param $email
         * @param array $variables
         * @return bool
         */

        private function sendEmail( $email, array $variables )
        {

            $body = self::$mailer->parse( self::$mailer->getTemplate('email.verify.php'), $variables );

            if( empty( $body ) )
            {

                throw new SyscrackException();
            }

            $result = self::$mailer->send( $body, 'Verify your email', $email );

            if( $result == false )
            {

                return false;
            }

            return true;
        }
    }
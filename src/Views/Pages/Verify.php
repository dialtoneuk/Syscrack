<?php
    namespace Framework\Views\Pages;

    /**
     * Lewis Lancaster 2016
     *
     * Class Verify
     *
     * @package Framework\Views\Pages
     */

    use Flight;
    use Framework\Application\Settings;
    use Framework\Application\Utilities\PostHelper;
    use Framework\Syscrack\Game\Utilities\Startup;
    use Framework\Syscrack\Verification;
    use Framework\Views\BaseClasses\Page as BaseClass;
    use Framework\Views\Structures\Page as Structure;

    class Verify extends BaseClass implements Structure
    {

        /**
         * @var Verification
         */

        protected $verification;

        /**
         * Verify constructor.
         */

        public function __construct()
        {

            parent::__construct( false, true, false, true );

            if( isset( $this->verification ) == false )
            {

                $this->verification = new Verification();
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
                    'GET /verify/', 'page'
                ],
                [
                    'POST /verify/', 'process'
                ]
            );
        }

        /**
         * Default page
         */

        public function page()
        {

            if (isset($_GET['token']))
            {

                if ($this->verification->getTokenUser($_GET['token']) == null)
                {

                    $this->redirectError('Sorry, this token is invalid...', 'login' );
                }

                $userid = $this->verification->getTokenUser($_GET['token']);

                if ($userid == null)
                {

                    $this->redirectError('Sorry, this token isnt tied to a user, try again?', 'login' );
                }

                if ($this->verification->verifyUser($_GET['token']) == false)
                {

                    $this->redirectError('Sorry, failed to verify, try again?', 'login' );
                }

                try
                {

                    if (Settings::getSetting('syscrack_startup_on_verification'))
                    {

                        $startup = new Startup($userid);
                    }
                }
                catch( \Exception $error )
                {

                    $this->redirectError('Sorry, we encounted an error, please tell a developer', 'login');
                }

                $this->redirectSuccess('login');
            }

            Flight::notFound();
        }

        /**
         * Processes the verification request
         */

        public function process()
        {

            if (PostHelper::hasPostData() == false)
            {

                $this->redirectError('Please enter a token');
            }

            if (PostHelper::checkForRequirements(['token']) == false)
            {

                $this->redirectError('Please enter a token');
            }

            $token = PostHelper::getPostData('token');

            $userid = $this->verification->getTokenUser($token);

            if ($userid == null)
            {

                $this->redirectError('Sorry, this token isnt tied to a user, try again?', 'login' );
            }

            if ($this->verification->verifyUser($_GET['token']) == false)
            {

                $this->redirectError('Sorry, failed to verify, try again?', 'Wlogin' );
            }

            try
            {

                if (Settings::getSetting('syscrack_startup_on_verification'))
                {

                    $startup = new Startup($userid);
                }
            }
            catch( \Exception $error )
            {

                $this->redirectError('Sorry, we encounted an error, please a developer', '/login');
            }

            $this->redirectSuccess('login');
        }
    }
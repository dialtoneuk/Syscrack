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
    use Framework\Application\Container;
    use Framework\Application\Settings;
    use Framework\Application\Utilities\PostHelper;
    use Framework\Syscrack\Game\Utilities\Startup;
    use Framework\Syscrack\Verification;
    use Framework\Views\Structures\Page;

    class Verify implements Page
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

            if (Container::getObject('application')->getController()->page == Settings::getSetting('developer_page'))
            {

                return;
            }


            $this->verification = new Verification();
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
                    'GET /verify/', 'page'
                ],
                [
                    'POSt /verify/', 'process'
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

                    Flight::redirect('/verify?error=Sorry, that token is invalid');

                    exit;
                }

                $userid = $this->verification->getTokenUser($_GET['token']);

                if ($userid == null)
                {

                    Flight::redirect('/verify?error=Sorry, that token is invalid');

                    exit;
                }

                if ($this->verification->verifyUser($_GET['token']) == false)
                {

                    Flight::redirect('/verify?error=Sorry, that token is invalid');

                    exit;
                }

                $startup = new Startup($userid);

                Flight::redirect('/login?success');

                exit;
            }

            Flight::render('page.verify');
        }

        /**
         * Processes the verification request
         */

        public function process()
        {

            if (PostHelper::hasPostData() == false)
            {

                Flight::redirect('/verify?error=Please enter something.. atleast...');

                exit;
            }

            if (PostHelper::checkPostData(['token']) == false)
            {

                Flight::redirect('/verify?error=Please enter something.. atleast...');

                exit;
            }

            $token = PostHelper::getPostData('token');

            $userid = $this->verification->getTokenUser($token);

            if ($this->verification->verifyUser($token) == false)
            {

                Flight::redirect('/verify?error=Sorry.. that token is invalid...');
            }

            if (Settings::getSetting('syscrack_startup_on_verification'))
            {

                $startup = new Startup($userid);
            }

            Flight::redirect('/login/?success');
        }
    }
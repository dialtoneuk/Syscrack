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
    use Framework\Application\Settings;
    use Framework\Application\Utilities\PostHelper;
    use Framework\Exceptions\ViewException;
    use Framework\Syscrack\Game\Computer;
    use Framework\Syscrack\Login\Account;
    use Framework\Views\BaseClasses\Page as BaseClass;
    use Framework\Views\Structures\Page as Structure;

    class Login extends BaseClass implements Structure
    {

        /**
         * Login constructor.
         */

        public function __construct()
        {

            parent::__construct( false, true, false, true );

            if (Container::getObject('session')->isLoggedIn())
            {

                Flight::redirect( Settings::getSetting('controller_index_root') . Settings::getSetting('controller_index_page') );
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

            if (empty($username) || empty($password))
            {

                $this->redirectError('Failed to login');
            }

            $login = new Account();

            try
            {

                if ($login->login($username, $password) == false)
                {

                    $this->redirectError('Failed to login');
                }
            } catch (\Exception $error)
            {

                $this->redirectError($error->getMessage());
            }

            Container::getObject('session')->cleanupSession($login->getUserID($username));

            Container::getObject('session')->insertSession($login->getUserID($username));

            $this->addConnectedComputer($login->getUserID($username));

            Flight::redirect('/game/');
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

            $computer = new Computer();

            if ($computer->userHasComputers($userid) == false)
            {

                throw new ViewException('User has no computer');
            }

            $computer->setCurrentUserComputer($computer->getUserMainComputer($userid)->computerid);
        }
    }
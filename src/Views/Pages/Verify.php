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
    use Framework\Exceptions\SyscrackException;
    use Framework\Syscrack\Game\Structures\Computer;
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

            parent::__construct( true, true, false, true );

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

                $_GET['token'] = htmlspecialchars( $_GET['token'], ENT_QUOTES, 'UTF-8' );

                if ($this->verification->getTokenUser($_GET['token']) == null)
                {

                    $this->redirectError('Sorry, this token is invalid...' );
                }

                $userid = $this->verification->getTokenUser($_GET['token']);

                if ($userid == null)
                {

                    $this->redirectError('Sorry, this token isnt tied to a user, try again?');
                }

                if ($this->verification->verifyUser( $_GET['token'] ) == false)
                {

                    $this->redirectError('Sorry, failed to verify, try again?');
                }

                try
                {

                    if( Settings::getSetting('syscrack_startup_on_verification') == true )
                    {

                        $computerid = $this->computers->createComputer( $userid, Settings::getSetting('syscrack_startup_default_computer'), $this->internet->getIP() );

                        if( empty( $computerid ) )
                        {

                            throw new SyscrackException();
                        }

                        $class = $this->computers->getComputerClass( Settings::getSetting('syscrack_startup_default_computer') );

                        if( $class instanceof Computer == false )
                        {

                            throw new SyscrackException();
                        }

                        $class->onStartup( $computerid, $userid, [], Settings::getSetting('syscrack_default_hardware') );
                    }
                }
                catch( \Exception $error )
                {

                    $this->redirectError('Sorry, your account has been verified but we encountered an error: ' . $error->getMessage() );
                }

                $this->redirectSuccess('login');
            }
            else
            {

                Flight::render('syscrack/page.verify');
            }
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

            $token = PostHelper::getPostData( 'token', true );

            $userid = $this->verification->getTokenUser($token);

            if ($userid == null)
            {

                $this->redirectError('Sorry, this token is not tied to a user, try again?');
            }

            if ($this->verification->verifyUser( $token ) == false)
            {

                $this->redirectError('Sorry, failed to verify, try again?');
            }

            try
            {

                if( Settings::getSetting('syscrack_startup_on_verification') == true )
                {

                    $computerid = $this->computers->createComputer( $userid, Settings::getSetting('syscrack_startup_default_computer'), $this->internet->getIP() );

                    if( empty( $computerid ) )
                    {

                        throw new SyscrackException();
                    }

                    $class = $this->computers->getComputerClass( Settings::getSetting('syscrack_startup_default_computer') );

                    if( $class instanceof Computer == false )
                    {

                        throw new SyscrackException();
                    }

                    $class->onStartup( $computerid, $userid, [], Settings::getSetting('syscrack_default_hardware') );
                }
            }
            catch( \Exception $error )
            {

                $this->redirectError('Sorry, your account has been verified but we encountered an error: ' . $error->getMessage() );
            }

            $this->redirectSuccess('login');
        }
    }
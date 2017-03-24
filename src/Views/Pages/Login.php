<?php
namespace Framework\Views\Pages;

/**
 * Lewis Lancaster 2016
 *
 * Class Login
 *
 * @package Framework\Views\Pages
 */

use Framework\Application\Utilities\PostHelper;
use Framework\Views\Structures\Page;
use Framework\Application\Container;
use Framework\Application\Session;
use Framework\Syscrack\Login\Account;
use Flight;

class Login implements Page
{

    /**
     * Login constructor.
     */

    public function __construct()
    {

        session_start();

        Container::setObject( 'session',  new Session() );
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

        if( PostHelper::hasPostData() == false )
        {

            $this->redirectError('Blank Form');
        }

        if( PostHelper::checkForRequirements(['username','password']) == false )
        {

            $this->redirectError('Missing Information');
        }

        $username = PostHelper::getPostData('username'); $password = PostHelper::getPostData('password');

        if( empty( $username ) || empty( $password ) )
        {

            $this->redirectError('Failed to login');
        }

        $login = new Account();

        if( $login->login( $username, $password ) == false )
        {

            $this->redirectError('Information is incorrect');
        }

        Container::getObject('session')->insertSession( $login->getUserID( $username ) );

        Flight::redirect('/game/');
    }

    public function facebook()
    {


    }

    /**
     * Display an error
     *
     * @param $error
     */

    private function redirectError( $error )
    {

        Flight::redirect('/login/?error=' . $error );
    }
}
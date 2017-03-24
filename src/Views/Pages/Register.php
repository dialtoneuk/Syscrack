<?php
namespace Framework\Views\Pages;

/**
 * Lewis Lancaster 2016
 *
 * Class Register
 *
 * @package Framework\Views\Pages
 */

use Framework\Application\Settings;
use Framework\Application\Utilities\PostHelper;
use Framework\Views\Structures\Page;
use Framework\Application\Container;
use Framework\Application\Session;
use Framework\Syscrack\Register as Account;
use Flight;

class Register implements Page
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

        if( Settings::getSetting('user_allow_registrations') )
        {

            $this->redirectError('Registration is currently disabled, sorry...');
        }

        if( PostHelper::hasPostData() == false )
        {

            $this->redirectError('Blank Form');
        }

        if( PostHelper::checkForRequirements(['username','password','email']) == false )
        {

            $this->redirectError('Missing Information');
        }

        $username = PostHelper::getPostData('username'); $password = PostHelper::getPostData('password'); $email = PostHelper::getPostData('email');

        if( empty( $username ) || empty( $password ) || empty( $email ) )
        {

            $this->redirectError('Failed to register');
        }

        $register = new Account();

        if( strlen( $password ) < Settings::getSetting('registration_password_length') )
        {

            $this->redirectError('Your password is too small, it needs to be longer than ' . Settings::getSetting('registration_password_length') . ' characters');
        }

        $result = @$register->register( $username, $password, $email );

        if( $result == false )
        {

            $this->redirectError('Either your Username is taken, or your email is already used');
        }

        Flight::redirect('/verify/?token=' . $result );
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
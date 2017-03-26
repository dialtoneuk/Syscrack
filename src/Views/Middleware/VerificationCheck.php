<?php
namespace Framework\Views\Middleware;

/**
 * Lewis Lancaster 2016
 *
 * Class VerificationCheck
 *
 * @package Framework\Views\Middleware
 */

use Framework\Application\Container;
use Framework\Application\Utilities\Log;
use Framework\Views\Structures\Middleware;
use Framework\Syscrack\Verification;
use Framework\Application\Session;
use Flight;

class VerificationCheck implements Middleware
{

    /**
     * @var Session
     */

    protected $session;

    /**
     * @var Verification
     */

    protected $verification;


    /**
     * Checks if the user has verified their email
     *
     * @return bool
     */

    public function onRequest()
    {

        Log::log('Middleware Requested');

        if( Container::hasObject('session') == false )
        {

            return true;
        }

        $this->session = Container::getObject('session');

        $this->verification = new Verification();

        if( $this->session->isLoggedIn() == false )
        {

            return true;
        }

        $userid = $this->session->getSessionUser();

        if( $this->verification->isVerified( $userid ) == false )
        {

            return false;
        }

        return true;
    }

    /**
     * Called when the user passes the middleware
     */

    public function onSuccess()
    {

        Log::log('Succeeded Middleware Check');
    }

    /**
     * If the user hasn't verified their email, they'll be sent here.
     */

    public function onFailure()
    {

        Flight::redirect('/verifyemail');
    }
}
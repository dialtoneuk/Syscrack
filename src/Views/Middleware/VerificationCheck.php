<?php
namespace Framework\Views\Middleware;

/**
 * Lewis Lancaster 2016
 *
 * Class VerificationCheck
 *
 * @package Framework\Views\Middleware
 */

use Framework\Views\Structures\Middleware;
use Framework\Syscrack\Verification;
use Framework\Session\Capsule as Session;
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
     * VerificationCheck constructor.
     */

    public function __construct()
    {

        $this->session = new Session();

        $this->verification = new Verification();
    }

    /**
     * Checks if the user has verified their email
     *
     * @return bool
     */

    public function onRequest()
    {

        if( $this->session->isValid() == false )
        {

            //We return true because the session is invalid and there for this is a 'guest user'. So we simply let the
            //user past.
            return true;
        }

        $userid = $this->session->getSessionOwner();

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

        //Could do something here
    }

    /**
     * If the user hasn't verified their email, they'll be sent here.
     */

    public function onFailure()
    {

        Flight::redirect('/middleware/verifyemail');
    }
}
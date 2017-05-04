<?php
namespace Framework\Application;

/**
 * Lewis Lancaster 2017
 *
 * Class Session
 *
 * @package Framework\Session
 */

use Framework\Application\Utilities\IPAddress;
use Framework\Database\Tables\Sessions as Database;

class Session
{

    /**
     * @var Database
     */

    protected $database;

    /**
     * Capsule constructor.
     */

    public function __construct()
    {

        $this->database = new Database();
    }

    /**
     * Updates the time since the last action
     */

    public function updateLastAction()
    {

        $this->database->updateSession( session_id(), array('lastaction' => microtime( true ) ) );
    }

    /**
     * Clears the last session error
     */

    public function clearError()
    {

        if( isset( $_SESSION['error'] ) == false )
        {

            return;
        }

        $_SESSION['error_page'] = null;

        $_SESSION['error'] = null;
    }

    /**
     * Gets the time of which the user hsa done the last action
     *
     * @return mixed
     */

    public function getLastAction()
    {

        return $this->database->getSession( session_id() )->lastaction;
    }

    /**
     * Gets the database session
     *
     * @return mixed
     */

    public function getDatabaseSession()
    {

        return $this->database->getSession( session_id() );
    }

    /**
     * Gets the session user
     *
     * @return string
     */

    public function getSessionUser()
    {

        return $this->getDatabaseSession()->userid;
    }

    /**
     * Gets the session address
     *
     * @return mixed
     */

    public function getSessionAddress()
    {

        return $this->getDatabaseSession()->ipaddress;
    }

    /**
     * Gets the sessions user agent
     *
     * @return mixed
     */

    public function getSessionUserAgent()
    {

        return $this->getDatabaseSession()->useragent;
    }

    /**
     * Gets the time since sessions last action
     *
     * @return mixed
     */

    public function getSessionLastAction()
    {

        return $this->getDatabaseSession()->lastaction;
    }

    /**
     * Cleans up a users sessions
     */

    public function cleanupSession( $userid )
    {

        $this->database->trashUserSessions( $userid );
    }

    /**
     * Inserts a new session into the database
     *
     * @param $userid
     */

    public function insertSession( $userid )
    {

        $array = array(
            'sessionid'     => session_id(),
            'userid'        => $userid,
            'useragent'     => $_SERVER['HTTP_USER_AGENT'],
            'ipaddress'     => IPAddress::getAddress(),
            'lastaction'    => microtime( true )
        );

        $this->database->insertSession( $array );
    }

    /**
     * Gets the sessions of which have been active in the last hour ( according to the settings )
     *
     * @return mixed|null
     */

    public function getActiveSessions()
    {

        return $this->database->getSessionsByLastAction( time() - Settings::getSetting('online_timeframe') );
    }

    /**
     * Returns true if the user is logged in
     *
     * @return bool
     */

    public function isLoggedIn()
    {

        if( $this->database->getSession( session_id() ) == null )
        {

            return false;
        }

        return true;
    }

    /**
     * Returns true if the session is active
     *
     * @return bool
     */

    public function sessionActive()
    {

        if( session_status() != PHP_SESSION_ACTIVE )
        {

            return false;
        }

        return true;
    }
}
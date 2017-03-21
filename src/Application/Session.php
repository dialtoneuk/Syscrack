<?php
namespace Framework\Application;

/**
 * Lewis Lancaster 2017
 *
 * Class Session
 *
 * @package Framework\Session
 */

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

        if( $this->isLoggedIn() )
        {

            if( Settings::getSetting('session_update_lastaction') )
                $this->updateLastAction();
        }
    }

    /**
     * Updates the time since the last action
     */

    public function updateLastAction()
    {

        $this->database->updateSession( session_id(), array('lastaction' => microtime( true ) ) );
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
     * @return mixed
     */

    public function getSessionUser()
    {

        return $this->getDatabaseSession()['userid'];
    }

    /**
     * Gets the session address
     *
     * @return mixed
     */

    public function getSessionAddress()
    {

        return $this->getDatabaseSession()['ip'];
    }

    /**
     * Gets the sessions user agent
     *
     * @return mixed
     */

    public function getSessionUserAgent()
    {

        return $this->getDatabaseSession()['useragent'];
    }

    /**
     * Gets the time since sessions last action
     *
     * @return mixed
     */

    public function getSessionLastAction()
    {

        return $this->getDatabaseSession()['lastaction'];
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
            'ip'            => gethostbyname( gethostname() ),
            'lastaction'    => microtime( true )
        );

        $this->database->insertSession( $array );
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
<?php
namespace Framework\Syscrack\Game\Utility;

/**
 * Lewis Lancaster 2017
 *
 * Class PageHelper
 *
 * @package Framework\Syscrack\Game\Utility
 */

use Framework\Application\Container;
use Framework\Application\Settings;
use Framework\Exceptions\SyscrackException;
use Framework\Syscrack\Game\Finance;
use Framework\Syscrack\User;

class PageHelper
{

    /**
     * @var \Framework\Application|\Framework\Application\Session
     */

    protected $session;

    /**
     * PageHelper constructor.
     */

    public function __construct()
    {

        if( Container::hasObject('session') == false )
        {

            return;
        }

        $this->session = Container::getObject('session');
    }

    /**
     * Gets the users id
     *
     * @return string
     */

    public function getUserID()
    {

        return $this->session->getSessionUser();
    }

    /**
     * Gets the users address
     *
     * @return mixed
     */

    public function getUserAddress()
    {

        return $this->session->getSessionAddress();
    }

    /**
     * Gets the total cash of a user
     *
     * @return int
     */

    public function getCash()
    {

        $finance = new Finance();

        if( $finance->hasAccount( $this->session->getSessionUser() ) == false )
        {

            throw new SyscrackException();
        }

        return Settings::getSetting('syscrack_currency') . $finance->getTotalUserCash( $this->session->getSessionUser() );
    }

    /**
     * Gets the username of the user
     *
     * @return string
     */

    public function getUsername()
    {

        $user = new User();

        if( $user->userExists( $this->session->getSessionUser() ) == false )
        {

            throw new SyscrackException();
        }

        return $user->getUsername( $this->session->getSessionUser() );
    }
}
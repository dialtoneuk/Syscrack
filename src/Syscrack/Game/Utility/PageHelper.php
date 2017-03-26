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
use Framework\Syscrack\Game\Computer;
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
     * Gets computers software
     *
     * @return array
     */

    public function getComputerSoftware()
    {

        $computer = new Computer();

        if( $computer->userHasComputers( $this->session->getSessionUser() ) == false )
        {

            throw new SyscrackException();
        }

        return $computer->getComputerSoftware( $computer->getUserMainComputer( $this->session->getSessionUser() )->computerid );
    }

    /**
     * Gets the computers hardware
     *
     * @return array
     */

    public function getComputerHardware()
    {

        $computer = new Computer();

        if( $computer->userHasComputers( $this->session->getSessionUser() ) == false )
        {

            throw new SyscrackException();
        }

        return $computer->getComputerHardware( $computer->getUserMainComputer( $this->session->getSessionUser() )->computerid );
    }

    /**
     * Gets the users installed hasher
     *
     * @return null
     */

    public function getInstalledHasher()
    {

        $computer = new Computer();

        if( $computer->userHasComputers( $this->session->getSessionUser() ) == false )
        {

            throw new SyscrackException();
        }

        $softwares = $computer->getComputerSoftware( $computer->getUserMainComputer( $this->session->getSessionUser() )->computerid );

        foreach( $softwares as $software )
        {

            if( $software['type'] == Settings::getSetting('syscrack_hasher_type') )
            {

                return $software;
            }
        }

        return null;
    }

    /**
     * Gets the users installed firewall
     *
     * @return null
     */

    public function getInstalledFirewall()
    {

        $computer = new Computer();

        if( $computer->userHasComputers( $this->session->getSessionUser() ) == false )
        {

            throw new SyscrackException();
        }

        $softwares = $computer->getComputerSoftware( $computer->getUserMainComputer( $this->session->getSessionUser() )->computerid );

        foreach( $softwares as $software )
        {

            if( $software['type'] == Settings::getSetting('syscrack_firewall_type') )
            {

                return $software;
            }
        }

        return null;
    }

    /**
     * Gets the users installed cracker
     *
     * @return null
     */

    public function getInstalledCracker()
    {

        $computer = new Computer();

        if( $computer->userHasComputers( $this->session->getSessionUser() ) == false )
        {

            throw new SyscrackException();
        }

        $softwares = $computer->getComputerSoftware( $computer->getUserMainComputer( $this->session->getSessionUser() )->computerid );

        foreach( $softwares as $software )
        {

            if( $software['type'] == Settings::getSetting('syscrack_cracker_type') )
            {

                return $software;
            }
        }

        return null;
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
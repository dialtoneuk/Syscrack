<?php
namespace Framework\Syscrack\Game;

/**
 * Lewis Lancaster 2017
 *
 * Class Internet
 *
 * @package Framework\Syscrack\Game
 */

use Framework\Application\Container;
use Framework\Application\Utilities\Hashes;
use Framework\Database\Tables\Computers;
use Framework\Exceptions\SyscrackException;

class Internet
{

    /**
     * @var Computers
     */

    protected $computers;

    /**
     * Internet constructor.
     */

    public function __construct()
    {

        $this->computers = new Computers();
    }

    /**
     * Returns true if the address exists
     *
     * @param $ipaddress
     *
     * @return bool
     */

    public function ipExists( $ipaddress )
    {

        if( $this->computers->getComputerByIPAddress( $ipaddress ) == null )
        {

            return false;
        }

        return true;
    }

    /**
     * Gets the computers by their IP address
     *
     * @param $ipaddress
     *
     * @return mixed|null
     */

    public function getComputer( $ipaddress )
    {

        return $this->computers->getComputerByIPAddress( $ipaddress );
    }
    /**
     * Gets the computers password
     *
     * @param $ipaddress
     *
     * @return mixed
     */

    public function getComputerPassword( $ipaddress )
    {

        return $this->computers->getComputerByIPAddress( $ipaddress )->password;
    }

    /**
     * Gets the computers address
     *
     * @param $computerid
     *
     * @return mixed
     */

    public function getComputerAddress( $computerid )
    {

        return $this->computers->getComputer( $computerid )->ipaddress;
    }

    /**
     * Changes the computers address
     *
     * @param $computerid
     *
     * @return string
     */

    public function changeAddress( $computerid )
    {

        $address = $this->getIP();

        if( $this->ipExists( $address ) )
        {

            throw new SyscrackException();
        }

        $array = array(
            'ipaddress' => $address
        );

        $this->computers->updateComputer( $computerid, $array );

        return $address;
    }

    /**
     * Returns true if the user has a current connection
     *
     * @return bool
     */

    public function hasCurrentConnection()
    {

        if( isset( $_SESSION['connected_ipaddress'] ) == false )
        {

            return false;
        }

        return true;
    }

    /**
     * Gets the current connected address of the computer
     *
     * @return null
     */

    public function getCurrentConnectedAddress()
    {

        if( Container::hasObject('session') == false )
        {

            return null;
        }

        $session = Container::getObject('session');

        if( $session->isLoggedIn() == false )
        {

            return null;
        }

        return $_SESSION['connected_ipaddress'];
    }

    /**
     * Sets the current connected address of the user
     *
     * @param $ipaddress
     *
     * @return null
     */

    public function setCurrentConnectedAddress( $ipaddress )
    {

        if( Container::hasObject('session') == false )
        {

            return null;
        }

        $session = Container::getObject('session');

        if( $session->isLoggedIn() == false )
        {

            return null;
        }

        $_SESSION['connected_ipaddress'] = $ipaddress;
    }

    /**
     * Changes the computers password
     *
     * @param $computerid
     *
     * @return string
     */

    public function changePassword( $computerid )
    {

        $password = $this->getPassword();

        $array = array(
            'password' => $password
        );

        $this->computers->updateComputer( $computerid, $array );

        return $password;
    }

    /**
     * Returns a new random IPs
     *
     * @return string
     */

    private function getIP()
    {

        return rand(0,255) . '.' . rand(0,255)  . '.' .  rand(0,255)  . '.' .  rand(0,255);
    }

    /**
     * Returns a new random computer password
     *
     * @return string
     */

    private function getPassword()
    {

        return Hashes::randomBytes( rand(6,18 ) );
    }
}
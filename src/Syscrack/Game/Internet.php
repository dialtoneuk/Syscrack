<?php
namespace Framework\Syscrack\Game;

/**
 * Lewis Lancaster 2017
 *
 * Class Internet
 *
 * @package Framework\Syscrack\Game
 */

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
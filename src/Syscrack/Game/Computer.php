<?php
namespace Framework\Syscrack\Game;

/**
 * Lewis Lancaster 2017
 *
 * Class Computer
 *
 * @package Framework\Syscrack\Game
 *
 * //TODO: On rewrite, is the local list of softwares on the computer needed?
 */

use Framework\Application\Settings;
use Framework\Database\Tables\Computers;
use Framework\Exceptions\SyscrackException;

class Computer
{

    /**
     * @var Computers
     */

    protected $database;

    /**
     * Computer constructor.
     */

    public function __construct()
    {

        $this->database = new Computers();
    }

    /**
     * Gets all the computers
     *
     * @return \Illuminate\Support\Collection|mixed
     */

    public function getAllComputers( $pick=32 )
    {

        return $this->database->getAllComputers( $pick );
    }

    /**
     * Returns true if the user has computers
     *
     * @param $userid
     *
     * @return bool
     */

    public function userHasComputers( $userid )
    {

        if( $this->database->getComputersByUser( $userid ) == null )
        {

            return false;
        }

        return true;
    }

    /**
     * Changes a computers IP address
     *
     * @param $computerid
     *
     * @param $address
     */

    public function changeIPAddress( $computerid, $address )
    {

        $array = array(
            'ipaddress' => $address
        );

        $this->database->updateComputer( $computerid, $array );
    }

    /**
     * Formats a computer to the default software
     *
     * @param $computerid
     */

    public function format( $computerid )
    {

        $array = array(
            'softwares' => json_encode( Settings::getSetting('syscrack_default_software') )
        );

        $this->database->updateComputer( $computerid, $array );
    }

    /**
     * Resets the hardware of a computer to the default hardware
     *
     * @param $computerid
     */

    public function resetHardware( $computerid )
    {

        $array = array(
            'hardware' => json_encode( Settings::getSetting('syscrack_default_hardware') )
        );

        $this->database->updateComputer( $computerid, $array );
    }

    /**
     * Gets the computer at the id
     *
     * @param $computerid
     *
     * @return mixed||\stdClass
     */

    public function getComputer( $computerid )
    {

        return $this->database->getComputer( $computerid );
    }

    /**
     * Gets the current list of software in the system
     *
     * @param $computerid
     *
     * @return array
     */

    public function getComputerSoftware( $computerid )
    {

        return json_decode( $this->getComputer( $computerid )->softwares, true );
    }

    /**
     * Returns true if the computer exists
     *
     * @param $computerid
     *
     * @return bool
     */

    public function computerExists( $computerid )
    {

        if( $this->database->getComputer( $computerid ) == null )
        {

            return false;
        }

        return true;
    }

    /**
     * Returns true if the computer has this software
     *
     * @param $computerid
     *
     * @param $softwareid
     *
     * @return bool
     */

    public function hasSoftware( $computerid, $softwareid )
    {

        $softwares = $this->getComputerSoftware( $computerid );

        foreach( $softwares as $software )
        {

            if( $software['softwareid'] == $softwareid )
            {

                return true;
            }
        }

        return false;
    }

    /**
     * Creates a new computer
     *
     * @param $userid
     *
     * @param $type
     *
     * @param $ipaddress
     *
     * @param array $softwares
     *
     * @param array $hardwares
     *
     * @return int
     */

    public function createComputer( $userid, $type, $ipaddress, $softwares = [], $hardwares = [] )
    {

        $array = array(
            'userid'    => $userid,
            'type'      => $type,
            'ipaddress' => $ipaddress,
            'softwares' => json_encode( $softwares ),
            'hardwares' => json_encode( $hardwares )
        );

        return $this->database->insertComputer( $array );
    }

    /**
     * Adds software to the computers file system
     *
     * @param $computerid
     *
     * @param $softwareid
     *
     * @param $type
     */

    public function addSoftware( $computerid, $softwareid, $type, $softwarename='My Software' )
    {

        $softwares = $this->getComputerSoftware( $computerid );

        $softwares[] = array(
            'softwareid'        => $softwareid,
            'softwarename'      => $softwarename,
            'type'              => $type,
            'installed'         => false,
            'timeinstalled'     => time()
        );

        $this->database->updateComputer( $computerid, array('softwares' => json_encode( $softwares ) ) );
    }

    /**
     * removes a software from the computers list
     *
     * @param $computerid
     *
     * @param $softwareid
     */

    public function removeSoftware( $computerid, $softwareid )
    {

        $softwares = $this->getComputerSoftware( $computerid );

        if( empty( $softwares ) )
        {

            throw new SyscrackException();
        }

        foreach( $softwares as $key=>$software )
        {

            if( $software['softwareid'] == $softwareid )
            {

                unset( $softwares[ $key ] );
            }
        }

        $this->database->updateComputer( $computerid, array('softwares' => json_encode( $softwares ) ) );
    }

    /**
     * Installs a software on the computer side software list
     *
     * @param $computerid
     *
     * @param $softwareid
     */

    public function installSoftware( $computerid, $softwareid )
    {

        $softwares = $this->getComputerSoftware( $computerid );

        if( empty( $softwares ) )
        {

            throw new SyscrackException();
        }

        foreach( $softwares as $key=>$software )
        {

            if( $software['softwareid'] == $softwareid )
            {

                $softwares[ $key ]['installed'] = true;
            }
        }

        $this->database->updateComputer( $computerid, array('softwares' => json_encode( $softwares ) ) );
    }

    /**
     * Uninstalls a software
     *
     * @param $computerid
     *
     * @param $softwareid
     */

    public function uninstallSoftware( $computerid, $softwareid )
    {

        $softwares = $this->getComputerSoftware( $computerid );

        if( empty( $softwares ) )
        {

            throw new SyscrackException();
        }

        foreach( $softwares as $key=>$software )
        {

            if( $software['softwareid'] == $softwareid )
            {

                $softwares[ $key ]['installed'] = false;
            }
        }

        $this->database->updateComputer( $computerid, array('softwares' => json_encode( $softwares ) ) );
    }

    /**
     * Gets the computers hardware
     *
     * @param $computerid
     *
     * @return array
     */

    public function getComputerHardware( $computerid )
    {

        return json_decode( $this->database->getComputer( $computerid )->hardwares, true );
    }

    /**
     * Returns the main ( first ) computer
     *
     * @param $userid
     *
     * @return mixed|\stdClass
     */

    public function getUserMainComputer( $userid )
    {

        return $this->database->getComputersByUser( $userid )[0];
    }

    /**
     * Gets all the users computers
     *
     * @param $userid
     *
     * @return \Illuminate\Support\Collection|null
     */

    public function getUserComputers( $userid )
    {

        return $this->database->getComputersByUser( $userid );
    }

    /**
     * Gets the computers type
     *
     * @param $computerid
     *
     * @return mixed
     */

    public function getComputerType( $computerid )
    {

        return $this->database->getComputer( $computerid )->type;
    }

    /**
     * Gets all the installed software on a computer
     *
     * @param $computerid
     *
     * @return array
     */

    public function getInstalledSoftware( $computerid )
    {

        $softwares = $this->getComputerSoftware( $computerid );

        $result = array();

        foreach( $softwares as $key=>$value )
        {

            if( $value['installed'] == true )
            {

                $result[] = $value['softwareid'];
            }
        }

        return $result;
    }

    /**
     * Gets the install cracker on the machine
     *
     * @param $computerid
     *
     * @return null
     */

    public function getCracker( $computerid )
    {

        $softwares = $this->getComputerSoftware( $computerid );

        foreach( $softwares as $software )
        {

            if( $software['type'] == Settings::getSetting('syscrack_software_cracker_type') )
            {

                if( $software['installed'] == false )
                {

                    continue;
                }

                return $software['softwareid'];
            }
        }

        return null;
    }

    /**
     * Gets the firewall
     *
     * @param $computerid
     *
     * @return null
     */

    public function getFirewall( $computerid )
    {

        $softwares = $this->getComputerSoftware( $computerid );

        foreach( $softwares as $software )
        {

            if( $software['type'] == Settings::getSetting('syscrack_software_hasher_type') )
            {

                if( $software['installed'] == false )
                {

                    continue;
                }

                return $software['softwareid'];
            }
        }

        return null;
    }

    /**
     * Gets the hasher
     *
     * @param $computerid
     *
     * @return null
     */

    public function getHasher( $computerid )
    {

        $softwares = $this->getComputerSoftware( $computerid );

        foreach( $softwares as $software )
        {

            if( $software['type'] == Settings::getSetting('syscrack_software_hasher_type') )
            {

                if( $software['installed'] == false )
                {

                    continue;
                }

                return $software['softwareid'];
            }
        }

        return null;
    }

    /**
     * Returns the collector
     *
     * @param $computerid
     *
     * @return null
     */

    public function getCollector( $computerid )
    {

        $softwares = $this->getComputerSoftware( $computerid );

        foreach( $softwares as $software )
        {

            if( $software['type'] == Settings::getSetting('syscrack_software_collector_type') )
            {

                if( $software['installed'] == false )
                {

                    continue;
                }

                return $software['softwareid'];
            }
        }

        return null;
    }

    /**
     * Gets the current connected user commputer
     *
     * @param $computerid
     */

    public function setCurrentUserComputer( $computerid )
    {

        $_SESSION['current_computer'] = $computerid;
    }

    /**
     * Gets the current connected user computer
     *
     * @return mixed
     */

    public function getCurrentUserComputer()
    {

        return $_SESSION['current_computer'];
    }

    /**
     * Returns true if we have a current connected computer
     *
     * @return bool
     */

    public function hasCurrentComputer()
    {

        if( isset( $_SESSION['current_computer'] ) == false )
        {

            return false;
        }

        return true;
    }

    /**
     * Checks if the user has this type of software installed
     *
     * @param $computerid
     *
     * @param $type
     *
     * @param bool $checkinstall
     *
     * @return bool
     */

    public function hasType( $computerid, $type, $checkinstall=true )
    {

        $softwares = $this->getComputerSoftware( $computerid );

        foreach( $softwares as $software )
        {

            if( $software['type'] == $type )
            {

                if( $software['installed'] == false )
                {

                    continue;
                }

                return true;
            }
        }

        return false;
    }

    /**
     * Gets a software by its name
     *
     * @param $computerid
     *
     * @param $softwarename
     *
     * @param bool $checkinstalled
     *
     * @return mixed|null
     */

    public function getSoftwareByName( $computerid, $softwarename, $checkinstalled=true )
    {
        
        $softwares = $this->getComputerSoftware( $computerid );

        foreach( $softwares as $software )
        {

            if( $software['softwarename'] == $softwarename )
            {

                if( $checkinstalled )
                {

                    if( $software['installed'] == true )
                    {

                        return $software;
                    }
                }
                else
                {

                    return $software;
                }
            }
        }

        return null;
    }

    /**
     * Returns true if this software is installed
     *
     * @param $computerid
     *
     * @param $softwareid
     *
     * @return bool
     */

    public function isInstalled( $computerid, $softwareid )
    {

        $softwares = $this->getComputerSoftware( $computerid );

        if( empty( $softwares ) )
        {

            return false;
        }

        foreach( $softwares as $software )
        {

            if( $software['softwareid'] == $softwareid )
            {

                return $software['installed'];
            }
        }

        return false;
    }

    /**
     * Returns true if the computer is a bank
     *
     * @param $computerid
     *
     * @return bool
     */

    public function isBank( $computerid )
    {

        if( $this->getComputerType( $computerid ) !== Settings::getSetting('syscrack_computer_bank_type') )
        {

            return false;
        }

        return true;
    }

    /**
     * Returns true if the computer is a bitcoin server
     *
     * @param $computerid
     *
     * @return bool
     */

    public function isBitcoin( $computerid )
    {

        if( $this->getComputerType( $computerid ) !== Settings::getSetting('syscrack_computer_bitcoin_type') )
        {

            return false;
        }

        return true;
    }

    /**
     * Returns true if the computer is a market server
     *
     * @param $computerid
     *
     * @return bool
     */

    public function isMarket( $computerid )
    {

        if( $this->getComputerType( $computerid ) !== Settings::getSetting('syscrack_computer_market_type') )
        {

            return false;
        }

        return true;
    }

    /**
     * Returns true if the computer is an NPCs
     *
     * @param $computerid
     *
     * @return bool
     */

    public function isNPC( $computerid )
    {

        if( $this->getComputerType( $computerid ) !== Settings::getSetting('syscrack_computer_npc_type') )
        {

            return false;
        }

        return true;
    }

    /**
     * Returns true if the computer is a VPC
     *
     * @param $computerid
     *
     * @return bool
     */

    public function isVPC( $computerid )
    {

        if( $this->getComputerType( $computerid ) !== Settings::getSetting('syscrack_computer_vpc_type') )
        {

            return false;
        }

        return true;
    }
}
<?php
namespace Framework\Syscrack\Game;

/**
 * Lewis Lancaster 2017
 *
 * Class Computer
 *
 * @package Framework\Syscrack\Game
 */

use Framework\Application\Settings;
use Framework\Application\Utilities\Factory;
use Framework\Application\Utilities\FileSystem;
use Framework\Database\Tables\Computer as Database;
use Framework\Exceptions\SyscrackException;
use Framework\Syscrack\Game\Structures\Computer as Structure;

class Computer
{

    /**
     * @var Factory;
     */

    protected static $factory;

    /**
     * @var Database
     */

    protected $database;

    /**
     * Computer constructor.
     */

    public function __construct()
    {

        $this->database = new Database();

        if( empty( self::$factory ) )
        {

            $this->loadComputers();
        }
    }

    /**
     * Loads the computers into the array
     */

    public function loadComputers()
    {

        self::$factory = new Factory( Settings::getSetting('syscrack_computers_namespace') );

        foreach( FileSystem::getFilesInDirectory( Settings::getSetting('syscrack_computers_location') ) as $file )
        {

            $name = FileSystem::getFileName( $file );

            if( self::$factory->hasClass( $name ) )
            {

                continue;
            }

            self::$factory->createClass( $name );
        }
    }

    /**
     * Gets a computer class
     *
     * @param $name
     *
     * @return Computer
     */

    public function getComputerClass( $name )
    {

        if( self::$factory->hasClass( $name ) == false )
        {

            throw new SyscrackException();
        }

        return self::$factory->findClass( $name );
    }

    /**
     * Returns true if we have this computer class
     *
     * @param $name
     *
     * @return bool
     */

    public function hasComputerClass( $name )
    {

        if( self::$factory->hasClass( $name ) == false )
        {

            return false;
        }

        return true;
    }

    /**
     * Gets the computers configuration
     *
     * @param $name
     *
     * @return mixed
     */

    public function getComputerConfiguration( $name )
    {

        $configuration = self::$factory->findClass( $name )->configuration();

        if( empty( $configuration ) )
        {

            throw new SyscrackException();
        }

        return $configuration;
    }

    /**
     * Calls the computer start up method
     *
     * @param $name
     *
     * @return mixed
     */

    public function onComputerStartup( $name )
    {

        $class = self::$factory->findClass( $name );

        if( empty( $class ) )
        {

            throw new SyscrackException();
        }

        return $class->onStartup();
    }

    /**
     * Finds a computer by its type
     *
     * @param $type
     *
     * @return Computer|null
     */

    public function findComputerByType( $type )
    {

        $classes = self::$factory->getAllClasses();

        foreach( $classes as $class )
        {

            if( $class instanceof Structure  == false )
            {

                throw new SyscrackException();
            }

            /**
             * @var $class Structure
             */

            if( $class->configuration()['type'] == $type )
            {

                return $class;
            }
        }

        return null;
    }

    /**
     * Returns true if we have this computer type
     *
     * @param $type
     *
     * @return bool
     */

    public function hasComputerType( $type )
    {

        if( $this->findComputerByType( $type ) == null )
        {

            return false;
        }

        return true;
    }

    /**
     * @param int $pick
     * @return \Illuminate\Support\Collection|mixed
     */

    public function getAllComputers( $pick=32 )
    {

        return $this->database->getAllComputers( $pick );
    }

    /**
     * Gets the computer count
     *
     * @return int
     */

    public function getComputerCount()
    {

        return $this->database->getComputerCount();
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
            'software' => json_encode([])
        );

        $this->database->updateComputer( $computerid, $array );
    }

    /**
     * Resets the hardware of a computer
     *
     * @param $computerid
     */

    public function resetHardware( $computerid )
    {

        $array = array(
            'hardware' => json_encode( [] )
        );

        $this->database->updateComputer( $computerid, $array );
    }

    /**
     * Sets the hardware of a computer
     *
     * @param $computerid
     *
     * @param array $hardware
     */

    public function setHardware( $computerid, array $hardware )
    {

        $array = array(
            'hardware' => json_encode( $hardware )
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

        return json_decode( $this->getComputer( $computerid )->software, true );
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

        $software = $this->getComputerSoftware( $computerid );

        foreach( $software as $softwares )
        {

            if( $softwares['softwareid'] == $softwareid )
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
     * @param array $software
     *
     * @param array $hardware
     *
     * @return int
     */

    public function createComputer( $userid, $type, $ipaddress, $software = [], $hardware = [] )
    {

        $array = array(
            'userid'    => $userid,
            'type'      => $type,
            'ipaddress' => $ipaddress,
            'software' => json_encode( $software ),
            'hardware' => json_encode( $hardware )
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

    public function addSoftware( $computerid, $softwareid, $type )
    {

        $software = $this->getComputerSoftware( $computerid );

        $software[] = array(
            'softwareid'        => $softwareid,
            'type'              => $type,
            'installed'         => false,
            'timeinstalled'     => time()
        );

        $this->database->updateComputer( $computerid, array('software' => json_encode( $software ) ) );
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

        $software = $this->getComputerSoftware( $computerid );

        if( empty( $software ) )
        {

            throw new SyscrackException();
        }

        foreach( $software as $key=>$softwares )
        {

            if( $softwares['softwareid'] == $softwareid )
            {

                unset( $softwares[ $key ] );
            }
        }

        $this->database->updateComputer( $computerid, array('software' => json_encode( $software ) ) );
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

        $software = $this->getComputerSoftware( $computerid );

        if( empty( $software ) )
        {

            throw new SyscrackException();
        }

        foreach( $software as $key=>$softwares )
        {

            if( $softwares['softwareid'] == $softwareid )
            {

                $software[ $key ]['installed'] = true;
            }
        }

        $this->database->updateComputer( $computerid, array('software' => json_encode( $software ) ) );
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

        $software = $this->getComputerSoftware( $computerid );

        if( empty( $software ) )
        {

            throw new SyscrackException();
        }

        foreach( $software as $key=>$softwares )
        {

            if( $softwares['softwareid'] == $softwareid )
            {

                $software[ $key ]['installed'] = false;
            }
        }

        $this->database->updateComputer( $computerid, array('software' => json_encode( $software ) ) );
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

        return json_decode( $this->database->getComputer( $computerid )->hardware, true );
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

        $software = $this->getComputerSoftware( $computerid );

        $result = array();

        foreach( $software as $key=>$value )
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

        $software = $this->getComputerSoftware( $computerid );

        foreach( $software as $softwares )
        {

            if( $softwares['type'] == Settings::getSetting('syscrack_software_cracker_type') )
            {

                if( $softwares['installed'] == false )
                {

                    continue;
                }

                return $softwares['softwareid'];
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

        $software = $this->getComputerSoftware( $computerid );

        foreach( $software as $softwares )
        {

            if( $softwares['type'] == Settings::getSetting('syscrack_software_hasher_type') )
            {

                if( $softwares['installed'] == false )
                {

                    continue;
                }

                return $softwares['softwareid'];
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

        $software = $this->getComputerSoftware( $computerid );

        foreach( $software as $softwares )
        {

            if( $softwares['type'] == Settings::getSetting('syscrack_software_hasher_type') )
            {

                if( $softwares['installed'] == false )
                {

                    continue;
                }

                return $softwares['softwareid'];
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

        $software = $this->getComputerSoftware( $computerid );

        foreach( $software as $softwares )
        {

            if( $softwares['type'] == Settings::getSetting('syscrack_software_collector_type') )
            {

                if( $softwares['installed'] == false )
                {

                    continue;
                }

                return $softwares['softwareid'];
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

        if( empty( $_SESSION['current_computer'] ) )
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

        $software = $this->getComputerSoftware( $computerid );

        foreach( $software as $softwares )
        {

            if( $softwares['type'] == $type )
            {

                if( $checkinstall && $softwares['installed'] == false )
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
        
        $software = $this->getComputerSoftware( $computerid );

        foreach( $software as $softwares )
        {

            if( $softwares['softwarename'] == $softwarename )
            {

                if( $checkinstalled )
                {

                    if( $softwares['installed'] == true )
                    {

                        return $softwares;
                    }
                }
                else
                {

                    return $softwares;
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

        $software = $this->getComputerSoftware( $computerid );

        if( empty( $software ) )
        {

            return false;
        }

        foreach( $software as $softwares )
        {

            if( $softwares['softwareid'] == $softwareid )
            {

                return $softwares['installed'];
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

        if( $this->getComputerType( $computerid ) !== Settings::getSetting('syscrack_computers_bank_type') )
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

        if( $this->getComputerType( $computerid ) !== Settings::getSetting('syscrack_computers_bitcoin_type') )
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

        if( $this->getComputerType( $computerid ) !== Settings::getSetting('syscrack_computers_market_type') )
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

        if( $this->getComputerType( $computerid ) !== Settings::getSetting('syscrack_computers_npc_type') )
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

        if( $this->getComputerType( $computerid ) !== Settings::getSetting('syscrack_computers_vpc_type') )
        {

            return false;
        }

        return true;
    }
}
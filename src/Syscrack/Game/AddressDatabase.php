<?php
namespace Framework\Syscrack\Game;

/**
 * Lewis Lancaster 2017
 *
 * Class AddressDatabase
 *
 * @package Framework\Syscrack\Game
 */

use Framework\Application\Settings;
use Framework\Application\Utilities\FileSystem;

class AddressDatabase
{

    /**
     * @var mixed
     */

    protected $database;

    /**
     * @var null
     */

    public $userid;

    /**
     * AddressDatabase constructor.
     *
     * @param null $userid
     *
     * @param bool $autoload
     */

    public function __construct( $userid=null, $autoload = true )
    {

        if( $userid != null )
        {

            if( $autoload == true )
            {

                if( $this->hasDatabase( $userid ) )
                {

                    $this->database = $this->getDatabase( $userid );
                }

                $this->userid = $userid;
            }
        }
    }

    /**
     * Finds a computer by their IP address
     *
     * @param $ipaddress
     *
     * @return null
     */

    public function getComputerByIPAddress( $ipaddress )
    {

        if( empty( $this->database ) )
        {

            return null;
        }

        foreach( $this->database as $computer )
        {

            if( $computer['ipaddress'] == $ipaddress )
            {

                return $computer;
            }
        }

        return null;
    }

    /**
     * Adds a computer to the database
     *
     * @param $computer
     */

    public function addComputer( $computer )
    {

        if( empty( $this->database ) )
        {

            return;
        }

        $this->database[] = $computer;
    }

    /**
     * Removes a computer from the hacked database
     *
     * @param $computer
     */

    public function removeComputer( $computer )
    {

        if( empty( $this->database ) )
        {

            return;
        }

        foreach( $this->database as $key=>$value)
        {

            if( $value == $computer )
            {

                unset( $this->database[ $key ] );
            }
        }

        $this->database = array_values( $this->database );
    }

    /**
     * Reads the users IP database
     *
     * @param $userid
     *
     * @return mixed
     */

    public function getDatabase( $userid )
    {

        return $this->readDatabase( $userid );
    }

    /**
     * Returns true if the user has a database file
     *
     * @param $userid
     *
     * @return bool
     */

    public function hasDatabase( $userid )
    {

        if( FileSystem::fileExists(Settings::getSetting('syscrack_addressdatabase_location') . $userid .
            Settings::getSetting('syscrack_addressdatabase_extension')) == false )
        {

            return false;
        }

        return true;
    }

    /**
     * Creates the address database
     *
     * @param $userid
     *
     * @param array|null $data
     */

    public function createDatabase( $userid, array $data=null )
    {

        FileSystem::writeJson(Settings::getSetting('syscrack_addressdatabase_location') . $userid .
            Settings::getSetting('syscrack_addressdatabase_extension'), $data);
    }

    /**
     * Saves the users hacked database
     *
     * @param $userid
     *
     * @param $data
     */

    public function saveDatabase( $userid, $data )
    {

        FileSystem::writeJson(Settings::getSetting('syscrack_addressdatabase_location') . $userid .
            Settings::getSetting('syscrack_addressdatabase_extension'), $data);
    }

    /**
     * Reads the database file
     *
     * @param $userid
     *
     * @return mixed
     */

    private function readDatabase( $userid )
    {

        return FileSystem::readJson( Settings::getSetting('syscrack_addressdatabase_location') . $userid .
            Settings::getSetting('syscrack_addressdatabase_extension') );
    }
}
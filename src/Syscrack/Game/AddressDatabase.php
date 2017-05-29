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
use Framework\Exceptions\SyscrackException;

class AddressDatabase
{

    /**
     * @var array
     */

    protected $database = [];

    /**
     * Checks if the user has an address database
     *
     * @param $userid
     *
     * @return bool
     */

    public function hasDatabase( $userid )
    {

        if( FileSystem::fileExists( $this->getPath() . $userid . '.json' ) == false )
        {

            return false;
        }

        return true;
    }

    /**
     * Gets the users addresses
     *
     * @param $userid
     *
     * @return array|mixed
     */

    public function getUserAddresses( $userid )
    {

        if( $this->hasDatabaseSet() == false )
        {

            $this->database = $this->getUserDatabase( $userid );
        }

        return $this->database;
    }

    /**
     * Deletes an address
     *
     * @param $ipaddress
     *
     * @param $userid
     */

    public function deleteAddress( $ipaddress, $userid )
    {

        if( $this->hasDatabaseSet() == false )
        {

            $this->database = $this->getUserDatabase( $userid );
        }

        if( $this->hasAddress( $ipaddress, $userid ) == false )
        {

            throw new SyscrackException();
        }

        unset( $this->database[ $this->getKeyOfAddress( $ipaddress, $userid )] );

        $this->saveUserDatabase( $userid );
    }

    /**
     * Deletes multiple addresses
     *
     * @param array $ipaddresses
     *
     * @param $userid
     */

    public function deleteMultipleAddresses( array $ipaddresses, $userid )
    {

        if( $this->hasDatabaseSet() == false )
        {

            $this->database = $this->getUserDatabase( $userid );
        }

        foreach( $ipaddresses as $address )
        {

            if( $this->hasAddress( $address['ipaddress'], $userid ) == false )
            {

                continue;
            }

            unset( $this->database[ $this->getKeyOfAddress( $address['ipaddress'], $userid )] );
        }

        $this->saveUserDatabase( $userid );
    }

    /**
     * Adds an address
     *
     * @param $ipaddress
     *
     * @param $userid
     */

    public function addAddress( $ipaddress, $userid )
    {

        if( $this->hasDatabaseSet() == false )
        {

            $this->database = $this->getUserDatabase( $userid );
        }

        $this->database[] = array(
            'ipaddress' => $ipaddress,
            'timehacked' => time()
        );

        $this->saveUserDatabase( $userid );
    }

    /**
     * Adds a virus to the address database
     *
     * @param $ipaddress
     *
     * @param $softwareid
     *
     * @param $userid
     */

    public function addVirus( $ipaddress, $softwareid, $userid )
    {

        if( $this->hasDatabaseSet() == false )
        {

            $this->database = $this->getUserDatabase( $userid );
        }

        if( $this->hasAddress( $ipaddress, $userid ) == false )
        {

            throw new SyscrackException();
        }

        array_merge( $this->database[ $this->getKeyOfAddress( $ipaddress, $userid )], array(
            'virus' => $softwareid
        ));

        $this->saveUserDatabase( $userid );
    }

    /**
     * Returns true if we have hacked this address
     *
     * @param $ipaddress
     *
     * @param $userid
     *
     * @return bool
     */

    public function hasAddress( $ipaddress, $userid )
    {

        if( $this->hasDatabaseSet() == false )
        {

            $this->database =$this->getUserDatabase( $userid );
        }

        foreach( $this->database as $address )
        {

            if( $address['ipaddress'] == $ipaddress )
            {

                return true;
            }
        }

        return false;
    }

    /**
     * Gets the position of this address in our address database
     *
     * @param $ipaddress
     *
     * @param $userid
     *
     * @return int|null|string
     */

    public function getKeyOfAddress( $ipaddress, $userid )
    {


        if( $this->hasDatabaseSet() == false )
        {

            $this->database =$this->getUserDatabase( $userid );
        }

        foreach( $this->database as $key=>$address )
        {

            if( $address['ipaddress'] == $ipaddress )
            {

                return $key;
            }
        }

        return null;
    }

    /**
     * Saves the users database
     *
     * @param $userid
     */

    private function saveUserDatabase( $userid )
    {

        if( $this->hasDatabaseSet() == false )
        {

            throw new SyscrackException();
        }

        FileSystem::writeJson( $this->getPath() . $userid . '.json', $this->database );
    }

    /**
     * Returns true if we have the database set
     *
     * @return bool
     */

    private function hasDatabaseSet()
    {

        if( empty( $this->database ) )
        {

            return false;
        }

        return true;
    }

    /**
     * Gets the user database
     *
     * @param $userid
     *
     * @return mixed
     */

    private function getUserDatabase( $userid )
    {

        return FileSystem::readJson( $this->getPath() . $userid . '.json' );
    }

    /**
     * Gets the path of the address database
     *
     * @return mixed
     */

    private function getPath()
    {

        return Settings::getSetting('syscrack_addressdatabase_location');
    }
}
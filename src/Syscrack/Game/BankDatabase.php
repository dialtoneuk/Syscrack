<?php
namespace Framework\Syscrack\Game;

/**
 * Lewis Lancaster 2017
 *
 * Class BankDatabase
 *
 * @package Framework\Syscrack\Game
 */

use Framework\Application\Utilities\FileSystem;
use Framework\Application\Settings;

class BankDatabase
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
     * BankDatabase constructor.
     *
     * @param null $userid
     *
     * @param bool $autoload
     */

    public function __construct( $userid = null, $autoload = true )
    {

        if( $userid != null )
        {

            if( $autoload )
            {

                $this->database = $this->getDatabase( $userid );

                $this->userid = $userid;
            }
        }
    }

    /**
     * Gets the banks IP address
     *
     * @param $accountnumber
     *
     * @return null
     */

    public function getBankIPAddress( $accountnumber )
    {

        foreach( $this->database as $key=>$value )
        {

            if( $value['accountnumber'] == $accountnumber )
            {

                return $value['ipaddress'];
            }
        }

        return null;
    }

    /**
     * Checks if this account number is in the database
     *
     * @param $accountnumber
     *
     * @return bool
     */

    public function hasAccountNumber( $accountnumber )
    {

        foreach( $this->database as $key=>$value )
        {

            if( $value['accountnumber'] == $accountnumber )
            {

                return true;
            }
        }

        return false;
    }

    /**
     * Returns true if the users database file exists
     *
     * @param $userid
     *
     * @return bool
     */

    public function hasDatabase( $userid )
    {

        if( FileSystem::fileExists( $this->getFile( $userid ) ) == false )
        {

            return false;
        }

        return true;
    }

    /**
     * Loads the database
     *
     * @param $userid
     */

    public function loadDatabase( $userid )
    {

        $this->database = $this->getDatabase( $userid );
    }

    /**
     * Gets the bank database
     *
     * @param $userid
     *
     * @return mixed
     */

    public function getDatabase( $userid )
    {

        return FileSystem::readJson( $this->getFile( $userid ) );
    }

    /**
     * Saves the database to file
     *
     * @param $userid
     *
     * @param array $data
     */

    public function saveDatabase( $userid, array $data )
    {

        FileSystem::writeJson( $this->getFile( $userid ), $data );
    }

    /**
     * Gets the file path
     *
     * @param $userid
     *
     * @return string
     */

    public function getFile( $userid )
    {

        return Settings::getSetting('syscrack_bankdatabase_location') . $userid .
            Settings::getSetting('syscrack_filedatabase_extension');
    }
}
<?php
namespace Framework\Syscrack\Game;

/**
 * Lewis Lancaster 2017
 *
 * Class Computer
 *
 * @package Framework\Syscrack\Game
 */

use Framework\Database\Tables\Computers;

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

        return json_decode( $this->database->getComputer( $computerid )->software, true );
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
}
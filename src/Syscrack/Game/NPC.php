<?php
namespace Framework\Syscrack\Game;

/**
 * Lewis Lancaster 2017
 *
 * Class NPC
 *
 * @package Framework\Syscrack\Game
 */

use Framework\Application\Settings;
use Framework\Application\Utilities\FileSystem;
use Framework\Database\Tables\Computers;
use Framework\Exceptions\SyscrackException;

class NPC
{

    /**
     * @var Computers
     */

    protected $computers;

    /**
     * NPC constructor.
     */

    public function __construct()
    {

        $this->computers = new Computers();
    }

    /**
     * Gets all of the NPC's
     *
     * @return mixed|null
     */

    public function getNPCs()
    {

        return $this->computers->getComputerByType( Settings::getSetting('syscrack_npc_type') );
    }

    /**
     * Returns true if the user is an NPC
     *
     * @param $computerid
     *
     * @return bool
     */

    public function isNPC( $computerid )
    {

        if( $this->computers->getComputer( $computerid )->type != Settings::getSetting('syscrack_npc_type') )
        {

            return false;
        }

        return true;
    }

    /**
     * Resets the NPC's softwares and hardwares
     *
     * @param $computerid
     */

    public function resetNPC( $computerid )
    {

        if( $this->hasDefaultState( $computerid ) == false )
        {

            throw new SyscrackException();
        }

        $this->computers->updateComputer( $computerid, array(
            'softwares' => $this->getNPCDefaultSoftware( $computerid ),
            'hardwares' => $this->getNPCDefaultHardware( $computerid )
        ));
    }

    /**
     * Gets the NPC's name
     *
     * @param $computerid
     *
     * @return mixed
     */

    public function getNPCName( $computerid )
    {

        $default = $this->getDefaultState( $computerid );

        if( empty( $default ) )
        {

            throw new SyscrackException();
        }

        return $default['name'];
    }

    /**
     * Gets the default NPC webpage
     *
     * @param $computerid
     *
     * @return mixed
     */

    public function getNPCWebpage( $computerid )
    {

        $default = $this->getDefaultState( $computerid );

        if( empty( $default ) )
        {

            throw new SyscrackException();
        }

        return $default['webpage'];
    }

    /**
     * Gets the default NPC hardware
     *
     * @param $computerid
     *
     * @return mixed
     */

    public function getNPCDefaultSoftware( $computerid )
    {

        $default = $this->getDefaultState( $computerid );

        if( empty( $default ) )
        {

            throw new SyscrackException();
        }

        return $default['softwares'];
    }

    /**
     * Gets the default NPC hardware
     *
     * @param $computerid
     *
     * @return mixed
     */

    public function getNPCDefaultHardware( $computerid )
    {

        $default = $this->getDefaultState( $computerid );

        if( empty( $default ) )
        {

            throw new SyscrackException();
        }

        return $default['hardwares'];
    }

    /**
     * Gets the npcs puzzle reward address
     *
     * @param $computerid
     *
     * @return mixed
     */

    public function getNPCDefaultPuzzle( $computerid )
    {

        $default = $this->getDefaultState( $computerid );

        if( empty( $default ) )
        {

            throw new SyscrackException();
        }

        return $default['puzzle'];
    }

    /**
     * Returns true if the NPC is a puzzle NPC
     *
     * @param $computerid
     *
     * @return bool
     */

    public function isNPCPuzzle( $computerid )
    {

        $default = $this->getDefaultState( $computerid );

        if( isset( $default['puzzle'] ) == false )
        {

            return false;
        }

        return true;
    }

    /**
     * Gets the NPC's default state
     *
     * @param $computerid
     *
     * @return mixed
     */

    public function getDefaultState( $computerid )
    {

        return FileSystem::readJson( $this->getFilepath( $computerid ) );
    }

    /**
     * Returns true if the NPC has a default state
     *
     * @param $computerid
     *
     * @return bool
     */

    public function hasDefaultState( $computerid )
    {

        if( FileSystem::fileExists( $this->getFilepath( $computerid ) ) == false )
        {

            return false;
        }

        return true;
    }

    /**
     * Gets the filepath of the default NPC json files
     *
     * @param $computerid
     *
     * @return string
     */

    private function getFilepath( $computerid )
    {

        return Settings::getSetting('syscrack_npc_filepath') . $computerid . ".json";
    }
}
<?php
namespace Framework\Syscrack\Game;

/**
 * Lewis Lancaster 2017
 *
 * Class NPC
 *
 * @package Framework\Syscrack\Game
 *
 * TODO: Rewrite this to instead take software level, software type and software name and create a new software instead of using a 'software id'
 */

use Framework\Application\Settings;
use Framework\Application\Utilities\FileSystem;
use Framework\Database\Tables\Computers;

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
     * Returns true if we have an NPC Page
     *
     * @param $computerid
     *
     * @return bool
     */

    public function hasNPCPage( $computerid )
    {

        $npcfile = $this->getNPCFile( $computerid );

        if( isset( $npcfile['page'] ) == false )
        {

            return false;
        }

        if( $this->NPCPageExists( $computerid ) == false )
        {

            return false;
        }

        return true;
    }

    /**
     * Returns true if an NPC Page exists
     *
     * @param $computerid
     *
     * @return bool
     */

    public function NPCPageExists( $computerid )
    {

        if( FileSystem::fileExists( 'views/' . $this->getNPCPageLocation( $computerid ) ) == false )
        {

            return false;
        }

        return true;
    }

    /**
     * Gets the location of this computers page
     *
     * @param $computerid
     *
     * @return string
     */

    public function getNPCPageLocation( $computerid )
    {

        return Settings::getSetting('syscrack_npc_page_location') . $this->getNPCFile( $computerid )['page'] . '.php';
    }

    /**
     * Gets the NPC File tied to this computer
     *
     * @param $computerid
     *
     * @return mixed
     */

    public function getNPCFile( $computerid )
    {

        return FileSystem::readJson( Settings::getSetting('syscrack_npc_filepath') . $computerid . '.json' );
    }

    /**
     * Returns true if we have an NPC file
     *
     * @param $computerid
     *
     * @return bool
     */

    public function hasNPCFile( $computerid )
    {

        if( FileSystem::fileExists( Settings::getSetting('syscrack_npc_filepath') . $computerid . '.json' ) == false )
        {

            return false;
        }

        return true;
    }
}
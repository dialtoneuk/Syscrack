<?php
namespace Framework\Syscrack\Game;

/**
 * Lewis Lancaster 2017
 *
 * Class Schema
 *
 * @package Framework\Syscrack\Game
 *
 */

use Framework\Application\Settings;
use Framework\Application\Utilities\FileSystem;
use Framework\Database\Tables\Computers;

class Schema
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
     * Creates a new schema file
     *
     * @param $computerid
     *
     * @param string $name
     *
     * @param string $page
     *
     * @param array $riddles
     *
     * @param array $softwares
     *
     * @param array $hardwares
     */

    public function createSchema( $computerid, $name='Default', $page='schema.default', array $riddles, array $softwares, array $hardwares )
    {

        $schema = array(
            'name'      => $name,
            'page'      => $page,
            'riddles'   => $riddles,
            'softwares' => $softwares,
            'hardwares' => $hardwares
        );

        FileSystem::writeJson( $this->getSchemaPath( $computerid ), $schema );
    }

    public function setSchema( $computerid, $schema = [] )
    {

        FileSystem::writeJson( $this->getSchemaPath( $computerid ), $schema );
    }

    /**
     * Returns true if we have an NPC Page
     *
     * @param $computerid
     *
     * @return bool
     */

    public function hasSchemaPage($computerid )
    {

        $schema = $this->getSchema( $computerid );

        if( isset( $schema['page'] ) == false )
        {

            return false;
        }

        if( $this->SchemaPageExists( $computerid ) == false )
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

    public function SchemaPageExists($computerid )
    {

        if( FileSystem::fileExists( '/themes/' .   Settings::getSetting('render_folder' ) . DIRECTORY_SEPARATOR . $this->getSchemaPageLocation( $computerid ) ) == false )
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

    public function getSchemaPageLocation($computerid )
    {

        return Settings::getSetting('syscrack_schema_page_location') . $this->getSchema( $computerid )['page'] . '.php';
    }

    /**
     * Gets the NPC File tied to this computer
     *
     * @param $computerid
     *
     * @return mixed
     */

    public function getSchema($computerid )
    {

        return FileSystem::readJson( Settings::getSetting('syscrack_schema_filepath') . $computerid . '.json' );
    }

    /**
     * Gets the schemas path
     *
     * @param $computerid
     *
     * @return string
     */

    public function getSchemaPath( $computerid )
    {

        return Settings::getSetting('syscrack_schema_filepath') . $computerid . '.json';
    }

    /**
     * Returns true if we have an NPC file
     *
     * @param $computerid
     *
     * @return bool
     */

    public function hasSchema($computerid )
    {

        if( FileSystem::fileExists( $this->getSchemaPath( $computerid ) ) == false )
        {

            return false;
        }

        return true;
    }
}
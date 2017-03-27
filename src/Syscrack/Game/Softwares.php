<?php
namespace Framework\Syscrack\Game;

/**
 * Lewis Lancaster 2017
 *
 * Class Softwares
 *
 * @package Framework\Syscrack\Game
 */

use Framework\Application\Settings;
use Framework\Application\Utilities\FileSystem;
use Framework\Application\Utilities\Factory;
use Framework\Exceptions\SyscrackException;
use Framework\Database\Tables\Softwares as Database;
use Framework\Syscrack\Game\Structures\Software as Structure;
use Illuminate\Support\Facades\File;

class Softwares
{

    /**
     * @var Factory
     */

    protected $factory;

    /**
     * @var Database
     */

    protected $database;

    /**
     * Softwares constructor.
     *
     * @param bool $autoload
     */

    public function __construct( $autoload=true )
    {

        $this->factory = new Factory( Settings::getSetting('syscrack_software_namespace') );

        $this->database = new Database();

        if( $autoload == true )
        {

            $this->loadSoftwares();
        }
    }

    /**
     * Gets the software from the database
     *
     * @param $softwareid
     *
     * @return mixed|null
     */

    public function getDatabaseSoftware( $softwareid )
    {

        return $this->database->getSoftware( $softwareid );
    }

    /**
     * Returns true if this softwareid exists
     *
     * @param $softwareid
     *
     * @return bool
     */

    public function softwareExists( $softwareid )
    {

        if( $this->database->getSoftware( $softwareid ) == null )
        {

            return false;
        }

        return true;
    }

    /**
     * Gets the software class related to this software id
     *
     * @param $softwareid
     *
     * @return null
     */

    public function getSoftwareClassFromID( $softwareid )
    {

        return $this->findSoftwareByUniqueName( $this->database->getSoftware( $softwareid )->uniquename );
    }

    /**
     * Deletes the software
     *
     * @param $softwareid
     */

    public function deleteSoftware( $softwareid )
    {

        $this->database->deleteSoftware( $softwareid );
    }

    /**
     * Deletes all the software related to a computer id
     *
     * @param $computerid
     */

    public function deleteSoftwaresByComputer( $computerid )
    {

        $this->database->deleteSoftwareByComputer( $computerid );
    }

    /**
     * Creates a new piece of software
     *
     * @param $software
     *
     * @param $userid
     *
     * @param $computerid
     *
     * @return int
     */

    public function createSoftware( $software, int $userid, int $computerid, string $softwarename='My Software' )
    {

        if( $this->hasSoftware( $software ) == false )
        {

            throw new SyscrackException();
        }

        $class = $this->getSoftwareClass( $software );

        if( $class instanceof Structure == false )
        {

            throw new SyscrackException();
        }

        $configuration = $class->configuration();

        $array = array(
            'userid'        => $userid,
            'computerid'    => $computerid,
            'level'         => $class->getDefaultLevel(),
            'size'          => $class->getDefaultSize(),
            'uniquename'    => $configuration['uniquename'],
            'type'          => $configuration['type'],
            'softwarename'  => $softwarename,
            'lastmodified'  => time(),
            'installed'     => false
        );

        return $this->database->insertSoftware( $array );
    }

    /**
     * Gets the software name from the software ID
     *
     * @param $softwareid
     *
     * @return int|null|string
     */

    public function getSoftwareNameFromSoftwareID( $softwareid )
    {

        return $this->getNameFromClass( $this->findSoftwareByUniqueName( $this->getDatabaseSoftware( $softwareid )->uniquename ) );
    }

    /**
     * Installs a software
     *
     * @param $softwareid
     */

    public function installSoftware( $softwareid, $userid )
    {

        $array = array(
            'installed' => true,
            'userid'    => $userid
        );

        $this->database->updateSoftware( $softwareid, $array );
    }

    /**
     * Uninstalls a software
     *
     * @param $softwareid
     */

    public function uninstallSoftware( $softwareid )
    {

        $array = array(
            'installed' => false
        );

        $this->database->updateSoftware( $softwareid, $array );
    }

    /**
     * Finds a software class by its unique name
     *
     * @param $uniquename
     *
     * @return Structure
     */

    public function findSoftwareByUniqueName( $uniquename )
    {

        $classes = $this->factory->getAllClasses();

        foreach( $classes as $key=>$class )
        {

            if( $class instanceof Structure == false )
            {

                throw new SyscrackException();
            }

            /** @var Structure $class */
            if( $class->configuration()['uniquename'] == $uniquename )
            {

                return $class;
            }
        }

        return null;
    }

    /**
     * Returns the name of the software from class
     *
     * @param $softwareclass
     *
     * @return int|null|string
     */

    public function getNameFromClass( $softwareclass )
    {

        $factory = $this->factory->getAllClasses();

        foreach( $factory as $key=>$value )
        {

            if( $value == $softwareclass )
            {

                return $key;
            }
        }

        return null;
    }

    /**
     * Returns true if this software is installable
     *
     * @param $softwareid
     *
     * @return bool
     */

    public function canInstall( $softwareid )
    {

        $software = $this->database->getSoftware( $softwareid );

        if( $software == null )
        {

            throw new SyscrackException();
        }

        $softwareclass = $this->findSoftwareByUniqueName( $software->uniquename );

        if( $softwareclass->configuration()['installable'] == false )
        {

            return false;
        }

        return true;
    }

    /**
     * Gets the software class, which is used when processing what a software actually does
     *
     * @param $software
     *
     * @return Structure
     */

    public function getSoftwareClass( $software )
    {

        return $this->factory->findClass( $software );
    }

    /**
     * Gets the softwares type
     *
     * @param $software
     */

    public function getSoftwareType( $software )
    {

        return $this->getSoftwareClass( $software )->configuration()['type'];
    }

    /**
     * Gets the software extension
     *
     * @param $software
     */

    public function getSoftwareExtension( $software )
    {

        return $this->getSoftwareClass( $software )->configuration()['extension'];
    }

    /**
     * Gets the softwares unique name
     *
     * @param $software
     *
     * @return mixed
     */

    public function getSoftwareUniqueName( $software )
    {

        return $this->getSoftwareClass( $software )->configuration()['unqiuename'];
    }

    /**
     * Returns wether the software is installable or not
     *
     * @param $software
     *
     * @return mixed
     */

    public function getSoftwareInstallable( $software )
    {

        return $this->getSoftwareClass( $software )->configuration()['installable'];
    }

    /**
     * Gets the softwares default file size on the users system
     *
     * @param $software
     *
     * @return float
     */

    public function getSoftwareDefaultSize( $software )
    {

        return $this->getSoftwareClass( $software )->getDefaultSize();
    }

    /**
     * Gets the softwares default level
     *
     * @param $software
     *
     * @return float
     */

    public function getSoftwareDefaultLevel( $software )
    {

        return $this->getSoftwareClass( $software )->getDefaultLevel();
    }

    /**
     * Executes a method inside the given software class
     *
     * @param $software
     *
     * @param string $method
     *
     * @param array $parameters
     *
     * @return mixed|null
     */

    public function executeSoftwareMethod( $software, $method='onExecute', array $parameters )
    {

        $software = $this->getSoftwareClass( $software );

        if( $this->isCallable( $software, $method ) == false )
        {

            return null;
        }

        if( empty( $parameters ) == false )
        {

            return call_user_func_array( array( $software, $method ), $parameters );
        }

        return $software->{ $method }();
    }

    /**
     * Returns true if we have this software
     *
     * @param $software
     *
     * @return bool
     */

    public function hasSoftware( $software )
    {

        if( $this->factory->hasClass( $software ) == false )
        {

            return false;
        }

        return true;
    }

    /**
     * Returns true if this software is installed
     *
     * @param $softwareid
     *
     * @param $computerid
     *
     * @return bool
     */

    public function isInstalled( $softwareid, $computerid )
    {

        if( $this->getDatabaseSoftware( $softwareid )->$computerid !== $computerid )
        {

            return false;
        }

        if( $this->getDatabaseSoftware( $softwareid )->installed == false )
        {

            return false;
        }

        return true;
    }

    /**
     * Returns true if the method is callable
     *
     * @param $software
     *
     * @param $method
     *
     * @return bool
     */

    private function isCallable( Structure $software, string $method )
    {

        $requirements = Settings::getSetting('syscrack_software_allowedmethods');

        if( isset( $requirements[ $method ] ) )
        {

            throw new SyscrackException('Method is not in the allowed callable methods');
        }

        $software = new \ReflectionClass( $software );

        if( $software->getMethod( $method )->isPublic() == false )
        {

            return false;
        }

        return true;
    }

    /**
     * Loads all the software classes into the factory
     */

    private function loadSoftwares()
    {

        $softwares = FileSystem::getFilesInDirectory( Settings::getSetting('syscrack_software_location') );

        foreach( $softwares as $software )
        {

            $this->factory->createClass( FileSystem::getFileName( $software ) );
        }
    }
}
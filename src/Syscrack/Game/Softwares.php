<?php
namespace Framework\Syscrack\Game;

/**
 * Lewis Lancaster 2017
 *
 * Class Softwares
 *
 * @package Framework\Syscrack\Game
 *
 * //TODO: On rewrite, try use classes as return variables instead of booleans in order to display more detailed information
 */

use Framework\Application\Settings;
use Framework\Application\Utilities\FileSystem;
use Framework\Application\Utilities\Factory;
use Framework\Exceptions\SyscrackException;
use Framework\Syscrack\Game\Structures\Software;
use Framework\Database\Tables\Softwares as Database;
use Framework\Syscrack\Game\Structures\Software as Structure;

class Softwares
{

    /**
     * @var Factory
     *
     * Since theres an issue with the software forever in a loop loading the software classes, this static variable
     * holds these classes so we don't reload them
     *
     * TODO: rewrite this so this isn't a problem
     */

    protected static $factory;

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

        $this->database = new Database();

        if( $autoload )
        {

            if( empty( self::$factory ) )
            {

                self::$factory = new Factory( Settings::getSetting('syscrack_software_namespace') );

                $this->loadSoftwares();
            }
        }
    }

    /**
     * Loads all the software classes into the factory
     */

    private function loadSoftwares()
    {

        $softwares = FileSystem::getFilesInDirectory( Settings::getSetting('syscrack_software_location') );


        foreach( $softwares as $software )
        {


            if( self::$factory->hasClass( FileSystem::getFileName( $software ) ) )
            {

                continue;
            }

            self::$factory->createClass( FileSystem::getFileName( $software ) );
        }
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
     * @return Software
     */

    public function getSoftwareClassFromID( $softwareid )
    {

        return $this->findSoftwareByUniqueName( $this->database->getSoftware( $softwareid )->uniquename );
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

        $classes = self::$factory->getAllClasses();

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

        if( $this->hasSoftwareClass( $software ) == false )
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
     * Copys a software
     *
     * @param $targetid
     *
     * @param $computerid
     *
     * @param $userid
     *
     * @return int
     */

    public function copySoftware( $targetid, $computerid, $userid )
    {

        $software = $this->database->getSoftware( $targetid );

        $array = array(
            'userid'        => $userid,
            'computerid'    => $computerid,
            'level'         => $software->level,
            'size'          => $software->size,
            'uniquename'    => $software->uniquename,
            'type'          => $software->type,
            'softwarename'  => $software->softwarename,
            'lastmodified'  => time(),
            'installed'     => $software->installed,
            'data'          => $software->data
        );

        return $this->database->insertSoftware( $array );
    }

    /**
     * Returns true if we have this software class in our factory
     *
     * @param $software
     *
     * @return bool
     */

    public function hasSoftwareClass( $software )
    {

        if( self::$factory->hasClass( $software ) == false )
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

        return self::$factory->findClass( $software );
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

        return $this->getNameFromClass( $this->findSoftwareByUniqueName( $this->getSoftware( $softwareid )->uniquename ) );
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

        $factory = self::$factory->getAllClasses();

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
     * Gets the software from the database
     *
     * @param $softwareid
     *
     * @return mixed|null
     */

    public function getSoftware( $softwareid )
    {

        return $this->database->getSoftware( $softwareid );
    }

    /**
     * Gets all of the viruses currently installed on the computer
     *
     * @param $computerid
     *
     * @return \Illuminate\Support\Collection|null
     */

    public function getVirusesOnComputer( $computerid )
    {

        return $this->database->getTypeOnComputer( Settings::getSetting('syscrack_virus_type'), $computerid );
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

        if( isset( $softwareclass->configuration()['installable'] ) == false )
        {

            return false;
        }

        if( $softwareclass->configuration()['installable'] == false )
        {

            return false;
        }

        return true;
    }

    /**
     * Returns true if this software cannot be uninstalled
     *
     * @param $softwareid
     *
     * @return bool
     */

    public function canUninstall( $softwareid )
    {

        $software = $this->database->getSoftware( $softwareid );

        if( $software == null )
        {

            throw new SyscrackException();
        }

        $softwareclass = $this->findSoftwareByUniqueName( $software->uniquename );

        if( isset( $softwareclass->configuration()['uninstallable'] ) == false )
        {

            return false;
        }

        if( $softwareclass->configuration()['uninstallable'] == false )
        {

            return false;
        }

        return true;
    }

    /**
     * Returns true if a software is executable
     *
     * @param $softwareid
     *
     * @return bool
     */

    public function canExecute( $softwareid )
    {

        $software = $this->database->getSoftware( $softwareid );

        if( $software == null )
        {

            throw new SyscrackException();
        }

        $softwareclass = $this->findSoftwareByUniqueName( $software->uniquename );

        if( isset( $softwareclass->configuration()['executable'] ) == false )
        {

            return true;
        }

        if( $softwareclass->configuration()['executable'] == false )
        {

            return false;
        }

        return true;
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
     * Returns true if this data is set
     *
     * @param $softwareid
     *
     * @return bool
     */

    public function hasData( $softwareid )
    {

        $software = $this->database->getSoftware( $softwareid );

        if( $software->data == null || empty( $software->data ) )
        {

            return false;
        }

        return true;
    }

    /**
     * Gets the software's data
     *
     * @param $softwareid
     *
     * @return mixed
     */

    public function getSoftwareData( $softwareid )
    {

        return json_decode( $this->database->getSoftware( $softwareid )->data, true );
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

        if( $this->getSoftware( $softwareid )->$computerid !== $computerid )
        {

            return false;
        }

        if( $this->getSoftware( $softwareid )->installed == false )
        {

            return false;
        }

        return true;
    }
}
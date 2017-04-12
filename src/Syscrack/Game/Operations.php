<?php
namespace Framework\Syscrack\Game;

/**
 * Lewis Lancaster 2017
 *
 * Class Operations
 *
 * @package Framework\Syscrack\Game
 */

use Framework\Application\Settings;
use Framework\Application\Utilities\Factory;
use Framework\Application\Utilities\FileSystem;
use Framework\Database\Tables\Processes as Database;
use Framework\Exceptions\SyscrackException;
use Framework\Syscrack\Game\Structures\Operation;

class Operations
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
     * @var Hardware
     */

    protected $hardware;

    /**
     * Processes constructor.
     *
     * @param bool $autoload
     */

    public function __construct( $autoload=true )
    {

        $this->factory = new Factory( "Framework\\Syscrack\\Game\\Operations\\" );

        $this->database = new Database();

        if( $autoload )
        {

            $this->getProcessesClasses();
        }
    }

    /**
     * Returns true if a process exists
     *
     * @param $processid
     *
     * @return bool
     */

    public function processExists( $processid )
    {

        if( $this->database->getProcess( $processid ) == null )
        {

            return false;
        }

        return true;
    }

    /**
     * Gets a process
     *
     * @param $processid
     *
     * @return \Illuminate\Support\Collection|null
     */

    public function getProcess( $processid )
    {

        return $this->database->getProcess( $processid );
    }

    /**
     * Gets all of the users processes
     *
     * @param $userid
     *
     * @return \Illuminate\Support\Collection|null
     */

    public function getUserProcesses( $userid )
    {

        return $this->database->getUserProcesses( $userid );
    }

    /**
     * Gets the processes of a computer
     *
     * @param $computerid
     *
     * @return \Illuminate\Support\Collection|null
     */

    public function getComputerProcesses( $computerid )
    {

        return $this->database->getComputerProcesses( $computerid );
    }

    /**
     * Creates a new process and adds it to the database
     *
     * @param $timecompleted
     *
     * @param $computerid
     *
     * @param $userid
     *
     * @param $process
     *
     * @param array $data
     *
     * @return int
     */

    public function createProcess( $timecompleted, $computerid, $userid, $process, array $data )
    {

        if( $this->findProcessClass( $process ) == false )
        {

            throw new SyscrackException();
        }

        $result = $this->callProcessMethod( $this->findProcessClass( $process ), 'onCreation', array(
            'timecompleted' => $timecompleted,
            'computerid'    => $computerid,
            'userid'        => $userid,
            'process'       => $process,
            'data'          => $data
        ));

        if( $result == false )
        {

            return false;
        }

        return $this->addToDatabase( $timecompleted, $computerid, $userid, $process, $data );
    }

    /**
     * Returns true if the process can be completed
     *
     * @param $processid
     *
     * @return bool
     */

    public function canComplete( $processid )
    {

        $process = $this->database->getProcess( $processid );

        if( time() - $process->timecompleted < 0 )
        {

            return false;
        }

        return true;
    }

    /**
     * Adds a process to the database
     *
     * @param $timecompleted
     *
     * @param $computerid
     *
     * @param $userid
     *
     * @param $process
     *
     * @param array $data
     *
     * @return int
     */

    public function addToDatabase( $timecompleted, $computerid, $userid, $process, array $data )
    {

        $array = array(
            'timecompleted' => $timecompleted,
            'timestarted'   => time(),
            'computerid'    => $computerid,
            'userid'        => $userid,
            'process'       => $process,
            'data'          => json_encode( $data )
        );

        return $this->database->insertProcess( $array );
    }

    /**
     * Completes the process
     *
     * @param $processid
     */

    public function completeProcess( $processid )
    {

        $process = $this->getProcess( $processid );

        if( empty( $process ) )
        {

            throw new SyscrackException();
        }


        $this->database->trashProcess( $processid );

        $this->callProcessMethod( $this->findProcessClass( $process->process ), 'onCompletion', array(
            'timecompleted' => $process->timecompleted,
            'timestarted'   => $process->timestarted,
            'computerid'    => $process->computerid,
            'userid'        => $process->userid,
            'process'       => $process->process,
            'data'          => json_decode( $process->data, true )
        ));
    }

    /**
     * Returns true if the user has processes
     *
     * @param $userid
     *
     * @return bool
     */

    public function userHasProcesses( $userid )
    {

        if( $this->database->getUserProcesses( $userid ) == null )
        {

            return false;
        }

        return true;
    }

    /**
     * Returns true if the computer has processes
     *
     * @param $computerid
     *
     * @return bool
     */

    public function computerHasProcesses( $computerid )
    {

        if( $this->database->getComputerProcesses( $computerid ) == null )
        {

            return false;
        }

        return true;
    }

    /**
     * Returns true if we have this process class
     *
     * @param $process
     *
     * @return bool
     */

    public function hasProcessClass( $process )
    {

        if( $this->factory->hasClass( $process ) == false )
        {

            return false;
        }

        return true;
    }

    /**
     * Finds a process class
     *
     * @param $process
     *
     * @return mixed|null
     */

    public function findProcessClass( $process )
    {

        return $this->factory->findClass( $process );
    }

    /**
     * Calls a method inside the process class
     *
     * @param Operation $process
     *
     * @param string $method
     *
     * @param array $data
     *
     * @return mixed
     */

    private function callProcessMethod(Operation $process, $method='onCreation', array $data )
    {

        if( $process instanceof Operation === false )
        {

            throw new SyscrackException();
        }

        if( $this->isCallable( $process, $method ) == false )
        {

            throw new SyscrackException();
        }

        return call_user_func_array( array( $process, $method ), $data );
    }

    /**
     * Returns true if the function is callable
     *
     * @param $process
     *
     * @param $method
     *
     * @return bool
     */

    private function isCallable( $process, $method )
    {

        $class = new \ReflectionClass( $process );

        if( empty( $class ) )
        {

            return false;
        }

        if( $class->getMethod( $method )->isPublic() == false )
        {

            return false;
        }

        return true;
    }

    /**
     * @param $computerid
     *
     * @param $softwareid
     *
     * @param float $speedness
     *
     * @return float|int|null


    public function getCompletionTime( $computerid, $softwareid=null, $speedness=5.0 )
    {

        $softwares = new Softwares();

        $timehelper = new TimeHelper();

        if( $this->hardware->hasHardwareType( $computerid, Settings::getSetting('syscrack_cpu_type')) == null )
        {

            return null;
        }

        if( $softwareid !== null )
        {

            $software = $softwares->getSoftwareData( $softwareid );

            $cpu = $this->hardware->getCPUSpeed( $computerid );

            $seconds = floor( $timehelper->getSecondsInFuture( sqrt( $cpu / $software->level ) / ( $cpu * Settings::getSetting('syscrack_global_speed' ) * $speedness ) ) );

            if( $seconds <= 0 )
            {

                return null;
            }

            return $timehelper->getSecondsInFuture( $seconds );
        }
    }
    */

    /**
     * Gets all the processes
     *
     * @return array|Structures\Software|null|\stdClass
     */

    private function getProcessesClasses()
    {

        if( empty( $this->factory->getAllClasses() ) == false )
        {

            throw new SyscrackException();
        }

        $files = FileSystem::getFilesInDirectory( Settings::getSetting('syscrack_operations_location') );

        if( empty( $files ) )
        {

            throw new SyscrackException();
        }

        foreach( $files as $file )
        {

            $this->factory->createClass( FileSystem::getFileName( $file ) );
        }
    }

}
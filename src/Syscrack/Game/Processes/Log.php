<?php
namespace Framework\Syscrack\Game\Processes;

/**
 * Lewis Lancaster 2017
 *
 * Class Log
 *
 * @package Framework\Syscrack\Game\Processes
 */

use Framework\Exceptions\SyscrackException;
use Framework\Syscrack\Game\Structures\Process;
use Framework\Syscrack\Game\Log as LogManager;
use Framework\Syscrack\Game\Internet;
use Flight;

class Log implements Process
{

    /**
     * @var LogManager
     */

    protected $log;

    /**
     * @var Internet
     */

    protected $internet;

    /**
     * Log constructor.
     */

    public function __construct()
    {

        $this->log = new LogManager();

        $this->internet = new Internet();
    }

    /**
     * Called when the process is created
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
     * @return bool
     */

    public function onCreation($timecompleted, $computerid, $userid, $process, array $data)
    {

        if( isset( $data['ipaddress'] ) == false )
        {

            throw new SyscrackException();
        }

        $computer = $this->internet->getComputer( $data['ipaddress'] );

        if( $this->log->hasLog( $computer->computerid ) == false )
        {

            return false;
        }

        return true;
    }

    /**
     * Called when a process is completed
     *
     * @param $timecompleted
     *
     * @param $timestarted
     *
     * @param $computerid
     *
     * @param $userid
     *
     * @param $process
     *
     * @param array $data
     */

    public function onCompletion($timecompleted, $timestarted, $computerid, $userid, $process, array $data)
    {

        if( isset( $data['ipaddress'] ) == false )
        {

            throw new SyscrackException();
        }

        $this->log->saveLog( $this->internet->getComputer( $data['ipaddress'] )->computerid, [] );

        $this->redirectSuccess( $data['ipaddress'] );
    }

    /**
     * gets the time in seconds it takes to complete an action
     *
     * @param $computerid
     *
     * @param $ipaddress
     *
     * @param $process
     *
     * @return null
     */

    public function getCompletionTime($computerid, $ipaddress, $process)
    {

        return null;
    }

    /**
     * Redirects the user to an error page
     *
     * @param string $message
     *
     * @param string $ipaddress
     */

    private function redirectError( $message='', $ipaddress='' )
    {

        if( $ipaddress !== '' )
        {

            Flight::redirect('/game/internet/' . $ipaddress . "?error=" . $message ); exit;
        }

        Flight::redirect('/game/internet/?error=' . $message ); exit;
    }

    /**
     * Redirects the user to a success page
     *
     * @param string $ipaddress
     */

    private function redirectSuccess( $ipaddress='' )
    {

        if( $ipaddress !== '' )
        {

            Flight::redirect('/game/internet/' . $ipaddress . "?success" ); exit;
        }

        Flight::redirect('/game/internet/?success'); exit;
    }
}
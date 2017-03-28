<?php
namespace Framework\Syscrack\Game\Processes;

/**
 * Lewis Lancaster 2017
 *
 * Class Install
 *
 * @package Framework\Syscrack\Game\Processes
 */

use Framework\Exceptions\SyscrackException;
use Framework\Syscrack\Game\Structures\Process;
use Framework\Syscrack\Game\Utilities\TimeHelper;
use Framework\Syscrack\Game\Softwares;
use Framework\Syscrack\Game\Computer;
use Framework\Syscrack\Game\Internet;
use Framework\Syscrack\Game\Log;
use Flight;

class Install implements Process
{

    protected $softwares;

    protected $computer;

    protected $internet;

    protected $log;

    public function __construct()
    {

        $this->softwares = new Softwares();

        $this->computer = new Computer();

        $this->internet = new Internet();

        $this->log = new Log();
    }

    public function onCreation($timecompleted, $computerid, $userid, $process, array $data)
    {

        if( isset( $data['ipaddress'] ) == false )
        {

            return false;
        }

        if( isset( $data['softwareid'] ) == false )
        {

            return false;
        }

        if( $this->softwares->softwareExists( $data['softwareid'] ) == false )
        {

            return false;
        }
        else
        {

            if( $this->computer->hasSoftware( $this->internet->getComputer( $data['ipaddress'] )->computerid, $data['softwareid'] ) == false )
            {

                return false;
            }
            else
            {

                return true;
            }
        }
    }

    /**
     * Called when the process is completed
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

        if( isset( $data['softwareid'] ) == false )
        {

            throw new SyscrackException();
        }

        $this->softwares->installSoftware( $data['softwareid'], $userid );

        $this->computer->installSoftware( $this->internet->getComputer( $data['ipaddress'] ), $data['softwareid'] );

        $this->logInstall( $this->softwares->getDatabaseSoftware( $data['softwareid'] )->softwarename,
            $this->internet->getComputer( $data['ipaddress'] ),$this->computer->getComputer( $this->computer->getCurrentUserComputer() )->ipaddress );

        $this->logLocal( $this->softwares->getDatabaseSoftware( $data['softwareid'] )->softwarname,
            $this->computer->getCurrentUserComputer(), $data['ipaddress']);

        $this->redirectSuccess( $data['ipaddress'] );
    }

    /**
     * Gets the completion time
     *
     * @param $computerid
     *
     * @param $ipaddress
     *
     * @param $process
     *
     * @return int
     */

    public function getCompletionTime($computerid, $ipaddress, $process)
    {

        $future = new TimeHelper();

        return $future->getSecondsInFuture( 10 );
    }

    /**
     * Logs a login action to the computers log
     *
     * @param $computerid
     *
     * @param $ipaddress
     */

    private function logInstall( $softwarename, $computerid, $ipaddress )
    {

        $this->logToComputer('Installed file <' . $softwarename . '> on root', $computerid, $ipaddress );
    }

    /**
     * Logs to the computer
     *
     * @param $computerid
     *
     * @param $ipaddress
     */

    private function logLocal( $softwarename, $computerid, $ipaddress )
    {

        $this->logToComputer('Installed file <' . $softwarename . '> on ' . $ipaddress, $computerid, 'localhost' );
    }

    /**
     * Updates the computers log
     *
     * @param $message
     *
     * @param $computerid
     *
     * @param $ipaddress
     */

    private function logToComputer( $message, $computerid, $ipaddress )
    {

        $this->log->updateLog( $message, $computerid, $ipaddress );
    }

    /**
     * Redirects to the error page
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
     * Redirects to the success page
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
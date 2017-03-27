<?php
namespace Framework\Syscrack\Game\Processes;

/**
 * Lewis Lancaster 2017
 *
 * Class Login
 *
 * @package Framework\Syscrack\Game\Processes
 */

use Framework\Exceptions\SyscrackException;
use Framework\Syscrack\Game\Structures\Process;
use Framework\Syscrack\Game\Internet;
use Framework\Syscrack\Game\Log;
use Framework\Syscrack\Game\Computer;
use Flight;

class Login implements Process
{

    protected $internet;

    protected $log;

    protected $computer;

    public function __construct()
    {

        $this->internet = new Internet();

        $this->log = new Log();

        $this->computer = new Computer();
    }

    /**
     * Called when this process request is created
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

        return true;
    }

    /**
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
     *
     * @return mixed|void
     */

    public function onCompletion($timecompleted, $timestarted, $computerid, $userid, $process, array $data)
    {

        if( isset( $data['ipaddress'] ) == false )
        {

            throw new SyscrackException();
        }

        if( $this->internet->getCurrentConnectedAddress() == $data['ipaddress'] )
        {

            $this->redirectError('You are already logged into this computer');
        }
        else
        {

            $this->logAccess( $this->internet->getComputer( $data['ipaddress'] )->computerid, $this->computer->getComputer( $this->computer->getCurrentUserComputer() )->ipaddress );

            $this->logLocal( $this->computer->getComputer( $this->computer->getCurrentUserComputer() )->computerid, $data['ipaddress'] );

            $this->internet->setCurrentConnectedAddress( $data['ipaddress'] );

            $this->redirectSuccess( $data['ipaddress'] );
        }
    }

    /**
     * Gets the time of which to complete this process
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
     * Logs a login action to the computers log
     *
     * @param $computerid
     *
     * @param $ipaddress
     */

    private function logAccess( $computerid, $ipaddress )
    {

        $this->logToComputer('Logged in as root', $computerid, $ipaddress );
    }

    /**
     * Logs to the computer
     *
     * @param $computerid
     *
     * @param $ipaddress
     */

    private function logLocal( $computerid, $ipaddress )
    {

        $this->logToComputer('Logged into <' . $ipaddress . '> as root', $computerid, 'localhost' );
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
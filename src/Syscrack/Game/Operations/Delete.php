<?php
namespace Framework\Syscrack\Game\Operations;

/**
 * Lewis Lancaster 2017
 *
 * Class Logout
 *
 * @package Framework\Syscrack\Game\Operations
 */

use Framework\Application\Settings;
use Framework\Exceptions\SyscrackException;
use Framework\Syscrack\Game\Structures\Operation;
use Framework\Syscrack\Game\Internet;
use Framework\Syscrack\Game\Softwares;
use Framework\Syscrack\Game\Computer;
use Framework\Syscrack\Game\Log;
use Flight;

class Delete implements Operation
{

    /**
     * @var Internet
     */

    protected $internet;

    protected $computer;

    protected $softwares;

    protected $log;

    /**
     * Logout constructor.
     */

    public function __construct()
    {

        $this->internet = new Internet();

        $this->computer = new Computer();

        $this->softwares = new Softwares();

        $this->log = new Log();
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
     * @return mixed
     */

    public function onCreation($timecompleted, $computerid, $userid, $process, array $data)
    {

        if( isset( $data['ipaddress' ] ) == false )
        {

            return false;
        }

        if( isset( $data['softwareid'] ) == false )
        {

            return false;
        }

        if( $this->computer->hasSoftware( $this->internet->getComputer( $data['ipaddress'] )->computerid, $data['softwareid'] ) == false )
        {

            return false;
        }

        $softwareclass = $this->softwares->getSoftwareClassFromID( $data['softwareid'] );

        if( isset( $softwareclass->configuration()['removeable'] ) == false )
        {

            return true;
        }
        else
        {

            if( $softwareclass->configuration()['removeable'] == false )
            {

                return false;
            }
        }

        return true;
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
     */

    public function onCompletion($timecompleted, $timestarted, $computerid, $userid, $process, array $data)
    {

        if( isset( $data['ipaddress' ] ) == false )
        {

            throw new SyscrackException();
        }

        if( isset( $data['softwareid'] ) == false )
        {

            throw new SyscrackException();
        }

        $software = $this->softwares->getSoftware( $data['softwareid'] );

        $this->logDelete( $software->softwarename, $this->internet->getComputer( $data['ipaddress'] )->computerid, $this->computer->getComputer( $this->computer->getCurrentUserComputer() )->ipaddress );

        $this->logLocal( $software->softwarename, $data['ipaddress'] );

        $this->softwares->deleteSoftware( $data['softwareid'] );

        $this->computer->removeSoftware( $this->internet->getComputer( $data['ipaddress'] )->computerid, $data['softwareid'] );

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

    private function logDelete( $softwarename, $computerid, $ipaddress )
    {

        $this->logToComputer('Deleted file <' . $softwarename . '> on root', $computerid, $ipaddress );
    }

    /**
     * Logs to the local log
     *
     * @param $softwarename
     *
     * @param $ipaddress
     */

    private function logLocal( $softwarename, $ipaddress )
    {

        $this->logToComputer('Deleted file <' . $softwarename . '> on ' . $ipaddress, $this->computer->getComputer( $this->computer->getCurrentUserComputer() )->computerid, 'localhost' );
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
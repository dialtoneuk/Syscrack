<?php
namespace Framework\Syscrack\Game\Operations;

/**
 * Lewis Lancaster 2017
 *
 * Class Download
 *
 * @package Framework\Syscrack\Game\Operations
 */

use Framework\Exceptions\SyscrackException;
use Framework\Syscrack\Game\Log;
use Framework\Syscrack\Game\Structures\Operation;
use Framework\Syscrack\Game\Internet;
use Framework\Syscrack\Game\Computer;
use Framework\Syscrack\Game\Softwares;
use Flight;

class Download implements Operation
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

        if( $this->computer->hasSoftware( $this->internet->getComputer( $data['ipaddress' ] )->computerid, $data['softwareid'] ) == false )
        {

            return false;
        }

        $software = $this->softwares->getSoftware( $data['softwareid'] );

        $softwares = $this->computer->getComputerSoftware( $this->computer->getCurrentUserComputer() );

        foreach( $softwares as $value )
        {

            if( $value['type'] == $software->type )
            {

                if( $this->softwares->getSoftware( $value['softwareid'] )->softwarename == $software->softwarename )
                {

                    return false;
                }
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

        $softwareid = $this->softwares->copySoftware( $data['softwareid'], $this->computer->getCurrentUserComputer(), $userid );

        if( empty( $softwareid ) )
        {

            throw new SyscrackException();
        }

        $software = $this->softwares->getSoftware( $data['softwareid'] );

        if( $software == null )
        {

            throw new SyscrackException();
        }

        $this->computer->addSoftware( $this->computer->getCurrentUserComputer(), $softwareid, $software->type, $software->softwarename );

        $this->logDownload( $software->softwarename, $this->internet->getComputer( $data['ipaddress'] )->computerid, $this->computer->getComputer( $this->computer->getCurrentUserComputer() )->ipaddress );

        $this->logLocal( $software->softwarename, $data['ipaddress'] );

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

    private function logDownload( $softwarename, $computerid, $ipaddress )
    {

        $this->logToComputer('Downloaded file <' . $softwarename . '> on root', $computerid, $ipaddress );
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

        $this->logToComputer('Downloaded file <' . $softwarename . '> on ' . $ipaddress, $this->computer->getComputer( $this->computer->getCurrentUserComputer() )->computerid, 'localhost' );
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
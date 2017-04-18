<?php
namespace Framework\Syscrack\Game\Operations;

/**
 * Lewis Lancaster 2017
 *
 * Class Logout
 *
 * @package Framework\Syscrack\Game\Operations
 */

use Framework\Exceptions\SyscrackException;
use Framework\Syscrack\Game\Structures\Operation as Structure;
use Framework\Syscrack\Game\BaseClasses\Operation as BaseClass;
use Framework\Application\Settings;

class Delete extends BaseClass implements Structure
{

    /**
     * Delete constructor.
     */

    public function __construct()
    {

        parent::__construct();
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

        if( $this->checkData( $data ) == false )
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


    public function getCompletionSpeed($computerid, $process, $softwareid=null)
    {

        return $this->calculateProcessingTime( $computerid, Settings::getSetting('syscrack_cpu_type'), 5.5, $softwareid );
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

        $this->log('Deleted file <' . $softwarename . '> on root', $computerid, $ipaddress );
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

        $this->log('Deleted file <' . $softwarename . '> on ' . $ipaddress, $this->computer->getComputer( $this->computer->getCurrentUserComputer() )->computerid, 'localhost' );
    }

}
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
use Framework\Syscrack\Game\Structures\Operation as Structure;
use Framework\Syscrack\Game\BaseClasses\Operation as BaseClass;
use Framework\Application\Settings;

class Download extends BaseClass implements Structure
{

    /**
     * Download constructor.
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
     * Gets the completion speed
     *
     * @param $computerid
     *
     * @param $process
     *
     * @param null $softwareid
     *
     * @return int
     */

    public function getCompletionSpeed($computerid, $process, $softwareid=null)
    {

        if( $this->softwares->softwareExists( $softwareid ) == false )
        {

            throw new SyscrackException();
        }

        return $this->calculateProcessingTime( $computerid, Settings::getSetting('syscrack_download_type'), $this->softwares->getSoftware( $softwareid )->size / 10, $softwareid );
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

        $this->log('Downloaded file <' . $softwarename . '> on root', $computerid, $ipaddress );
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

        $this->log('Downloaded file <' . $softwarename . '> on ' . $ipaddress, $this->computer->getComputer( $this->computer->getCurrentUserComputer() )->computerid, 'localhost' );
    }
}
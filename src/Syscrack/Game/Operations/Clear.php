<?php
namespace Framework\Syscrack\Game\Operations;

/**
 * Lewis Lancaster 2017
 *
 * Class Clear
 *
 * @package Framework\Syscrack\Game\Operations
 */

use Framework\Exceptions\SyscrackException;
use Framework\Syscrack\Game\Structures\Operation as Structure;
use Framework\Syscrack\Game\Operation as BaseClass;

class Clear extends BaseClass implements Structure
{

    /**
     * Clear constructor.
     */

    public function __construct()
    {

        parent::__construct();
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

        if( $this->checkData( $data, ['ipaddress'] ) == false )
        {

            return false;
        }

        $computer = $this->internet->getComputer( $data['ipaddress'] );

        if( $this->computerlog->hasLog( $computer->computerid ) == false )
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

        if( $this->checkData( $data, ['ipaddress'] ) == false )
        {

            throw new SyscrackException();
        }

        $this->computerlog->saveLog( $this->internet->getComputer( $data['ipaddress'] )->computerid, [] );

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

    public function getCompletionSpeed($computerid, $ipaddress, $process)
    {

        return null;
    }
}
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

class Install implements Process
{

    public function onCreation($timecompleted, $computerid, $userid, $process, array $data)
    {

        if( isset( $data['ipaddress'] ) == false )
        {

            throw new SyscrackException();
        }

        die( $data['ipaddress'] );
    }

    public function onCompletion($timecompleted, $timestarted, $computerid, $userid, $process, array $data)
    {

        if( isset( $data['ipaddress'] ) == false )
        {

            throw new SyscrackException();
        }

    }

    public function getCompletionTime($computerid, $ipaddress, $process)
    {

        return null;
    }
}
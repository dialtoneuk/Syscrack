<?php
namespace Framework\Syscrack\Game\Structures;

/**
 * Lewis Lancaster 2017
 *
 * Interface Operation
 *
 * @package Framework\Syscrack\Game\Structures
 */


interface Operation
{

    /**
     * Returns the operations configuration
     *
     * @return array
     */

    public function configuration();

    /**
     * Called when a process is created
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

    public function onCreation( $timecompleted, $computerid, $userid, $process, array $data );

    /**
     * Called upon the completion of the task
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

    public function onCompletion( $timecompleted, $timestarted, $computerid, $userid, $process, array $data );

    /**
     * Returns the completion speed for this process
     *
     * @param $computerid
     *
     * @param $ipaddress
     *
     * @param $softwareid
     *
     * @return int
     */

    public function getCompletionSpeed($computerid, $ipaddress, $softwareid );


    /**
     * Returns the custom data for this operation
     *
     * @param $ipaddress
     *
     * @param $userid
     *
     * @return array
     */

    public function getCustomData( $ipaddress, $userid );

    /**
     * Called upon a post request to the operation
     *
     * @param $data
     *
     * @param $ipaddress
     *
     * @param $userid
     *
     * @return bool
     */

    public function onPost( $data, $ipaddress, $userid );
}
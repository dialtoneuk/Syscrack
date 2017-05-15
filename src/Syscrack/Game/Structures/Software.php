<?php
namespace Framework\Syscrack\Game\Structures;

/**
 * Lewis Lancaster 2017
 *
 * Interface Software
 *
 * @package Framework\Syscrack\Game\Structures
 */

interface Software
{

    /**
     * The configuration of this software
     *
     * @return array
     */

    public function configuration();

    /**
     * Called when a software is executed
     *
     * @param $softwareid
     *
     * @param $userid
     *
     * @param $computerid
     *
     * @return mixed
     */

    public function onExecuted( $softwareid, $userid, $computerid );

    /**
     * Called when this software is installed on a computer
     *
     * @param $softwareid
     *
     * @param $userid
     *
     * @param $comptuerid
     *
     * @return mixed
     */

    public function onInstalled( $softwareid, $userid, $comptuerid );

    /**
     * Called when the software is uninstalled
     *
     * @param $softwareid
     *
     * @param $userid
     *
     * @param $computerid
     *
     * @return mixed
     */

    public function onUninstalled( $softwareid, $userid, $computerid );

    /**
     * Called when the software collects ( virus type only )
     *
     * @param $softwareid
     *
     * @param $userid
     *
     * @param $computerid
     *
     * @param $timeran
     *
     * @return float
     */

    public function onCollect( $softwareid, $userid, $computerid, $timeran );

    /**
     * Gets the execute completion time ( only on execute and if executable is equal to true )
     *
     * @param $softwareid
     *
     * @param $computerid
     *
     * @return mixed|null
     */

    public function getExecuteCompletionTime( $softwareid, $computerid );

    /**
     * Gets the default size in megabytes of the software
     *
     * @return float
     */

    public function getDefaultSize();

    /**
     * Gets the default level of the software
     *
     * @return float
     */

    public function getDefaultLevel();
}
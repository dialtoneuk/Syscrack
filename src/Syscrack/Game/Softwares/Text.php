<?php
    namespace Framework\Syscrack\Game\Softwares;

/**
 * Lewis Lancaster 2017
 *
 * Class Text
 *
 * @package Framework\Syscrack\Game\Softwares
 */

use Framework\Syscrack\Game\BaseClasses\BaseSoftware;


class Text extends BaseSoftware
{

    /**
     * The configuration of this Structure
     *
     * @return array
     */

    public function configuration()
    {

        return array(
            'uniquename'    => 'text',
            'extension'     => '.txt',
            'type'          => 'text',
            'viewable'      => true,
            'removable'    => true,
            'installable'   => false,
            'executable'    => true,
            'keepdata'      => true
        );
    }

    /**
     * @param $softwareid
     * @param $userid
     * @param $computerid
     * @return mixed|null
     */

    public function onExecuted( $softwareid, $userid, $computerid )
    {

        return true;
    }

    /**
     * @param $softwareid
     * @param $userid
     * @param $computerid
     * @return mixed|null
     */

    public function onInstalled( $softwareid, $userid, $computerid )
    {

        return true;
    }

    /**
     * @param $softwareid
     * @param $userid
     * @param $computerid
     * @return mixed|null
     */

    public function onUninstalled($softwareid, $userid, $computerid)
    {

        return true;
    }

    /**
     * @param $softwareid
     * @param $userid
     * @param $computerid
     * @param $timeran
     * @return float
     */

    public function onCollect( $softwareid, $userid, $computerid, $timeran )
    {

        return 0.0;
    }

    /**
     * @param $softwareid
     * @param $computerid
     * @return mixed|null|void
     */

    public function getExecuteCompletionTime($softwareid, $computerid)
    {

        return null;
    }
}
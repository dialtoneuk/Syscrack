<?php
namespace Framework\Syscrack\Game\Operations;

/**
 * Lewis Lancaster 2017
 *
 * Class View
 *
 * @package Framework\Syscrack\Game\Operations
 */

use Framework\Exceptions\SyscrackException;
use Framework\Syscrack\Game\Structures\Operation as Structure;
use Framework\Syscrack\Game\Operation as BaseClass;

class View extends BaseClass implements Structure
{

    /**
     * View constructor.
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

        if( $this->softwares->hasData( $data['softwareid'] ) == false )
        {

            return false;
        }

        return true;
    }

    /**
     * Renders the view page
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

        if( $this->checkData( $data ) == false )
        {

            throw new SyscrackException();
        }

        if( $this->softwares->hasData( $data['softwareid'] ) == false )
        {

            throw new SyscrackException();
        }

        $this->getRender('operations/operations.view', array('softwareid' => $data['softwareid'], 'ipaddress' => $data['ipaddress'], 'data' => $this->softwares->getSoftwareData( $data['softwareid'] ) ) );
    }

    /**
     * Gets the completion time
     *
     * @param $computerid
     *
     * @param $process
     *
     * @param null $sofwareid
     *
     * @return null
     */

    public function getCompletionSpeed($computerid, $process, $sofwareid=null )
    {

        return null;
    }
}
<?php
namespace Framework\Syscrack\Game\Operations;

/**
 * Lewis Lancaster 2017
 *
 * Class ForceDelete
 *
 * @package Framework\Syscrack\Game\Operations
 */

use Framework\Application\Settings;
use Framework\Exceptions\SyscrackException;
use Framework\Syscrack\Game\BaseClasses\BaseOperation;
use Framework\Syscrack\Game\Viruses;

class ForceDelete extends BaseOperation
{

    /**
     * @var Viruses
     */

    protected static $viruses;

    /**
     * Delete constructor.
     */

    public function __construct()
    {

        if( isset( self::$viruses ) == false )
            self::$viruses = new Viruses();


        parent::__construct( true );
    }

    /**
     * Returns the configuration
     *
     * @return array
     */

    public function configuration()
    {

        return array(
            'allowsoftware'    => true,
            'allowlocal'        => true,
            'requiresoftware'  => true,
            'requireloggedin'   => true
        );
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
            return false;

        if( self::$user->isAdmin( $userid ) == false )
           return false;

        return false;
    }

    /**
     * @param $timecompleted
     * @param $timestarted
     * @param $computerid
     * @param $userid
     * @param $process
     * @param array $data
     * @return bool|string
     */

    public function onCompletion($timecompleted, $timestarted, $computerid, $userid, $process, array $data)
    {

        if( $this->checkData( $data ) == false )
            return false;

        if( self::$user->isAdmin( $userid ) == false )
            return false;

        $software = self::$software->getSoftware( $data['softwareid'] );
        self::$software->deleteSoftware( $software->softwareid );
        self::$computer->removeSoftware( $this->getComputerId( $data['ipaddress'] ), $software->softwareid );

        if( isset( $data['redirect'] ) == false )
            return true;
        else
            return( $data['redirect'] );
    }

    /**
     * Returns the completion time for this action
     *
     * @param $computerid
     *
     * @param $ipaddress
     *
     * @param null $softwareid
     *
     * @return int
     */

    public function getCompletionSpeed($computerid, $ipaddress, $softwareid=null)
    {

        return $this->calculateProcessingTime( $computerid, Settings::setting('syscrack_hardware_cpu_type'), 5.5, $softwareid );
    }
}
<?php
namespace Framework\Syscrack\Game\Operations;

/**
 * Lewis Lancaster 2017
 *
 * Class Bank
 *
 * @package Framework\Syscrack\Game\Operations
 */

use Framework\Application\Settings;
use Framework\Exceptions\SyscrackException;
use Framework\Syscrack\Game\BaseClasses\Operation as BaseClass;
use Framework\Syscrack\Game\Structures\Operation as Structure;

class Bank extends BaseClass implements Structure
{

    /**
     * View constructor.
     */

    public function __construct()
    {

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
            'allowsoftwares'    => false,
            'allowlocal'        => false,
            'requiresoftwares'  => false,
            'requireloggedin'   => false
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

        if( $this->checkData( $data, ['ipaddress'] ) == false )
        {

            return false;
        }

        $computer = $this->internet->getComputer( $data['ipaddress'] );

        if( $computer->type != Settings::getSetting('syscrack_bank_type') )
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

        if( $this->checkData( $data, ['ipaddress'] ) == false )
        {

            throw new SyscrackException();
        }

        ob_clean();

        $this->getRender('operations/operations.bank', array( 'ipaddress' => $data['ipaddress'], 'userid' => $userid ), true );
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
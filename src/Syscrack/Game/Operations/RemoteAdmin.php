<?php
namespace Framework\Syscrack\Game\Operations;

/**
 * Lewis Lancaster 2017
 *
 * Class RemoteAdmin
 *
 * @package Framework\Syscrack\Game\Operations
 */

use Framework\Application\Settings;
use Framework\Exceptions\SyscrackException;
use Framework\Syscrack\Game\BaseClasses\Operation as BaseClass;
use Framework\Syscrack\Game\Finance;
use Framework\Syscrack\Game\Structures\Operation as Structure;

class RemoteAdmin extends BaseClass implements Structure
{

    /**
     * @var Finance
     */

    protected $finance;

    /**
     * View constructor.
     */

    public function __construct()
    {

        parent::__construct( true );

        if( isset( $this->finance ) == false )
        {

            $this->finance = new Finance();
        }
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
            'requireloggedin'   => false,
            'allowpost'         => true,
            'postrequirements'  => [
                'action'
            ]
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

        if( $computer->type != Settings::getSetting('syscrack_computer_bank_type') )
        {

            return false;
        }

        if( $this->finance->hasCurrentActiveAccount() == false )
        {

            return false;
        }
        else
        {

            if( $this->finance->getByAccountNumber( $this->finance->getCurrentActiveAccount() )->computerid !== $computer->computerid )
            {

                return false;
            }
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

        $this->getRender('operations/operations.bank.adminaccount', array( 'ipaddress' => $data['ipaddress'], 'userid' => $userid, 'accountnumber' => $this->finance->getCurrentActiveAccount() ), true );
    }

    /**
     * Gets the completion time
     *
     * @param $computerid
     *
     * @param $ipaddress
     *
     * @param null $softwareid
     *
     * @return null
     */

    public function getCompletionSpeed($computerid, $ipaddress, $softwareid=null )
    {

        return null;
    }

    /**
     * Gets the custom data for this operation
     *
     * @param $ipaddress
     *
     * @param $userid
     *
     * @return array
     */

    public function getCustomData($ipaddress, $userid)
    {

        return array();
    }

    /**
     * Calls when the operation receives a post request
     *
     * @param $data
     *
     * @return bool
     */

    public function onPost( $data, $ipaddress, $userid )
    {

        return true;
    }
}
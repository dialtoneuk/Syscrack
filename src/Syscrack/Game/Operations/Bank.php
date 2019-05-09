<?php
namespace Framework\Syscrack\Game\Operations;

/**
 * Lewis Lancaster 2017
 *
 * Class Bank
 *
 * @package Framework\Syscrack\Game\Operations
 */

use Framework\Exceptions\SyscrackException;
use Framework\Syscrack\Game\BaseClasses\BaseOperation;
use Framework\Syscrack\Game\Finance;

class Bank extends BaseOperation
{

    /**
     * @var Finance
     */

    protected static $finance;

    /**
     * View constructor.
     */

    public function __construct()
    {

        if( isset( self::$finance ) == false )
            self::$finance = new Finance();

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
            'allowsoftware'    => false,
            'allowlocal'        => false,
            'requiresoftware'  => false,
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
            return false;


        $computer = self::$internet->getComputer( $data['ipaddress'] );

        if( self::$computer->isBank( $computer->computerid ) == false )
            return false;

        return true;
    }

    /**
     * @param $timecompleted
     * @param $timestarted
     * @param $computerid
     * @param $userid
     * @param $process
     * @param array $data
     * @return bool
     */

    public function onCompletion($timecompleted, $timestarted, $computerid, $userid, $process, array $data)
    {

        if( $this->checkData( $data, ['ipaddress'] ) == false )
            return false;


        if( self::$internet->ipExists( $data['ipaddress'] ) == false )
            return false;

        $this->render('operations/operations.bank', array( 'ipaddress' => $data['ipaddress'], 'userid' => $userid ), true );
        return null;
    }

    /**
     * @param $data
     * @param $ipaddress
     * @param $userid
     * @return bool
     */

    public function onPost( $data, $ipaddress, $userid )
    {

        $computer = self::$internet->getComputer( $ipaddress );

        if( $data['action'] == 'create' )
        {
            if( self::$finance->hasAccountAtComputer( $computer->computerid, $userid ) == true )
                return false;
            else
                self::$finance->createAccount( $computer->computerid, $userid );
        }
        elseif( $data['action'] == "delete" )
        {

            if( self::$finance->hasAccountAtComputer( $computer->computerid, $userid ) == false )
                return false;
            else
                self::$finance->removeAccount( $computer->computerid, $userid );
        }

        return null;
    }
}
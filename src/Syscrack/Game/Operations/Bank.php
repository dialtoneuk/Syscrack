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
use Framework\Syscrack\Game\BaseClasses\Operation as BaseClass;
use Framework\Syscrack\Game\Finance;
use Framework\Syscrack\Game\Structures\Operation as Structure;

class Bank extends BaseClass implements Structure
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
        {

            return false;
        }

        $computer = self::$internet->getComputer( $data['ipaddress'] );

        if( self::$computers->isBank( $computer->computerid ) == false )
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

        if( self::$internet->ipExists( $data['ipaddress'] ) == false )
        {

            $this->redirectError('Sorry, this ip address does not exist anymore', $this->getRedirect() );
        }

        $this->getRender('operations/operations.bank', array( 'ipaddress' => $data['ipaddress'], 'userid' => $userid ), true );
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
            {

                $this->redirectError('You already have an account at this bank', $this->getRedirect( $ipaddress ) . '/bank' );
            }

            self::$finance->createAccount( $computer->computerid, $userid );

            $this->redirectSuccess( $this->getRedirect( $ipaddress ) . '/bank' );
        }
        elseif( $data['action'] == "delete" )
        {

            if( self::$finance->hasAccountAtComputer( $computer->computerid, $userid ) == false )
            {

                $this->redirectError('You do not have a bank account at this bank', $this->getRedirect( $ipaddress ) . '/bank' );
            }

            self::$finance->removeAccount( $computer->computerid, $userid );

            $this->redirectSuccess( $this->getRedirect( $ipaddress ) . '/bank' );
        }

        return true;
    }
}
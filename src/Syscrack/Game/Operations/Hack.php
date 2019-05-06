<?php
namespace Framework\Syscrack\Game\Operations;

/**
 * Lewis Lancaster 2017
 *
 * Class Hack
 *
 * @package Framework\Syscrack\Game\Operations
 */

use Framework\Application\Settings;
use Framework\Exceptions\SyscrackException;
use Framework\Syscrack\Game\AddressDatabase;
use Framework\Syscrack\Game\BaseClasses\Operation as BaseClass;
use Framework\Syscrack\Game\Statistics;
use Framework\Syscrack\Game\Structures\Operation as Structure;

class Hack extends BaseClass implements Structure
{

    /**
     * @var AddressDatabase;
     */

    protected static $addressdatabase;

    /**
     * @var Statistics
     */

    protected static $statistics;

    /**
     * Hack constructor.
     */

    public function __construct()
    {

        if( isset( self::$addressdatabase ) == false )
            self::$addressdatabase = new AddressDatabase();


        if( isset( self::$statistics ) == false )
            self::$statistics = new Statistics();
        

        parent::__construct();
    }

    /**
     * The configuration of this operation
     */

    public function configuration()
    {

        return array(
            'allowsoftware'    => false,
            'allowlocal'        => false,
            'requiresoftware'  => false,
            'requireloggedin'   => false,
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

        if( self::$computers->getComputer( $computerid )->ipaddress == $data['ipaddress'] )
        {

            return false;
        }

        //$user = Container::getObject('session')->getSessionUser();

        if( self::$addressdatabase->hasAddress( $data['ipaddress'], $userid ) )
        {

            $this->redirectError('You have already hacked this address', $this->getRedirect( $data['ipaddress'] ) );
        }

        if( self::$computers->hasType( $computerid, Settings::getSetting('syscrack_software_cracker_type'), true ) == false )
        {

            return false;
        }

        $victimid = $this->getComputerId( $data['ipaddress'] );

        if( self::$computers->hasType( $victimid, Settings::getSetting('syscrack_software_hasher_type'), true ) == true )
        {

            if( $this->getHighestLevelSoftware( $victimid, Settings::getSetting('syscrack_software_hasher_type') )['level'] > $this->getHighestLevelSoftware( $computerid, Settings::getSetting('syscrack_software_cracker_type') )['level'] )
            {

                $this->redirectError('Your cracker is too weak', $this->getRedirect( $data['ipaddress'] ) );
            }
        }

        return true;
    }

    /**
     * @param $timecompleted
     * @param $timestarted
     * @param $computerid
     * @param $userid
     * @param $process
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

        self::$addressdatabase->addAddress( $data['ipaddress'],  $userid );

        if( Settings::getSetting('syscrack_statistics_enabled') == true )
        {

            self::$statistics->addStatistic('hacks');
        }

        if( isset( $data['redirect'] ) )
        {

            $this->redirectSuccess( $data['redirect'] );
        }
        else
        {

            $this->redirectSuccess( $this->getRedirect( $data['ipaddress'] ) );
        }
    }

    /**
     * Gets the completion speed of this action
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

        return $this->calculateProcessingTime( $computerid, Settings::getSetting('syscrack_hardware_cpu_type'), Settings::getSetting('syscrack_operations_hack_speed'), $softwareid );
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
     * Called upon a post request to this operation
     *
     * @param $data
     *
     * @param $ipaddress
     *
     * @param $userid
     *
     * @return bool
     */

    public function onPost($data, $ipaddress, $userid)
    {

        return true;
    }
}
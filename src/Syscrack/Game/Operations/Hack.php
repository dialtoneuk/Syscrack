<?php
namespace Framework\Syscrack\Game\Operations;

/**
 * Lewis Lancaster 2017
 *
 * Class Hack
 *
 * @package Framework\Syscrack\Game\Operations
 */

use Framework\Application\Container;
use Framework\Application\Settings;
use Framework\Exceptions\SyscrackException;
use Framework\Syscrack\Game\AddressDatabase;
use Framework\Syscrack\Game\BaseClasses\Operation as BaseClass;
use Framework\Syscrack\Game\Structures\Operation as Structure;

class Hack extends BaseClass implements Structure
{

    /**
     * @var AddressDatabase;
     */

    protected $addressdatabase;

    /**
     * Hack constructor.
     */

    public function __construct()
    {

        parent::__construct();
    }

    /**
     * The configuration of this operation
     */

    public function configuration()
    {

        return array(
            'allowsoftwares'    => false,
            'allowlocal'        => false,
            'requiresoftwares'  => false,
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

        if( $this->computer->getComputer( $this->computer->getCurrentUserComputer() )->ipaddress == $data['ipaddress'] )
        {

            return false;
        }

        $this->addressdatabase = new AddressDatabase( Container::getObject('session')->getSessionUser() );

        if( $this->addressdatabase->getComputerByIPAddress( $data['ipaddress' ] ) != null )
        {

            return false;
        }

        $userscomputer = $this->computer->getComputer( $this->computer->getCurrentUserComputer() );

        if( $this->computer->hasType( $userscomputer->computerid, Settings::getSetting('syscrack_cracker_type'), true ) == false )
        {

            return false;
        }

        $victimscomputer = $this->internet->getComputer( $data['ipaddress'] );

        if( $this->computer->hasType($victimscomputer->computerid, Settings::getSetting('syscrack_hasher_type'), true ) == true )
        {

            return false;
        }

        if( $this->getHighestLevelSoftware( $victimscomputer->computerid, Settings::getSetting('syscrack_hasher_type') ) > $this->getHighestLevelSoftware( $userscomputer->computerid, Settings::getSetting('syscrack_cracker_type') ) )
        {

            return false;
        }

        return true;
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
     */

    public function onCompletion($timecompleted, $timestarted, $computerid, $userid, $process, array $data)
    {

        if( $this->checkData( $data, ['ipaddress'] ) == false )
        {

            throw new SyscrackException();
        }

        $this->addressdatabase = new AddressDatabase( Container::getObject('session')->getSessionUser() );

        $this->addressdatabase->addComputer( array(
            'computerid'        => $this->internet->getComputer( $data['ipaddress'] )->computerid,
            'ipaddress'         => $data['ipaddress'],
            'timehacked'        => time()
        ));

        $this->addressdatabase->saveDatabase();

        if( isset( $data['redirect'] ) )
        {

            $this->redirectSuccess( null , $data['redirect'] );
        }
        else
        {

            $this->redirectSuccess( $data['ipaddress'] );
        }
    }

    /**
     * Gets the completion speed of this action
     *
     * @param $computerid
     *
     * @param $process
     *
     * @param null $softwareid
     *
     * @return int
     */

    public function getCompletionSpeed($computerid, $process, $softwareid=null)
    {

        return $this->calculateProcessingTime( $computerid, Settings::getSetting('syscrack_cpu_type'), Settings::getSetting('syscrack_hack_speed'), $softwareid );
    }
}
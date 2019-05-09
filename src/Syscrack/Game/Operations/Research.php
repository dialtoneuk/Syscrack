<?php
namespace Framework\Syscrack\Game\Operations;

/**
 * Lewis Lancaster 2017
 *
 * Class Research
 *
 * @package Framework\Syscrack\Game\Operations
 */

use Framework\Application\Settings;
use Framework\Application\Utilities\PostHelper;
use Framework\Syscrack\Game\BaseClasses\BaseOperation;
use Framework\Syscrack\Game\Finance;
use Framework\Syscrack\Game\Utilities\PageHelper;
use Framework\Syscrack\Game\Utilities\TimeHelper;

class Research extends BaseOperation
{

    /**
     * @var PageHelper
     */

    protected static $pagehelper;

    /**
     * @var Finance
     */

    protected static $finance;

    /**
     * View constructor.
     */

    public function __construct()
    {

        if( isset( self::$pagehelper ) == false )
            self::$pagehelper = new PageHelper();

        if( isset( self::$finance ) == false )
            self::$finance = new Finance();

        parent::__construct();
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
            'allowcustomdata'   => true,
            'localonly'         => true,
            'requiresoftware'  => false,
            'elevated'          => true
        );
    }

    /**
     * @param null $ipaddress
     * @return string
     */

    public function url($ipaddress = null)
    {

        if( $ipaddress == null )
            return( parent::url( $ipaddress ) );

        return('game/internet/' . @$ipaddress . '/');
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

        if( $this->checkData( $data, [ 'ipaddress'] ) == false )
            return false;

        if( $this->checkCustomData( $data, ['softwareid'] ) == false )
            return false;

        if ( PostHelper::checkForRequirements( ['accountnumber', 'name'] ) == false )
            return false;

        if( self::$computer->hasType( $computerid, Settings::setting('syscrack_software_research_type'), true ) == false )
            return false;

        if( self::$software->softwareExists( $data['custom']['softwareid'] ) == false )
            return false;

        $software = self::$software->getSoftware( $data['custom']['softwareid'] );
        $price = Settings::setting('syscrack_research_price_multiplier') * @$software->level;

        if ( self::$finance->accountNumberExists( PostHelper::getPostData('accountnumber' ) ) == false )
            return false;

        $account = self::$finance->getByAccountNumber( PostHelper::getPostData('accountnumber') );

        if ( self::$finance->canAfford( $account->computerid, $account->userid, $price ) == false )
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
     * @return bool|mixed
     */

    public function onCompletion($timecompleted, $timestarted, $computerid, $userid, $process, array $data)
    {

        $software = self::$software->getSoftware( $data['custom']['softwareid'] );
        $price = Settings::setting('syscrack_research_price_multiplier') * $software->level;
        $account = self::$finance->getByAccountNumber( $data['custom']['accountnumber'] );

        self::$finance->withdraw( $account->computerid, $account->userid, $price );

        $newsoftware = self::$software->createSoftware( self::$software->getSoftwareNameFromSoftwareID(
            $data['custom']['softwareid'] ),
            $userid,
            $computerid,
            $data['custom']['name'],
            $software->level + Settings::setting('syscrack_research_increase'),
            $software->size * Settings::setting('syscrack_research_size_multiplyer'),
            json_decode( $software->data, true )
        );

        self::$computer->addSoftware( $computerid, $newsoftware, $software->type);

        if( isset( $data['redirect'] ) == false )
            return true;
        else
            return( $data['redirect'] );
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

        return array(
            'accountnumber' => PostHelper::getPostData('accountnumber', true ),
            'name' => PostHelper::getPostData('name', true ),
            'softwareid' => @PostHelper::getPostData('softwareid', true )
        );
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

        $research = self::$pagehelper->getInstalledType('research');

        return ( TimeHelper::getSecondsInFuture( Settings::setting("syscrack_operations_research_speed") / $research['level'] ) );
    }
}
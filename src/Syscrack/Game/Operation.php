<?php
namespace Framework\Syscrack\Game;

/**
 * Lewis Lancaster 2017
 *
 * Class Operation
 *
 * @package Framework\Syscrack\Game
 */

use Framework\Exceptions\SyscrackException;
use Framework\Syscrack\Game\Structures\Operation as Structure;
use Framework\Application\Settings;
use Flight;
use Framework\Syscrack\Game\Utilities\TimeHelper;

class Operation
{

    /**
     * @var Log
     */

    protected $computerlog;

    /**
     * @var Softwares
     */

    public $softwares;

    /**
     * @var Computer
     */

    public $computer;

    /**
     * @var Internet
     */

    public $internet;

    /**
     * @var Hardware
     */

    public $hardware;

    /**
     * Operation constructor.
     */

    public function __construct( $createclasses = true )
    {

        $this->computerlog = new Log();

        if( $createclasses )
        {

            $this->softwares = new Softwares();

            $this->computer = new Computer();

            $this->internet = new Internet();

            $this->hardware = new Hardware();
        }
    }

    /**
     * Checks the data given to the operation and returns false is a requirement isn't set
     *
     * @param array $data
     *
     * @param array $requirements
     *
     * @return bool
     */

    public function checkData( array $data, array $requirements = ['softwareid','ipaddress'] )
    {

        foreach( $requirements as $requirement )
        {

            if( isset( $data[ $requirement ] ) == false )
            {

                return false;
            }

            return true;
        }
    }

    /**
     * Adds a log message to a computer
     *
     * @param $message
     *
     * @param $computerid
     *
     * @param $ipaddress
     */

    public function log( $message, $computerid, $ipaddress )
    {

        $this->computerlog->updateLog( $message, $computerid, $ipaddress );
    }

    /**
     * Renders a page
     *
     * @param $file
     *
     * @param array|null $array
     */

    public function getRender( $file, array $array = null  )
    {

        Flight::render( Settings::getSetting('syscrack_view_location') . $file, $array);
    }

    /**
     * Calculates the processing time for an action using the algorithm
     *
     * @param $computerid
     *
     * @param string $hardwaretype
     *
     * @param float $speedness
     *
     * @param null $softwareid
     *
     * @return int
     */

    public function calculateProcessingTime( $computerid, $hardwaretype="cpu", $speedness=5.5, $softwareid=null )
    {

        if( $this->hardware->hasHardwareType( $computerid, $hardwaretype ) == false )
        {

            return TimeHelper::getSecondsInFuture( Settings::getSetting('syscrack_default_processingtime') );
        }

        if( $softwareid !== null )
        {

            if( $this->softwares->softwareExists( $softwareid ) == false )
            {

                throw new SyscrackException();
            }

            $hardware = $this->hardware->getHardwareType( $computerid, $hardwaretype );

            $software = $this->softwares->getSoftware( $softwareid );

            return TimeHelper::getSecondsInFuture( floor( sqrt( $hardware['value'] / $software->level / ( $hardware['value'] * Settings::getSetting('syscrack_global_speed' ) * $speedness ) ) ) );
        }

        $hardware = $this->hardware->getHardwareType( $computerid, $hardwaretype );

        return TimeHelper::getSecondsInFuture( floor( sqrt( $hardware['value'] / $speedness / ( $hardware['value'] * Settings::getSetting('syscrack_global_speed' ) ) ) ) );
    }

    /**
     * Redirects the user to an error page
     *
     * @param string $message
     *
     * @param string $ipaddress
     */

    public function redirectError( $message='', $ipaddress='' )
    {

        if( $ipaddress !== '' )
        {

            Flight::redirect('/game/internet/' . $ipaddress . "?error=" . $message ); exit;
        }

        Flight::redirect('/game/internet/?error=' . $message ); exit;
    }

    /**
     * Redirects the user to a success page
     *
     * @param string $ipaddress
     */

    public function redirectSuccess( $ipaddress='' )
    {

        if( $ipaddress !== '' )
        {

            Flight::redirect('/game/internet/' . $ipaddress . "?success" ); exit;
        }

        Flight::redirect('/game/internet/?success'); exit;
    }
}
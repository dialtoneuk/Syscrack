<?php
namespace Framework\Syscrack\Game\BaseClasses;

/**
 * Lewis Lancaster 2017
 *
 * Class Operation
 *
 * @package Framework\Syscrack\Game
 */

use Flight;
use Framework\Application\Settings;
use Framework\Application\Utilities\ArrayHelper;
use Framework\Exceptions\SyscrackException;
use Framework\Syscrack\Game\Computer;
use Framework\Syscrack\Game\Hardware;
use Framework\Syscrack\Game\Internet;
use Framework\Syscrack\Game\Log;
use Framework\Syscrack\Game\Softwares;
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

        if( $createclasses )
        {

            $this->computerlog = new Log();

            $this->softwares = new Softwares();

            $this->computer = new Computer();

            $this->internet = new Internet();

            $this->hardware = new Hardware();
        }
    }

    /**
     * Gets the configuration of this operation
     */

    public function configuration()
    {

        return array(
            'allowsoftwares'    => true,
            'allowlocal'        => true,
        );
    }

    /**
     * Gets the highest level of software on the users computer
     *
     * @param $computerid
     *
     * @param null $type
     *
     * @return array|null
     */

    public function getHighestLevelSoftware( $computerid, $type=null )
    {

        if( $type == null )
        {

            $type = Settings::getSetting('syscrack_cracker_type');
        }

        $softwares = $this->computer->getComputerSoftware( $computerid );

        if( empty( $softwares ) )
        {

            return null;
        }

        $results = [];

        foreach( $softwares as $key=>$value )
        {

            if( $value['type'] == $type )
            {

                if( $value['installed'] == true )
                {

                    $results[] = $this->softwares->getSoftware( $value['softwareid'] );
                }
            }
        }

        if( empty( $results ) )
        {

            return null;
        }

        $results = ArrayHelper::sortArray( $results, 'level' );

        if( is_array( $results ) == false )
        {

            return (array)$results;
        }

        return (array)$results[0];
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

    public function redirectError( $message = null, $ipaddress=null, $path = null)
    {

        if( $path === null )
        {

            $path = Settings::getSetting('syscrack_game_page');
        }

        if( $ipaddress !== null )
        {

            Flight::redirect('/' . $path . '/' . Settings::getSetting('syscrack_internet_page') . '/' . $ipaddress . '/?error=' . $message);
            exit;
        }

        Flight::redirect( '/' . $path . '/?error=' . $message);
        exit;
    }

    /**
     * Redirects the user to a success page
     *
     * @param string $ipaddress
     */

    public function redirectSuccess($ipaddress = null, $path = null )
    {

        if( $path === null )
        {

            $path = Settings::getSetting('syscrack_game_page');
        }

        if( $ipaddress != null )
        {

            Flight::redirect('/' . $path . '/' . Settings::getSetting('syscrack_internet_page') . '/' . $ipaddress . '/?success');

            exit;
        }

        Flight::redirect( '/' . $path . '/?success');

        exit;
    }
}
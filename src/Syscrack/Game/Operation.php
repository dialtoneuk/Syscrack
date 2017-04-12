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
     * Operation constructor.
     */

    public function __construct( $createclasses = true )
    {

        $class = get_parent_class( $this );

        if( $class instanceof Structure == false )
        {

            throw new SyscrackException('Operation class can only extend classes of which have the operation structure');
        }

        $this->computerlog = new Log();

        if( $createclasses )
        {

            $this->softwares = new Softwares();

            $this->computer = new Computer();

            $this->internet = new Internet();
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
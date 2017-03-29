<?php
namespace Framework\Syscrack\Game\Operations;

/**
 * Lewis Lancaster 2017
 *
 * Class View
 *
 * @package Framework\Syscrack\Game\Operations
 */

use Framework\Exceptions\SyscrackException;
use Framework\Syscrack\Game\Structures\Operation;
use Framework\Syscrack\Game\Internet;
use Framework\Syscrack\Game\Softwares;
use Framework\Application\Settings;
use Flight;

class View implements Operation
{

    /**
     * @var Internet
     */

    protected $internet;

    protected $softwares;

    /**
     * Logout constructor.
     */

    public function __construct()
    {

        $this->internet = new Internet();

        $this->softwares = new Softwares();
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

        if( isset( $data['softwareid'] ) == false )
        {

            return false;
        }

        if( isset( $data['ipaddress'] ) == false )
        {

            return false;
        }

        if( $this->softwares->softwareExists( $data['softwareid' ] ) == false )
        {

            return false;
        }

        if( $this->softwares->hasData( $data['softwareid'] ) == false )
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

        if( isset( $data['softwareid'] ) == false )
        {

            throw new SyscrackException();
        }

        if( isset( $data['ipaddress'] ) == false )
        {

            throw new SyscrackException();
        }

        $this->getRender('page.game.view', array('softwareid' => $data['softwareid'], 'ipaddress' => $data['ipaddress'], 'data' => $this->softwares->getSoftwareData( $data['softwareid'] ) ) );
    }

    /**
     * Gets the completion time
     *
     * @param $computerid
     *
     * @param $ipaddress
     *
     * @param $process
     *
     * @return null
     */

    public function getCompletionTime($computerid, $ipaddress, $process)
    {

        return null;
    }

    /**
     * Redirects the user to an error page
     *
     * @param string $message
     *
     * @param string $ipaddress
     */

    private function redirectError( $message='', $ipaddress='' )
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

    private function redirectSuccess( $ipaddress='' )
    {

        if( $ipaddress !== '' )
        {

            Flight::redirect('/game/internet/' . $ipaddress . "?success" ); exit;
        }

        Flight::redirect('/game/internet/?success'); exit;
    }

    /**
     * Renders a page
     *
     * @param $file
     *
     * @param array|null $array
     */

    private function getRender( $file, array $array = null  )
    {

        Flight::render( Settings::getSetting('syscrack_view_location') . $file, $array);
    }

}
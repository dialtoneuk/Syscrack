<?php
namespace Framework\Syscrack\Game\Operations;

/**
 * Lewis Lancaster 2017
 *
 * Class Login
 *
 * @package Framework\Syscrack\Game\Operations
 */

use Framework\Exceptions\SyscrackException;
use Framework\Syscrack\Game\BaseClasses\Operation as BaseClass;
use Framework\Syscrack\Game\Structures\Operation as Structure;

class Login extends BaseClass implements Structure
{

    /**
     * Login constructor.
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
            'allowlocal'        => false
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
     * @return bool
     */

    public function onCreation($timecompleted, $computerid, $userid, $process, array $data)
    {

        if( $this->checkData( $data, ['ipaddress'] ) == false )
        {

            return false;
        }

        if( $this->computer->getComputer( $this->computer->getCurrentUserComputer() )->ipaddress == $data['ipaddress'] )
        {

            $this->redirectError('Logging into your self is dangerous... do you want to break the space time continuum?', $this->getRedirect( $data['ipaddress'] ) ); exit;
        }
        else
        {

            return true;
        }
    }

    /**
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
     *
     * @return mixed|void
     */

    public function onCompletion($timecompleted, $timestarted, $computerid, $userid, $process, array $data)
    {

        if( $this->checkData( $data, ['ipaddress'] ) == false )
        {

            throw new SyscrackException();
        }

        if( $this->internet->hasCurrentConnection() )
        {

            if( $this->internet->getCurrentConnectedAddress() == $data['ipaddress'] )
            {

                $this->redirectError('You are already logged into this computer', $this->getRedirect( $data['ipaddress'] ) );
            }
            else
            {

                $this->logAccess( $this->internet->getComputer( $data['ipaddress'] )->computerid, $this->computer->getComputer( $this->computer->getCurrentUserComputer() )->ipaddress );

                $this->logLocal( $this->computer->getComputer( $this->computer->getCurrentUserComputer() )->computerid, $data['ipaddress'] );

                $this->internet->setCurrentConnectedAddress( $data['ipaddress'] );

                if( isset( $data['redirect'] ) )
                {

                    $this->redirectSuccess( $data['redirect'] );
                }
                else
                {

                    $this->redirectSuccess( $this->getRedirect( $data['ipaddress'] ) );
                }
            }
        }
        else
        {

            $this->logAccess( $this->internet->getComputer( $data['ipaddress'] )->computerid, $this->computer->getComputer( $this->computer->getCurrentUserComputer() )->ipaddress );

            $this->logLocal( $this->computer->getComputer( $this->computer->getCurrentUserComputer() )->computerid, $data['ipaddress'] );

            $this->internet->setCurrentConnectedAddress( $data['ipaddress'] );

            if( isset( $data['redirect'] ) )
            {

                $this->redirectSuccess( $data['redirect'] );
            }
            else
            {

                $this->redirectSuccess( $this->getRedirect( $data['ipaddress'] ) );
            }
        }
    }

    /**
     * Gets the time of which to complete this process
     *
     * @param $computerid
     *
     * @param $ipaddress
     *
     * @param $softwareid
     *
     * @return null
     */

    public function getCompletionSpeed($computerid, $ipaddress, $sofwareid=null)
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

    /**
     * Logs a login action to the computers log
     *
     * @param $computerid
     *
     * @param $ipaddress
     */

    private function logAccess( $computerid, $ipaddress )
    {

        $this->logToComputer('Logged in as root', $computerid, $ipaddress );
    }

    /**
     * Logs to the computer
     *
     * @param $computerid
     *
     * @param $ipaddress
     */

    private function logLocal( $computerid, $ipaddress )
    {

        $this->logToComputer('Logged into <' . $ipaddress . '> as root', $computerid, 'localhost' );
    }
}
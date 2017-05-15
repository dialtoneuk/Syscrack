<?php
namespace Framework\Syscrack\Game\Operations;

/**
 * Lewis Lancaster 2017
 *
 * Class Install
 *
 * @package Framework\Syscrack\Game\Operations
 */

use Framework\Application\Settings;
use Framework\Exceptions\SyscrackException;
use Framework\Syscrack\Game\BaseClasses\Operation as BaseClass;
use Framework\Syscrack\Game\Structures\Operation as Structure;
use Framework\Syscrack\Game\Viruses;

class Install extends BaseClass implements Structure
{

    /**
     * @var Viruses
     */

    protected $viruses;

    /**
     * Install constructor.
     */

    public function __construct()
    {

        parent::__construct();

        $this->viruses = new Viruses();
    }

    /**
     * Returns the configuration
     *
     * @return array
     */

    public function configuration()
    {

        return array(
            'allowsoftwares'    => true,
            'allowlocal'        => true,
            'requiresoftwares'  => true,
            'requireloggedin'   => true
        );
    }

    /**
     * Called when a process with the corresponding operation is created
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
     *
     * @return bool
     */

    public function onCreation($timecompleted, $computerid, $userid, $process, array $data)
    {

        if( $this->checkData( $data ) == false )
        {

            return false;
        }

        if( $this->softwares->canInstall( $data['softwareid'] ) == false )
        {

            return false;
        }

        if( $this->viruses->isVirus( $data['softwareid'] ) )
        {

            $software = $this->softwares->getSoftware( $data['softwareid'] );

            if( $this->viruses->virusAlreadyInstalled( $software->uniquename, $this->getComputerId( $data['ipaddress']), $userid ) )
            {

                return false;
            }

            if( $this->getComputerId( $data['ipaddress'] ) == $computerid )
            {

                return false;
            }
        }

        return true;
    }

    /**
     * Called when the process is completed
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

        if( $this->checkData( $data ) == false )
        {

            throw new SyscrackException();
        }

        if( $this->softwares->softwareExists( $data['softwareid'] ) == false )
        {

            $this->redirectError('Sorry, it looks like this software might have been deleted');
        }

        $this->softwares->installSoftware( $data['softwareid'], $userid );

        $this->computer->installSoftware( $this->internet->getComputer( $data['ipaddress'] )->computerid, $data['softwareid'] );

        $this->logInstall( $this->softwares->getSoftware( $data['softwareid'] )->softwarename,
            $this->internet->getComputer( $data['ipaddress'] )->computerid,$this->computer->getComputer( $this->computer->getCurrentUserComputer() )->ipaddress );

        $this->logLocal( $this->softwares->getSoftware( $data['softwareid'] )->softwarename,
            $this->computer->getCurrentUserComputer(), $data['ipaddress']);

        $this->softwares->executeSoftwareMethod( $this->softwares->getSoftwareNameFromSoftwareID( $data['softwareid'] ), 'onInstalled', array(
            'softwareid'    => $data['softwareid'],
            'userid'        => $userid,
            'computerid'    => $this->internet->getComputer( $data['ipaddress'] )->computerid
        ));

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
     *
     *
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

        return $this->calculateProcessingTime( $computerid, Settings::getSetting('syscrack_hardware_cpu_type'), 20, $softwareid );
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

    private function logInstall( $softwarename, $computerid, $ipaddress )
    {

        if( $this->computer->getCurrentUserComputer() == $computerid )
        {

            return;
        }

        $this->logToComputer('Installed file (' . $softwarename . ') on root', $computerid, $ipaddress );
    }

    /**
     * Logs to the computer
     *
     * @param $computerid
     *
     * @param $ipaddress
     */

    private function logLocal( $softwarename, $computerid, $ipaddress )
    {

        $this->logToComputer('Installed file (' . $softwarename . ') on <' . $ipaddress . '>', $computerid, 'localhost' );
    }
}
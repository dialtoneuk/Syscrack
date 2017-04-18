<?php
namespace Framework\Syscrack\Game\Operations;

/**
 * Lewis Lancaster 2017
 *
 * Class Install
 *
 * @package Framework\Syscrack\Game\Operations
 */

use Framework\Application\Container;
use Framework\Exceptions\SyscrackException;
use Framework\Syscrack\Game\Utilities\TimeHelper;
use Framework\Syscrack\Game\Structures\Operation as Structure;
use Framework\Syscrack\Game\BaseClasses\Operation as BaseClass;
use Framework\Syscrack\Game\Viruses;
use Framework\Application\Settings;

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

        if( $this->softwares->softwareExists( $data['softwareid'] ) == false )
        {

            return false;
        }
        else
        {

            if( $this->computer->hasSoftware( $this->internet->getComputer( $data['ipaddress'] )->computerid, $data['softwareid'] ) == false )
            {

                return false;
            }
            else
            {

                if( $this->softwares->canInstall( $data['softwareid'] ) == false )
                {

                    return false;
                }
                else
                {

                    if( $this->viruses->isVirus( $data['softwareid'] ) );
                    {

                        if( $this->viruses->hasVirusesOnComputer( $this->internet->getComputer( $data['ipaddress'] )->computerid, Container::getObject('session')->getSessionUser() ) )
                        {

                            if( $this->viruses->virusAlreadyInstalled( $this->softwares->getSoftwareClassFromID( $data['softwareid'] )->configuration()['uniquename'],
                                $this->internet->getComputer( $data['ipaddress'] )->computerid, Container::getObject('session')->getSessionUser() ))
                            {

                                return false;
                            }
                        }
                    }
                }

                return true;
            }
        }
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

        $this->softwares->installSoftware( $data['softwareid'], $userid );

        $this->computer->installSoftware( $this->internet->getComputer( $data['ipaddress'] )->computerid, $data['softwareid'] );

        $this->logInstall( $this->softwares->getSoftware( $data['softwareid'] )->softwarename,
            $this->internet->getComputer( $data['ipaddress'] )->computerid,$this->computer->getComputer( $this->computer->getCurrentUserComputer() )->ipaddress );

        $this->logLocal( $this->softwares->getSoftware( $data['softwareid'] )->softwarename,
            $this->computer->getCurrentUserComputer(), $data['ipaddress']);

        $this->softwares->executeSoftwareMethod( $this->softwares->getSoftwareClassFromID( $data['softwareid'] ), 'onInstall', array(
            'softwareid'    => $data['softwareid'],
            'userid'        => $userid,
            'computerid'    => $this->internet->getComputer( $data['ipaddress'] )->computerid
        ));

        $this->redirectSuccess( $data['ipaddress'] );
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

        return $this->calculateProcessingTime( $computerid, Settings::getSetting('syscrack_cpu_type'), 5.5, $softwareid );
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

        $this->log('Installed file <' . $softwarename . '> on root', $computerid, $ipaddress );
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

        $this->log('Installed file <' . $softwarename . '> on ' . $ipaddress, $computerid, 'localhost' );
    }
}
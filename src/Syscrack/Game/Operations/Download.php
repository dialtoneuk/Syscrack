<?php
namespace Framework\Syscrack\Game\Operations;

/**
 * Lewis Lancaster 2017
 *
 * Class Download
 *
 * @package Framework\Syscrack\Game\Operations
 */

use Framework\Application\Settings;
use Framework\Exceptions\SyscrackException;
use Framework\Syscrack\Game\BaseClasses\Operation as BaseClass;
use Framework\Syscrack\Game\Structures\Operation as Structure;
use Framework\Syscrack\Game\Viruses;

class Download extends BaseClass implements Structure
{

    /**
     * @var Viruses;
     */

    protected $viruses;

    /**
     * Download constructor.
     */

    public function __construct()
    {

        parent::__construct();

        if( isset( $this->viruses ) == false )
        {

            $this->viruses = new Viruses();
        }
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
            'allowlocal'        => false,
            'requiresoftwares'  => true,
            'requireloggedin'   => true
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

        if( $this->checkData( $data ) == false )
        {

            return false;
        }

        if( $this->computers->hasSoftware( $this->internet->getComputer( $data['ipaddress' ] )->computerid, $data['softwareid'] ) == false )
        {

            return false;
        }

        $software = $this->softwares->getSoftware( $data['softwareid'] );

        if( $this->hasSpace( $this->computers->getCurrentUserComputer(), $software->size ) == false )
        {

            $this->redirectError('Sorry, you dont have the free space for this download.', $this->getRedirect( $data['ipaddress'] ) );

            return false;
        }

        if( $this->viruses->isVirus( $software->softwareid ) )
        {

            if( $this->softwares->isInstalled( $software->softwareid, $this->getComputerId( $data['ipaddress'] ) ) )
            {

                return false;
            }
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

        if( $this->checkData( $data ) == false )
        {

            throw new SyscrackException();
        }

        if( $this->internet->ipExists( $data['ipaddress'] ) == false )
        {

            $this->redirectError('Sorry, this ip address does not exist anymore', $this->getRedirect() );
        }

        if( $this->softwares->softwareExists( $data['softwareid'] ) == false )
        {

            $this->redirectError('Sorry, it looks like this software might have been deleted', $this->getRedirect( $data['ipaddress'] ) );
        }

        $software = $this->softwares->getSoftware( $data['softwareid'] );

        if( $this->softwares->hasData( $software->softwareid ) == true && $this->softwares->keepData( $software->softwareid ) )
        {

            $softwaredata = $this->softwares->getSoftwareData( $software->softwareid );

            if( $this->softwares->checkSoftwareData( $software->softwareid, ['allowanondownloads'] ) == true )
            {

                unset( $softwaredata['allowanondownloads'] );
            }

            if( $this->softwares->checkSoftwareData( $software->softwareid, ['editable'] ) == true )
            {

                unset( $softwaredata['editable'] );
            }

            $new_softwareid = $this->softwares->copySoftware( $software->softwareid, $computerid, $userid, false, $softwaredata );
        }
        else
        {

            $new_softwareid = $this->softwares->copySoftware( $software->softwareid, $computerid, $userid );
        }

        $this->computers->addSoftware( $computerid, $new_softwareid, $software->type );

        if( $this->computers->hasSoftware( $computerid, $new_softwareid ) == false )
        {

            throw new SyscrackException();
        }

        $this->logDownload( $software->softwarename, $this->getComputerId( $data['ipaddress'] ), $this->computers->getComputer( $computerid )->ipaddress );

        $this->logLocal( $software->softwarename, $data['ipaddress'] );

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
     * Gets the completion speed
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

        if( $this->softwares->softwareExists( $softwareid ) == false )
        {

            throw new SyscrackException();
        }

        return $this->calculateProcessingTime( $computerid, Settings::getSetting('syscrack_hardware_download_type'), $this->softwares->getSoftware( $softwareid )->size / 5, $softwareid );
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

    private function logDownload( $softwarename, $computerid, $ipaddress )
    {

        $this->logToComputer('Downloaded file (' . $softwarename . ') on root', $computerid, $ipaddress );
    }

    /**
     * Logs to the local log
     *
     * @param $softwarename
     *
     * @param $ipaddress
     */

    private function logLocal( $softwarename, $ipaddress )
    {

        $this->logToComputer('Downloaded file (' . $softwarename . ') on <' . $ipaddress . '>', $this->computers->getComputer( $this->computers->getCurrentUserComputer() )->computerid, 'localhost' );
    }
}
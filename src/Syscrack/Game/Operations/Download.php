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

        if( $this->computer->hasSoftware( $this->internet->getComputer( $data['ipaddress' ] )->computerid, $data['softwareid'] ) == false )
        {

            return false;
        }

        $software = $this->softwares->getSoftware( $data['softwareid'] );

        if( $this->viruses->isVirus( $software->softwareid ) )
        {

            if( $this->softwares->isInstalled( $software->softwareid, $this->internet->getComputer( $data['ipaddress'] )->computerid ) )
            {

                return false;
            }
        }

        if( $this->computer->getSoftwareByName( $computerid, $software->softwarename, false ) !== null )
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

        if( $this->checkData( $data ) == false )
        {

            throw new SyscrackException();
        }

        if( $this->softwares->softwareExists( $data['softwareid'] ) == false )
        {

            $this->redirectError('Sorry, it looks like this software might have been deleted');
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

        $this->computer->addSoftware( $computerid, $new_softwareid, $software->type, $software->softwarename );

        if( $this->computer->hasSoftware( $computerid, $new_softwareid ) == false )
        {

            throw new SyscrackException();
        }

        $this->logDownload( $software->softwarename, $this->getComputerId( $data['ipaddress'] ), $this->computer->getComputer( $computerid )->ipaddress );

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
     * @param $process
     *
     * @param null $softwareid
     *
     * @return int
     */

    public function getCompletionSpeed($computerid, $process, $softwareid=null)
    {

        if( $this->softwares->softwareExists( $softwareid ) == false )
        {

            throw new SyscrackException();
        }

        return $this->calculateProcessingTime( $computerid, Settings::getSetting('syscrack_download_type'), $this->softwares->getSoftware( $softwareid )->size / 5, $softwareid );
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

        $this->logToComputer('Downloaded file (' . $softwarename . ') on <' . $ipaddress . '>', $this->computer->getComputer( $this->computer->getCurrentUserComputer() )->computerid, 'localhost' );
    }
}
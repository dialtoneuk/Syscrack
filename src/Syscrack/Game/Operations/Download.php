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
     * @var Viruses
     */

    protected static $viruses;

    /**
     * Download constructor.
     */

    public function __construct()
    {

        if( isset( self::$viruses ) == false )
            self::$viruses = new Viruses();


        parent::__construct( true );
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
            'allowlocal'        => false,
            'requiresoftware'  => true,
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

        if( self::$computers->hasSoftware( self::$internet->getComputer( $data['ipaddress' ] )->computerid, $data['softwareid'] ) == false )
        {

            return false;
        }

        $software = self::$software->getSoftware( $data['softwareid'] );

        if( $this->hasSpace( self::$computers->getCurrentUserComputer(), $software->size ) == false )
        {

            $this->redirectError('Sorry, you dont have the free space for this download.', $this->getRedirect( $data['ipaddress'] ) );

            return false;
        }

        if( self::$viruses->isVirus( $software->softwareid ) )
        {

            if( self::$software->isInstalled( $software->softwareid, $this->getComputerId( $data['ipaddress'] ) ) )
            {

                return false;
            }
        }

        return true;
    }

    /**
     * @param $timecompleted
     * @param $timestarted
     * @param $computerid
     * @param $userid
     * @param $process
     * @param array $data
     */

    public function onCompletion($timecompleted, $timestarted, $computerid, $userid, $process, array $data)
    {

        if( $this->checkData( $data ) == false )
        {

            throw new SyscrackException();
        }

        if( self::$internet->ipExists( $data['ipaddress'] ) == false )
        {

            $this->redirectError('Sorry, this ip address does not exist anymore', $this->getRedirect() );
        }

        if( self::$software->softwareExists( $data['softwareid'] ) == false )
        {

            $this->redirectError('Sorry, it looks like this software might have been deleted', $this->getRedirect( $data['ipaddress'] ) );
        }

        $software = self::$software->getSoftware( $data['softwareid'] );

        if( self::$software->hasData( $software->softwareid ) == true && self::$software->keepData( $software->softwareid ) )
        {

            $softwaredata = self::$software->getSoftwareData( $software->softwareid );

            if( self::$software->checkSoftwareData( $software->softwareid, ['allowanondownloads'] ) == true )
            {

                unset( $softwaredata['allowanondownloads'] );
            }

            if( self::$software->checkSoftwareData( $software->softwareid, ['editable'] ) == true )
            {

                unset( $softwaredata['editable'] );
            }

            $new_softwareid = self::$software->copySoftware( $software->softwareid, $computerid, $userid, false, $softwaredata );
        }
        else
        {

            $new_softwareid = self::$software->copySoftware( $software->softwareid, $computerid, $userid );
        }

        self::$computers->addSoftware( $computerid, $new_softwareid, $software->type );

        if( self::$computers->hasSoftware( $computerid, $new_softwareid ) == false )
        {

            throw new SyscrackException();
        }

        $this->logDownload( $software->softwarename, $this->getComputerId( $data['ipaddress'] ), self::$computers->getComputer( $computerid )->ipaddress );

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

        if( self::$software->softwareExists( $softwareid ) == false )
        {

            throw new SyscrackException();
        }

        return $this->calculateProcessingTime( $computerid, Settings::getSetting('syscrack_hardware_download_type'), self::$software->getSoftware( $softwareid )->size / 5, $softwareid );
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
     * @param $softwarename
     * @param $computerid
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

        $this->logToComputer('Downloaded file (' . $softwarename . ') on <' . $ipaddress . '>', self::$computers->getComputer( self::$computers->getCurrentUserComputer() )->computerid, 'localhost' );
    }
}
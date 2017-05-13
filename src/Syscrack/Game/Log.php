<?php
namespace Framework\Syscrack\Game;

/**
 * Lewis Lancaster 2017
 *
 * Class Log
 *
 * @package Framework\Syscrack\Game
 */

use Framework\Application\Settings;
use Framework\Application\Utilities\FileSystem;

class Log
{

    /**
     * Updates the users log
     *
     * @param $message
     *
     * @param $computerid
     *
     * @param string $ipaddress
     *
     * @param null $log
     */

    public function updateLog( $message, $computerid, $ipaddress='localhost', $log=null )
    {

        if( $log == null )
        {

            $log = Settings::getSetting('syscrack_log_current');
        }

        $computerlog = $this->getCustomLog( $computerid, $log );

        $computerlog[] = array(
            'ipaddress'     => $ipaddress,
            'message'       => $message,
            'time'          => time()
        );

        $this->saveLog( $computerid, $computerlog, $log );
    }

    /**
     * Creates a new log
     *
     * @param $computerid
     *
     * @param null $log
     *
     * @param array $data
     */

    public function createLog( $computerid, $log=null, $data=[] )
    {

        if( $log == null )
        {

            $log = Settings::getSetting('syscrack_log_current');
        }

        if( $this->hasCurrentLog( $computerid ) )
        {

            return;
        }

        $this->createDirectory( $computerid );

        $this->saveLog( $computerid, $data, $log );
    }

    /**
     * Gets the current log of the computer
     *
     * @param $computerid
     *
     * @return mixed
     */

    public function getCurrentLog( $computerid )
    {

        return FileSystem::readJson( $this->getFilepath( $computerid ) . Settings::getSetting('syscrack_log_current') . '.json' );
    }

    /**
     * Reads a custom log
     *
     * @param $computerid
     *
     * @param $log
     *
     * @return mixed
     */

    public function getCustomLog( $computerid, $log )
    {

        return FileSystem::readJson( $this->getFilepath( $computerid ) . $log . '.json' );
    }

    /**
     * Returns true if the user has a log from this date
     *
     * @param $computerid
     *
     * @param $date
     *
     * @return bool
     */

    public function hasDate( $computerid, $date )
    {

        if( FileSystem::fileExists( $this->getFilepath( $computerid ) . $date ) == false )
        {

            return false;
        }

        return true;
    }

    /**
     * Returns false if no current log is found
     *
     * @param $computerid
     *
     * @return bool
     */

    public function hasCurrentLog( $computerid )
    {

        if( empty( FileSystem::readJson( $this->getFilepath( $computerid ) . Settings::getSetting('syscrack_log_current') ) ) )
        {

            return false;
        }

        return true;
    }

    /**
     * Returns true if the user has a log
     *
     * @param $computerid
     *
     * @return bool
     */

    public function hasLog( $computerid )
    {

        if( FileSystem::directoryExists( $this->getFilepath( $computerid) ) == false )
        {

            return false;
        }

        if( FileSystem::fileExists( $this->getFilepath( $computerid ) . Settings::getSetting('syscrack_log_current') . '.json' ) == false )
        {

            return false;
        }

        return true;
    }

    /**
     * Saves a log
     *
     * @param $computerid
     *
     * @param array $data
     *
     * @param null $log
     */

    public function saveLog( $computerid, array $data, $log=null )
    {

        if( $log == null )
        {

            $log = Settings::getSetting('syscrack_log_current');
        }

        FileSystem::writeJson( $this->getFilepath( $computerid ) . $log . '.json', $data );
    }

    /**
     * Creates the directory for this computers logs
     *
     * @param $computerid
     */

    private function createDirectory( $computerid )
    {

        if( FileSystem::directoryExists( $this->getFilepath( $computerid ) ) )
        {

            return;
        }

        FileSystem::createDirectory( $this->getFilepath( $computerid ) );
    }

    /**
     * Gets the filepath for the computers logs
     *
     * @param $computerid
     *
     * @return string
     */

    private function getFilepath( $computerid )
    {

        return Settings::getSetting('syscrack_log_location') . $computerid . '/';
    }
}
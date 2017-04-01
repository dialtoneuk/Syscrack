<?php
namespace Framework\Syscrack\Game;

/**
 * Lewis Lancaster 2016
 *
 * Class Hardware
 *
 * @package Framework\Syscrack\Game
 */

use Framework\Application\Settings;
use Framework\Database\Tables\Computers;
use Framework\Application\Utilities\FileSystem;
use Framework\Exceptions\SyscrackException;
use Framework\Syscrack\Game\Finance;

class Hardware
{

    protected $computers;

    public function __construct()
    {

        $this->computers = new Computers();
    }

    public function getDownloadSpeed( $computerid )
    {

        return $this->getHardware( $computerid )[ Settings::getSetting('syscrack_internet_download_type') ];
    }

    public function getUploadSpeed( $computerid )
    {

        return $this->getHardware( $computerid )[ Settings::getSetting('syscrack_internet_upload_type') ];
    }

    public function getCPUSpeed( $computerid )
    {

        return $this->getHardware( $computerid )[ Settings::getSetting('syscrack_cpu_type') ];
    }

    public function getGPUSpeed( $computerid )
    {

        return $this->getHardware( $computerid )[ Settings::getSetting('syscrack_gpu_type') ];
    }

    public function updateHardware( $computerid, $type, $value )
    {

        $hardwares = $this->getHardware( $computerid );

        if( isset( $hardwares[ $type ] ) == false )
        {

            throw new SyscrackException();
        }

        $hardwares[ $type ] = $value;

        $this->computers->updateComputer( $computerid, array(
            'hardwares' => json_encode( $hardwares, JSON_PRETTY_PRINT )
        ));
    }

    public function addHardware( $computerid, $type, $value )
    {

        $hardwares = $this->getHardware( $computerid );

        if( isset( $hardwares[ $type ] ) )
        {

            throw new SyscrackException();
        }

        $hardwares[ $type ] = $value;

        $this->computers->updateComputer( $computerid, array(
            'hardwares' => json_encode( $hardwares, JSON_PRETTY_PRINT )
        ));
    }

    public function getHardwareType( $computerid, $type )
    {

        return $this->getHardware( $computerid )[ $type ];
    }

    public function hasHardwareType( $computerid, $type )
    {

        $hardware = $this->getHardware( $computerid );

        if( empty( $hardware ) )
        {

            return false;
        }

        if( isset( $hardware[ $type ] ) == false )
        {

            return false;
        }

        return true;
    }

    public function getHardware( $computerid )
    {

        return json_decode( $this->computers->getComputer( $computerid )->hardware );
    }
}
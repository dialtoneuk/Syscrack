<?php
namespace Framework\Syscrack\Game\BaseClasses;

/**
 * Lewis Lancaster 2017
 *
 * Class Operation
 *
 * @package Framework\Syscrack\Game
 */

use Flight;
use Framework\Application\Settings;
use Framework\Application\Utilities\ArrayHelper;
use Framework\Exceptions\SyscrackException;
use Framework\Syscrack\Game\Computers;
use Framework\Syscrack\Game\Hardware;
use Framework\Syscrack\Game\Internet;
use Framework\Syscrack\Game\Log;
use Framework\Syscrack\Game\Softwares;
use Framework\Syscrack\Game\Utilities\TimeHelper;

class Operation
{

    /**
     * @var Log
     */

    public $log;

    /**
     * @var Softwares
     */

    public $softwares;

    /**
     * @var Computers
     */

    public $computers;

    /**
     * @var Internet
     */

    public $internet;

    /**
     * @var Hardware
     */

    public $hardware;

    /**
     * Operation constructor.
     */

    public function __construct( $createclasses = true )
    {

        if( $createclasses )
        {

            $this->log = new Log();

            $this->softwares = new Softwares();

            $this->computers = new Computers();

            $this->internet = new Internet();

            $this->hardware = new Hardware();
        }
    }

    /**
     * Gets the configuration of this operation
     */

    public function configuration()
    {

        return array(
            'allowsoftwares'    => true,
            'allowlocal'        => true,
            'allowanonymous'    => false,
            'requiresoftwares'  => true,
            'requireloggedin'   => true,
            'allowpost'         => false,
            'allowcustomdata'   => false,
            'postrequirements'  => []
        );
    }

    /**
     * Checks if the computer has this software by its name
     *
     * @param $softwarename
     *
     * @param $computerid
     *
     * @param $installed
     *
     * @return bool
     */

    public function hasSoftware( $softwarename, $computerid, $installed=true )
    {

        $softwares = $this->computers->getComputerSoftware( $computerid );

        foreach( $softwares as $key=>$value )
        {

            if( $this->softwares->softwareExists( $value['softwareid'] ) == false )
            {

                continue;
            }

            $software = $this->softwares->getSoftware( $value['softwareid'] );

            if( $software->softwarename == $softwarename )
            {

                if( $installed )
                {

                    if( $software->installed == true )
                    {

                        return true;
                    }
                }
                else
                {

                    return true;
                }
            }
        }

        return false;
    }

    public function getComputerClass( $computerid )
    {


    }

    /**
     * Gets the highest level of software on the users computer
     *
     * @param $computerid
     *
     * @param null $type
     *
     * @return array|null
     */

    public function getHighestLevelSoftware( $computerid, $type=null )
    {

        if( $type == null )
        {

            $type = Settings::getSetting('syscrack_software_cracker_type');
        }

        $softwares = $this->computers->getComputerSoftware( $computerid );

        if( empty( $softwares ) )
        {

            return null;
        }

        $results = [];

        foreach( $softwares as $key=>$value )
        {

            if( $value['type'] == $type )
            {

                if( $value['installed'] == true )
                {

                    $results[] = $this->softwares->getSoftware( $value['softwareid'] );
                }
            }
        }

        if( empty( $results ) )
        {

            return null;
        }

        $results = ArrayHelper::sortArray( $results, 'level' );

        if( is_array( $results ) == false )
        {

            return (array)$results;
        }

        return (array)$results[0];
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

            if( $data[ $requirement ] == null || empty( $data[ $requirement ] ))
            {

                return false;
            }
        }

        return true;
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

    public function logToComputer($message, $computerid, $ipaddress )
    {

        $this->log->updateLog( $message, $computerid, $ipaddress );
    }

    /**
     * Logs the actions on the personal players computer and the corresponding ip addresses computer
     *
     * @param $message
     *
     * @param $computerid
     *
     * @param $ipaddress
     */

    public function logActions( $message, $computerid, $ipaddress )
    {

        if( $this->computers->computerExists( $computerid ) == false )
        {

            throw new SyscrackException();
        }

        $victimid = $this->internet->getComputer( $ipaddress );

        if( $this->log->hasLog( $victimid->computerid ) == false || $this->log->hasLog( $computerid ) == false )
        {

            throw new SyscrackException();
        }

        $this->logToComputer( $message, $victimid->computerid, $this->computers->getComputer( $computerid )->ipaddress );

        $this->logToComputer( $message, $computerid, Settings::getSetting('syscrack_log_localhost_name'));

    }

    /**
     * Renders a page
     *
     * @param $file
     *
     * @param array|null $array
     */

    public function getRender( $file, array $array = null, $default_classes = false, $cleanob=true  )
    {

        if( $array !== null )
        {

            if( $default_classes !== false )
            {

                array_merge( $array, [
                    'softwares' => $this->softwares,
                    'internet'  => $this->internet,
                    'computer'  => $this->computers
                ]);
            }
        }

        if( $cleanob )
        {

            ob_clean();
        }

        Flight::render( Settings::getSetting('syscrack_view_location') . $file, $array);
    }

    /**
     * Calculates the processing time for an action using the algorithm
     *
     * @param $computerid
     *
     * @param string $hardwaretype
     *
     * @param float $speedness
     *
     * @param null $softwareid
     *
     * @return int
     */

    public function calculateProcessingTime( $computerid, $hardwaretype="cpu", $speedness=5.5, $softwareid=null )
    {

        if( $this->hardware->hasHardwareType( $computerid, $hardwaretype ) == false )
        {

            return TimeHelper::getSecondsInFuture( Settings::getSetting('syscrack_operations_default_processingtime') );
        }

        if( $softwareid !== null )
        {

            if( $this->softwares->softwareExists( $softwareid ) == false )
            {

                throw new SyscrackException();
            }

            $hardware = $this->hardware->getHardwareType( $computerid, $hardwaretype );

            $software = $this->softwares->getSoftware( $softwareid );

            return TimeHelper::getSecondsInFuture( floor( ( sqrt( $software->level / $hardware['value'] ) * $speedness ) * ( Settings::getSetting('syscrack_operations_global_speed' ) ) ) );
        }

        $hardware = $this->hardware->getHardwareType( $computerid, $hardwaretype );

        return TimeHelper::getSecondsInFuture( floor( sqrt( $speedness / $hardware['value'] ) * ( Settings::getSetting('syscrack_operations_global_speed' ) ) ) );
    }

    /**
     * Redirects the user to a page
     *
     * @param $path
     *
     * @param bool $exit
     */

    public function redirect( $path, $exit=true )
    {

        Flight::redirect( Settings::getSetting('controller_index_root') . $path );

        if( $exit == true )
        {

            exit;
        }
    }

    /**
     * Redirects the user to an error
     *
     * @param string $message
     *
     * @param string $path
     */

    public function redirectError($message = '', $path = '')
    {

        if( Settings::getSetting('error_use_session') )
        {

            if( session_status() !== PHP_SESSION_ACTIVE )
            {

                session_status();
            }

            $_SESSION['error'] = $message;

            if( $path !== '' )
            {

                if( empty( explode('/', $path ) ) )
                {

                    $_SESSION['error_page'] = explode('/', $path)[0];
                }
                else
                {

                    if( substr( $path, 0, 1 ) == '/' )
                    {

                        $_SESSION['error_page'] = substr( $path, 1);
                    }
                    else
                    {

                        $_SESSION['error_page'] = $path;
                    }
                }
            }
            else
            {

                $_SESSION['error_page'] = $this->getCurrentPage();
            }

            if ($path !== '')
            {

                $this->redirect( $path . '?error' );
            }
            else
            {

                $this->redirect( $this->getCurrentPage() . '?error' );
            }
        }
        else
        {

            if ($path !== '')
            {

                $this->redirect( $path . '?error=' . $message );
            }
            else
            {

                $this->redirect( $this->getCurrentPage() . '?error=' . $message );
            }
        }
    }

    /**
     * Redirects the user to a success
     *
     * @param string $path
     */

    public function redirectSuccess($path = '')
    {

        if ($path !== '')
        {

            $this->redirect( $path . '?success' );
        }

        $this->redirect( $this->getCurrentPage() . '?success', true );
    }

    /**
     * Checks the custom data
     *
     * @param $data
     *
     * @param array|null $requirements
     *
     * @return bool
     */

    public function checkCustomData( $data, array $requirements=null )
    {

        if( isset( $data['custom'] ) == false )
        {

            return false;
        }

        if( empty( $data['custom'] ) || $data['custom'] == null )
        {

            return false;
        }

        if( $requirements !== null )
        {

            foreach( $requirements as $requirement )
            {

                if( isset( $data['custom'][ $requirement ] ) == false )
                {

                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Checks if the computer has space
     *
     * @param $computerid
     *
     * @param float $needed
     *
     * @return bool
     */

    public function hasSpace( $computerid, $needed )
    {

        $hdd = $this->hardware->getHardwareType( $computerid, 'harddrive')['value'];
        $softwares = json_decode( $this->computers->getComputer( $computerid )->softwares, true );
        $usedspace = 0.0 + $needed;

        foreach( $softwares as $key=>$value )
        {

            $usedspace += $this->softwares->getSoftware( $value['softwareid'] )->size;
        }

        return ( $usedspace < $hdd ) ? true : false;
    }

    /**
     * Gets the page the operation should redirect too
     *
     * @param null $ipaddress
     *
     * @param bool $local
     *
     * @return string
     */

    public function getRedirect( $ipaddress=null, $local=false )
    {

        if( $ipaddress == $this->computers->getComputer( $this->computers->getCurrentUserComputer() )->ipaddress )
        {

            return Settings::getSetting('syscrack_computers_page');
        }

        if( $local )
        {

            return Settings::getSetting('syscrack_computers_page');
        }

        if( $ipaddress )
        {

            return Settings::getSetting('syscrack_game_page') . '/' . Settings::getSetting('syscrack_internet_page') . '/' . $ipaddress;
        }

        return Settings::getSetting('syscrack_game_page');
    }

    /**
     * Unsets session variables on logout
     */

    public function safeUnset()
    {

        $unset = Settings::getSetting('syscrack_operations_safeunset_values');

        foreach( $unset as $value )
        {

            if( isset( $_SESSION[ $value ] ) )
            {

                unset( $_SESSION[ $value ] );
            }
        }
    }

    /**
     * Gets the computer id from an ipaddress
     *
     * @param $ipaddress
     *
     * @return mixed
     */

    public function getComputerId( $ipaddress )
    {

        return $this->internet->getComputer( $ipaddress )->computerid;
    }

    /**
     * Gets the current computers ip address
     *
     * @return mixed
     */

    public function getCurrentComputerAddress()
    {

        return $this->computers->getComputer( $this->computers->getCurrentUserComputer() )->ipaddress;
    }

    /**
     * Gets the software name of a software
     *
     * @param $softwareid
     *
     * @return mixed
     */

    public function getSoftwareName( $softwareid )
    {

        if( $this->softwares->softwareExists( $softwareid ) == false )
        {

            throw new SyscrackException();
        }

        return $this->softwares->getSoftware( $softwareid )->softwarename;
    }

    /**
     * Gets the current page
     *
     * @return string
     */

    private function getCurrentPage()
    {

        $page = array_values(array_filter(explode('/', strip_tags( $_SERVER['REQUEST_URI'] ))));

        if( empty( $page ) )
        {

            return Settings::getSetting('controller_index_page');
        }

        return $page[0];
    }

    /**
     * Gets the entire path in the form of an array
     *
     * @return array
     */

    private function getPageSplat()
    {

        return array_values(array_filter(explode('/', strip_tags( $_SERVER['REQUEST_URI'] ))));
    }
}
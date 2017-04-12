<?php
namespace Framework\Views\Pages;

/**
 * Lewis Lancaster 2016
 *
 * Class Error
 *
 * @package Framework\Views\Pages
 */

use Framework\Application\Container;
use Framework\Application\Session;
use Framework\Application\Settings;
use Framework\Application\Utilities\PostHelper;
use Framework\Syscrack\Game\Softwares;
use Framework\Exceptions\SyscrackException;
use Framework\Syscrack\Game\Log;
use Framework\Syscrack\Game\Structures\Operation;
use Framework\Views\Structures\Page;
use Framework\Syscrack\Game\Internet;
use Framework\Syscrack\Game\Computer;
use Framework\Syscrack\Game\Operations;
use Flight;

class Game implements Page
{

    /**
     * @var Internet
     */

    protected $internet;

    /**
     * @var Computer
     */

    protected $computer;

    /**
     * @var Operations
     */

    protected $operations;

    /**
     * @var Softwares
     */

    protected $software;

    /**
     * Game constructor.
     */

    public function __construct()
    {

        $this->internet = new Internet();

        $this->computer = new Computer();

        $this->software = new Softwares( false );

        if( session_status() !== PHP_SESSION_ACTIVE )
        {

            session_start();
        }

        if( Container::hasObject('session') == false )
        {

            Container::setObject('session', new Session() );
        }

        if( Container::getObject('session')->isLoggedIn() == false )
        {

            Flight::redirect( '/' . Settings::getSetting('controller_index_root') );

            exit;
        }
    }

    /**
     * The index page has a special algorithm which allows it to access the root. Only the index can do this.
     *
     * @return array
     */

    public function mapping()
    {

        return array(
            [
                'GET /game/', 'page'
            ],
            [
                'POST /game/', 'pageProcess'
            ],
            [
                '/game/internet/', 'internet'
            ],
            [
                '/game/computer/', 'computer'
            ],
            [
                '/game/addressbook/', 'addressBook'
            ],
            [
                'GET /game/computer/log/', 'computerLog'
            ],
            [
                'POST /game/computer/log/', 'computerLogProcess'
            ],
            [
                '/game/computer/processes/', 'computerProcesses'
            ],
            [
                '/game/internet/@ipaddress', 'viewAddress'
            ],
            [
                '/game/internet/@ipaddress/@process', 'process'
            ],
            [
                '/game/internet/@ipaddress/@process/@softwareid', 'processSoftware'
            ]
        );
    }

    /**
     * Game Index
     */

    public function page()
    {

        $this->getRender('page.game');
    }

    /**
     * Switches your computer
     */

    public function pageProcess()
    {

        if( PostHelper::hasPostData() == false || PostHelper::checkForRequirements( ['action','computerid'] ) == false )
        {

            $this->page();
        }
        else
        {

            $action = PostHelper::getPostData('action');

            $computerid = PostHelper::getPostData('computerid');

            if( $this->computer->computerExists( $computerid ) == false )
            {

                $this->page();
            }
            else
            {

                if( $action == "switch" )
                {

                    if( $this->computer->getComputer( $computerid )->userid != Container::getObject('session')->getSessionUser() )
                    {

                        $this->page();
                    }
                    else
                    {

                        $this->computer->setCurrentUserComputer( $computerid );

                        Flight::redirect('/game/');
                    }
                }
            }
        }
    }

    /**
     * The Computer
     */

    public function computer()
    {

        $this->getRender('page.game.computer');
    }

    /**
     * The computer log
     */

    public function computerLog()
    {

        $this->getRender('page.game.computer.log');
    }

    /**
     * Your computer processes
     */

    public function computerProcesses()
    {

        $this->getRender('page.game.computer.processes');
    }

    /**
     * The address book
     */

    public function addressBook()
    {

        $this->getRender('page.game.addressbook');
    }

    /**
     * Clears the local computers log
     */

    public function computerLogProcess()
    {

        if( PostHelper::hasPostData() == false )
        {

            $this->redirectLocalError('No post data', 'log');
        }
        else
        {

            if( PostHelper::checkForRequirements(['action'] ) == false )
            {

                $this->redirectLocalError('No action given', 'log');
            }
            else
            {

                $action = PostHelper::getPostData('action');

                if( $action == 'clear' )
                {

                    $log = new Log();

                    $log->saveLog( $this->computer->getCurrentUserComputer(), [] );

                    $this->redirectLocalSuccess('log');
                }
                else
                {

                    $this->redirectLocalError('Action does not exist');
                }
            }
        }
    }

    /**
     * Default page
     */

    public function internet()
    {

        if( PostHelper::hasPostData() )
        {

            if( $this->validAddress() == false )
            {

                $this->redirectError('404 Not Found');
            }

            $this->getRender('page.game.internet', array( 'ipaddress' => PostHelper::getPostData('ipaddress') ) );
        }
        else
        {

            $this->getRender('page.game.internet', array( 'ipaddress' => $this->internet->getComputerAddress( Settings::getSetting('syscrack_whois_computer') ) ) );
        }
    }

    /**
     * Processes a game action
     *
     * @param $ipaddress
     *
     * @param $process
     */

    public function process( $ipaddress, $process )
    {

        $this->operations = new Operations();

        if( $this->validAddress( $ipaddress ) == false )
        {

            $this->redirectError('404 Not Found');
        }
        else
        {

            if( $this->operations->hasProcessClass( $process ) == false )
            {

                $this->redirectError('Action not found', $ipaddress );
            }

            $class = $this->operations->findProcessClass( $process );

            if( $class instanceof Operation == false )
            {

                throw new SyscrackException();
            }

            $completiontime = $class->getCompletionSpeed( $this->computer->getCurrentUserComputer(), $ipaddress, $process );

            if( $completiontime == null )
            {

                $result = $class->onCreation( time(), $this->computer->getCurrentUserComputer(), Container::getObject('session')->getSessionUser(), $process, array(
                    'ipaddress' => $ipaddress
                ));

                if( $result == false )
                {

                    $this->redirectError('Unable to create process', $ipaddress );
                }
                else
                {

                    $class->onCompletion( time(), time(), $this->computer->getCurrentUserComputer(), Container::getObject('session')->getSessionUser(), $process, array(
                        'ipaddress' => $ipaddress
                    ));
                }
            }
            else
            {

                $processid = $this->operations->createProcess( $completiontime, $this->computer->getCurrentUserComputer(), Container::getObject('session')->getSessionUser(), $process, array(
                    'ipaddress' => $ipaddress
                ));

                if( $processid == false )
                {

                    $this->redirectError('Unable to create process', $ipaddress );
                }

                Flight::redirect('/processes/' . $processid );
            }
        }
    }

    /**
     * Processes a software action
     *
     * @param $ipaddress
     *
     * @param $process
     *
     * @param $softwareid
     */

    public function processSoftware( $ipaddress, $process, $softwareid )
    {

        $this->operations = new Operations();

        if( $this->validAddress( $ipaddress ) == false )
        {

            $this->redirectError('404 Not Found');
        }
        else
        {

            if( $this->internet->hasCurrentConnection() == false || $this->internet->getCurrentConnectedAddress() != $ipaddress )
            {

                $this->redirectError('You must be connected to this computer to preform actions on its software');
            }

            if( $this->operations->hasProcessClass( $process ) == false )
            {

                $this->redirectError('Action not found', $ipaddress );
            }

            if( $this->softwares->softwareExists( $softwareid ) == false )
            {

                $this->redirectError('Software does not exist', $ipaddress );
            }

            if( $this->computer->hasSoftware( $this->internet->getComputer( $ipaddress )->computerid, $softwareid ) == false )
            {

                $this->redirectError('Software does not exist', $ipaddress );
            }

            $class = $this->operations->findProcessClass( $process );

            if( $class instanceof Operation == false )
            {

                throw new SyscrackException();
            }

            $completiontime = $class->getCompletionTime( $this->computer->getCurrentUserComputer(), $ipaddress, $process );

            if( $completiontime == null )
            {

                $result = $class->onCreation( time(), $this->computer->getCurrentUserComputer(), Container::getObject('session')->getSessionUser(), $process, array(
                    'ipaddress'     => $ipaddress,
                    'softwareid'    => $softwareid
                ));

                if( $result == false )
                {

                    $this->redirectError('Process cannot be completed', $ipaddress );
                }
                else
                {

                    $class->onCompletion( time(), time(), $this->computer->getCurrentUserComputer(), Container::getObject('session')->getSessionUser(), $process, array(
                        'ipaddress' => $ipaddress,
                        'softwareid'    => $softwareid
                    ));
                }
            }
            else
            {

                $processid = $this->operations->createProcess( $completiontime, $this->computer->getCurrentUserComputer(), Container::getObject('session')->getSessionUser(), $process, array(
                    'ipaddress' => $ipaddress,
                    'softwareid'    => $softwareid
                ));

                if( $processid == false )
                {

                    $this->redirectError('Process failed to be created', $ipaddress );
                }

                Flight::redirect('/processes/' . $processid );
            }
        }
    }

    /**
     * Views a specific address
     *
     * @param $ipaddress
     */

    public function viewAddress( $ipaddress )
    {

        if( $this->validAddress( $ipaddress ) == false )
        {

            $this->redirectError('404 Not Found');
        }

        $this->getRender('page.game.internet', array( 'ipaddress' => $ipaddress ) );
    }

    /**
     * Redirects local error
     *
     * @param string $message
     */

    private function redirectLocalError( $message="", $page="" )
    {

        if( $page !== "" )
        {

            Flight::redirect('/game/computer/' . $page . '/?error=' . $message ); die();
        }

        Flight::redirect('/game/computer/?error=' . $message ); die();
    }

    /**
     * Redirects local success
     *
     * @param string $page
     */

    private function redirectLocalSuccess( $page="" )
    {

        if( $page !== "" )
        {

            Flight::redirect('/game/computer/' . $page . '/?success'); die();
        }

        Flight::redirect('/game/computer/?success'); die();
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

            Flight::redirect('/game/internet/' . $ipaddress . "?error=" . $message ); die();
        }

        Flight::redirect('/game/internet/?error=' . $message ); die();
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

    /**
     * returns true if the IP address is valid
     *
     * @param null $ipaddress
     *
     * @return bool
     */

    private function validAddress( $ipaddress=null )
    {

        if( $ipaddress == null )
        {

            if( PostHelper::checkForRequirements(['ipaddress'] ) == false )
            {

                return false;
            }

            $ipaddress = PostHelper::getPostData('ipaddress');
        }

        if( filter_var( $ipaddress, FILTER_VALIDATE_IP ) == false )
        {

            return false;
        }

        if( $this->internet->ipExists( $ipaddress ) == false )
        {

            return false;
        }

        return true;
    }
}
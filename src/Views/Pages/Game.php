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
use Framework\Exceptions\SyscrackException;
use Framework\Syscrack\Game\Structures\Process;
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

    protected $processes;

    /**
     * Game constructor.
     */

    public function __construct()
    {

        $this->internet = new Internet();

        $this->computer = new Computer();

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
                '/game/', 'page'
            ],
            [
                '/game/internet/', 'internet'
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

    public function page()
    {

        $this->getRender('page.game');
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

        $this->processes = new Operations();

        if( $this->validAddress( $ipaddress ) == false )
        {

            $this->redirectError('404 Not Found');
        }
        else
        {

            if( $this->processes->hasProcessClass( $process ) == false )
            {

                $this->redirectError('Action not found', $ipaddress );
            }

            $class = $this->processes->findProcessClass( $process );

            if( $class instanceof Process == false )
            {

                throw new SyscrackException();
            }

            $completiontime = $class->getCompletionTime( $this->computer->getCurrentUserComputer(), $ipaddress, $process );

            if( $completiontime == null )
            {

                $result = $class->onCreation( time(), $this->computer->getCurrentUserComputer(), Container::getObject('session')->getSessionUser(), $process, array(
                    'ipaddress' => $ipaddress
                ));

                if( $result = false )
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

                $processid = $this->processes->createProcess( $completiontime, $this->computer->getCurrentUserComputer(), Container::getObject('session')->getSessionUser(), $process, array(
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

        $this->processes = new Operations();

        if( $this->validAddress( $ipaddress ) == false )
        {

            $this->redirectError('404 Not Found');
        }
        else
        {

            if( $this->processes->hasProcessClass( $process ) == false )
            {

                $this->redirectError('Action not found', $ipaddress );
            }

            $class = $this->processes->findProcessClass( $process );

            if( $class instanceof Process == false )
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

                if( $result = false )
                {

                    $this->redirectError('Unable to create process', $ipaddress );
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

                $processid = $this->processes->createProcess( $completiontime, $this->computer->getCurrentUserComputer(), Container::getObject('session')->getSessionUser(), $process, array(
                    'ipaddress' => $ipaddress,
                    'softwareid'    => $softwareid
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

            Flight::redirect('/game/internet/' . $ipaddress . "?error=" . $message ); exit;
        }

        Flight::redirect('/game/internet/?error=' . $message ); exit;
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
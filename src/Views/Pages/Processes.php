<?php
namespace Framework\Views\Pages;

/**
 * Lewis Lancaster 2016
 *
 * Class Processes
 *
 * @package Framework\Views\Pages
 */

use Framework\Views\Structures\Page;
use Framework\Syscrack\Game\Processes as Manager;
use Framework\Syscrack\Game\Computer;
use Framework\Application\Container;
use Framework\Application\Session;
use Framework\Application\Settings;
use Flight;

class Processes implements Page
{

    /**
     * @var Manager
     */

    protected $processes;

    protected $computer;

    /**
     * Processes constructor.
     */

    public function __construct()
    {

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

        $this->processes = new Manager();

        $this->computer = new Computer();
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
                '/processes/', 'page'
            ],
            [
                '/processes/@processid', 'viewProcess'
            ],
            [
                '/processes/@processid/complete', 'completeProcess'
            ]
        );
    }

    /**
     * Default page
     */

    public function page()
    {

        die('page soon');
    }

    /**
     * Views a process
     *
     * @param $processid
     */

    public function viewProcess( $processid )
    {

        if( $this->processes->processExists( $processid ) == false )
        {

            $this->redirectError('This process does not exist');
        }
        else
        {

            $process = $this->processes->getProcess( $processid );

            if( $process->userid != Container::getObject('session')->getSessionUser() )
            {

                $this->redirectError('This process isnt yours');
            }
            else
            {

                if( $process->computerid != $this->computer->getCurrentUserComputer() )
                {

                    $this->redirectError('You are connected as a different computer');
                }
                else
                {

                    $this->getRender('page.processes', array( 'processid' => $processid, 'processclass' => $this->processes, 'auto' => true ) );
                }
            }
        }
    }

    public function completeProcess( $processid )
    {

        if( $this->processes->processExists( $processid ) == false )
        {

            $this->redirectError('This process does not exist');
        }
        else
        {

            $process = $this->processes->getProcess( $processid );

            if( $process->userid != Container::getObject('session')->getSessionUser() )
            {

                $this->redirectError('This process isnt yours');
            }
            else
            {

                if( $process->computerid != $this->computer->getCurrentUserComputer() )
                {

                    $this->redirectError('You are connected as a different computer');
                }
                else
                {

                    if( $this->processes->canComplete( $processid ) == false )
                    {

                        $this->redirectError('Process has not yet completed');
                    }
                    else
                    {

                        $this->processes->completeProcess( $processid );
                    }
                }
            }
        }
    }

    /**
     * Redirects the user to an error
     *
     * @param string $message
     *
     * @param string $processid
     */

    private function redirectError( $message='', $processid='' )
    {

        if( $processid !== '' )
        {

            Flight::redirect('/processes/' . $processid. "?error=" . $message ); exit;
        }

        Flight::redirect('/processes/?error=' . $message ); exit;
    }

    /**
     * Redirects the user to a success page
     *
     * @param string $processid
     */

    private function redirectSuccess( $processid='' )
    {

        if( $processid !== '' )
        {

            Flight::redirect('/processes/' . $processid . "?success" ); exit;
        }

        Flight::redirect('/processes/?success'); exit;
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
}
<?php
require_once 'vendor/autoload.php';

/**

    Framework 4 (v4.1.0 21/03/2017)

    - Written by Lewis Lancaster.

**/

    use Framework\Application;
    use Framework\Application\Settings;

    /**
 * Starts the application
 */

$application = new Application( false );

try
{

    $application->runController();

    /**
     * Set the application to be global
     */

    $application->addToGlobalContainer();

    /**
     * Set the application to be global
     */

    $application->runFlight();
}
catch( Exception $error )
{

    if( Settings::getSetting('error_logging') )
    {

        $application->getErrorHandler()->handleError( $error );

        if( Settings::getSetting('error_display_page') )
        {

            if( $_SERVER['REQUEST_URI'] == '/' )
                Flight::redirect('/error?redirect=/index');
            else
                Flight::redirect('/error?redirect=' . $_SERVER['REQUEST_URI'] );
        }
        else
        {

            Flight::redirect('/');
        }
    }
    else
    {

        Flight::notFound();
    }
}

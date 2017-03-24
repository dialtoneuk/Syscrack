<?php
require_once 'vendor/autoload.php';

/**

    Framework 4 (v4.1.0 21/03/2017)

    - Written by Lewis Lancaster.

**/

use Framework\Application;

//Log our start
Application\Utilities\Log::log('Application has begun loading');

/**
 * Starts the application
 */

$application = new Application( false );

try
{

    /**
     * Set the application to be global
     */

    Application\Utilities\Log::log('Controller Ran');
    $application->runController();

    /**
     * Set the application to be global
     */

    Application\Utilities\Log::log('Added application to global container');
    $application->addToGlobalContainer();

    /**
     * Set the application to be global
     */

    Application\Utilities\Log::log('Flight Ran');
    $application->runFlight();
}
catch( Exception $error )
{

    if( Application\Settings::getSetting('error_logging') )
    {

        $application->getErrorHandler()->handleError( $error );

        if( Application\Settings::getSetting('error_display_page') )
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
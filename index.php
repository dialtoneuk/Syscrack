<?php

    /**
     *
     _____                                _
    / ____|                              | |
    | (___  _   _ ___  ___ _ __ __ _  ___| | __
     \___ \| | | / __|/ __| '__/ _` |/ __| |/ /
     ____) | |_| \__ \ (__| | | (_| | (__|   <
    |_____/ \__, |___/\___|_|  \__,_|\___|_|\_\
            __/ | 2015/2018 - Alpha / Prototype
           |___/
     *
     * Written by Lewis Lancaster 2017.
     */

    define('SYSCRACK_TIME_START', microtime( true ) );

    /**
     * Checks if composer exists
     */

    if( version_compare(phpversion(), '7.0.0', '<' ) )
    {

        ob_clean();

        ?>

            <html>
                <head>
                    <meta charset="utf-8">
                    <meta http-equiv="X-UA-Compatible" content="IE=edge">
                    <meta name="viewport" content="width=device-width, initial-scale=1">

                    <title>PHP Version Error</title>

                    <!-- Stylesheets -->
                    <link href="/assets/css/bootstrap.dark.css" rel="stylesheet">
                    <link href="/assets/css/bootstrap-combobox.css" rel="stylesheet">

                    <!--[if lt IE 9]>
                    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
                    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
                    <![endif]-->
                </head>
                <body>
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-12">
                                <h5 style="color: #ababab" class="text-uppercase text-center">
                                    Critical Error
                                </h5>
                                <div class="panel panel-danger">
                                    <div class="panel-heading">
                                        Major error
                                    </div>
                                    <div class="panel-body text-center">
                                        Your PHP version is currently <?=phpversion()?> and needs to be version 7.0.0 or higher, if you are having troubles, please refer to our <a href="https://github.com/dialtoneuk/Syscrack2017/">github</a>.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </body>
            </html>
        <?php

        exit;
    }

    if( file_exists( $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php' ) == false )
    {

        ob_clean();

        ?>

            <html>
                <head>
                    <meta charset="utf-8">
                    <meta http-equiv="X-UA-Compatible" content="IE=edge">
                    <meta name="viewport" content="width=device-width, initial-scale=1">

                    <title>Composer Error</title>

                    <!-- Stylesheets -->
                    <link href="/assets/css/bootstrap.dark.css" rel="stylesheet">
                    <link href="/assets/css/bootstrap-combobox.css" rel="stylesheet">

                    <!--[if lt IE 9]>
                    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
                    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
                    <![endif]-->
                </head>
                <body>
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-12">
                                <<h5 style="color: #ababab" class="text-uppercase text-center">
                                    Critical Error
                                </h5>
                                <div class="panel panel-danger">
                                    <div class="panel-heading">
                                        Major error
                                    </div>
                                    <div class="panel-body text-center">
                                        Composer was unable to be loaded, this usually means you haven't ran 'composer install' on your htdocs directory. If you are still having troubles,
                                        please check out <a href="https://github.com/dialtoneuk/Syscrack2017/">the github.</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </body>
            </html>
        <?php

        exit;
    }

    /**
     * Requires the vendor
     */

    require_once "vendor/autoload.php";

    /**
     * Check if the framework application class exists
     */

    if( class_exists('Framework\\Application') == false )
    {

        ob_clean();

        ?>

            <html>
                <head>
                    <meta charset="utf-8">
                    <meta http-equiv="X-UA-Compatible" content="IE=edge">
                    <meta name="viewport" content="width=device-width, initial-scale=1">

                    <title>Framework Error</title>

                    <!-- Stylesheets -->
                    <link href="/assets/css/bootstrap.dark.css" rel="stylesheet">
                    <link href="/assets/css/bootstrap-combobox.css" rel="stylesheet">

                    <!--[if lt IE 9]>
                    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
                    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
                    <![endif]-->
                </head>
                <body>
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-12">
                                <h5 style="color: #ababab" class="text-uppercase text-center">
                                    Critical Error
                                </h5>
                                <div class="panel panel-danger">
                                    <div class="panel-heading">
                                        Major error
                                    </div>
                                    <div class="panel-body text-center">
                                        The framework was unable to find the Application class, this could be due to a few reasons, please check out the <a href="https://github.com/dialtoneuk/Syscrack2017/">github for solutions.</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </body>
            </html>
        <?php

        exit;
    }

    use Framework\Application;
    use Framework\Application\Settings;

    if( Settings::canFindSettings() == false )
    {

        ob_clean();

        ?>

            <html>
                <head>
                    <meta charset="utf-8">
                    <meta http-equiv="X-UA-Compatible" content="IE=edge">
                    <meta name="viewport" content="width=device-width, initial-scale=1">

                    <title>Framework Error</title>

                    <!-- Stylesheets -->
                    <link href="/assets/css/bootstrap.dark.css" rel="stylesheet">
                    <link href="/assets/css/bootstrap-combobox.css" rel="stylesheet">

                    <!--[if lt IE 9]>
                    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
                    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
                    <![endif]-->
                </head>
                <body>
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-12">
                                <h5 style="color: #ababab" class="text-uppercase text-center">
                                    Critical Error
                                </h5>
                                <div class="panel panel-danger">
                                    <div class="panel-heading">
                                        Major error
                                    </div>
                                    <div class="panel-body text-center">
                                        The framework was unable to find your settings file, this could be because of a few reasons. We suggest you check out <a href="https://github.com/dialtoneuk/Syscrack2017/">the github.</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
                </body>
            </html>
        <?php

        exit;
    }

    /**
    * Starts the application
    */

    try
    {

        $application = new Application( false );

        /**
         * Handles an error with the render engine
         */

        Flight::map('error', function(Error $error) use ( $application ){

            if( Settings::getSetting('error_logging') )
            {

                $application->getErrorHandler()->handleFlightError( $error );

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
        });

        /**
         * Maps the 'not found' page
         */

        Flight::map('notFound', function(){

            Flight::redirect('/framework/error/notfound/');
        });

        /**
         * Map our time end
         */

        Flight::before('start', function ()
        {

            define('SYSCRACK_TIME_END', microtime( true ) );
        });

        /**
         * Starts the applications controllers
         */

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
    }
    catch( Exception $error )
    {

        ob_clean();

        ?>

            <html>
                <head>
                    <meta charset="utf-8">
                    <meta http-equiv="X-UA-Compatible" content="IE=edge">
                    <meta name="viewport" content="width=device-width, initial-scale=1">

                    <title>Critical Error</title>

                    <!-- Stylesheets -->
                    <link href="/assets/alpha/css/bootstrap.dark.css" rel="stylesheet">
                    <link href="/assets/alpha/css/bootstrap-combobox.css" rel="stylesheet">

                    <!--[if lt IE 9]>
                    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
                    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
                    <![endif]-->
                </head>
                <body>
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-12">
                                <h5 style="color: #ababab" class="text-uppercase text-center">
                                    Critical Error
                                </h5>
                                <div class="panel panel-danger">
                                    <div class="panel-heading">
                                        <?=$error->getMessage()?> @ <?=$error->getFile()?> line <?=$error->getLine()?>
                                    </div>
                                    <div class="panel-body text-center">
                                        <p>
                                            An error occurred outside of the framework, this is usually due to a permission error, a rewrite error, or something completely different, check out the error stack below.
                                        </p>

                                        <div class="well">
<pre>
    <?=$error->getTraceAsString()?>
</pre>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </body>
            </html>
        <?php
    }

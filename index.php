<?php

    /**
     *
     _____                                _
    / ____|                              | |
    | (___  _   _ ___  ___ _ __ __ _  ___| | __
     \___ \| | | / __|/ __| '__/ _` |/ __| |/ /
     ____) | |_| \__ \ (__| | | (_| | (__|   <
    |_____/ \__, |___/\___|_|  \__,_|\___|_|\_\
            __/ | 2015/2017 - Beta
           |___/
     *
     * Written by Lewis Lancaster
     */

    /**
     * Checks if composer exists
     */

    if( file_exists( $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php') == false )
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
                    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
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
                                <h1 class="page-header text-center">
                                    Critical Error
                                </h1>
                                <div class="panel panel-danger">
                                    <div class="panel-heading">
                                        Major error
                                    </div>
                                    <div class="panel-body text-center">
                                        Composer was unable to be loaded, this usually means you haven't ran 'composer install' on your htdocs directory. If you are still having troubles,
                                        please check out <a href="#">the github.</a>
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
                <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
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
                            <h1 class="page-header text-center">
                                Critical Error
                            </h1>
                            <div class="panel panel-danger">
                                <div class="panel-heading">
                                    Major error
                                </div>
                                <div class="panel-body text-center">
                                    The framework was unable to find the Application class, this could be due to a few reasons, please check out the <a href="#">github for solutions</a>
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
                    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
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
                                <h1 class="page-header text-center">
                                    Critical Error
                                </h1>
                                <div class="panel panel-danger">
                                    <div class="panel-heading">
                                        <?=$error->getMessage()?> @ <?=$error->getFile()?> line <?=$error->getLine()?>
                                    </div>
                                    <div class="panel-body text-center">
                                        <p>
                                            An error occured outside of the framework, this is usually due to a permission error, a rewrite error, or something completely different, check out the error stack below.
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

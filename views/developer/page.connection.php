<?php

    use Framework\Database\Manager as Database;

?>
<html lang="en">

    <?php

        Flight::render('developer/templates/template.header', array( 'pagetitle' => 'Connection Tester'));
    ?>

    <body>
        <div class="container">

            <?php

                Flight::render('developer/templates/template.navigation');

                if( isset( $_GET['error'] ) )
                    Flight::render('developer/templates/template.alert', array( 'message' => $_GET['error'] ) );
                elseif( isset( $_GET['success'] ) )
                    Flight::render('developer/templates/template.alert', array( 'message' => 'Success', 'alert_type' => 'alert-success' ) );
            ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="page-header">
                        <h1>DB Connection Tester</h1>
                    </div>

                    <p class="lead">
                        This tool displays the current status of the DB connection and if there is currently a successful
                        connection between the web-application and your database.
                    </p>

                    <p>
                        To use this, first if you have not already, head to the <a href="/developer/connectioncreator/">Connecton Creator</a>
                        tool and enter your database settings. This page should update automatically everytime you refresh, but
                        you can also press the 'refresh' button underneath the status report.
                    </p>
                </div>
                <div class="col-md-6">
                    <div class="page-header">
                        <h1>Current Connection Status</h1>
                    </div>

                    <?php

                        //TODO: Find a cleaner way to do this
                        $databaseerror = null;

                        try
                        {

                            $database = new Database();

                            if( Database::getCapsule()->getConnection()->getPdo() )
                            {

                                // Do nothing, we are successfully connected
                            }
                        }
                        catch( Exception $error )
                        {

                            $databaseerror = $error->getMessage();
                        }

                        if( $databaseerror !== null )
                        {

                            ?>
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <p class="lead">
                                            Failed to connect...
                                        </p>

                                        <p>
                                            <?= $databaseerror ?>
                                        </p>

                                        <h5 style="color: #67b168">
                                            Connection Details
                                        </h5>

                                        <div class="well">
                                            <?= json_encode( Database::$connection, JSON_PRETTY_PRINT ) ?>
                                        </div>
                                    </div>
                                </div>
                            <?php
                        }
                        else
                        {

                            ?>
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <p class="lead">
                                            Succesfully connected!
                                        </p>

                                        <p>
                                            The web-application and database are now connected, if you are still getting
                                            connection errors, check the information below and make sure it is correct.
                                        </p>

                                        <h5 style="color: #67b168">
                                            Connection Details
                                        </h5>

                                        <div class="well">
                                            <?= json_encode( Database::$connection, JSON_PRETTY_PRINT ) ?>
                                        </div>
                                    </div>
                                </div>
                            <?php
                        }
                    ?>

                    <button class="btn btn-lg btn-primary btn-block" onclick="window.location.reload()">
                        Refresh
                    </button>
                </div>
            </div>

            <?php

                Flight::render('developer/templates/template.footer', array( 'breadcrumb' => true ));
            ?>
        </div>
    </body>
</html>

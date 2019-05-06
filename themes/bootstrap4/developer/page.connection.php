<?php

    use Framework\Application\Settings;
    use Framework\Application\Utilities\FileSystem;
    use Framework\Database\Manager as Database;
    use Framework\Application\Render;
?>
<html lang="en">

    <?php

        if( $settings['theme_dark') )
        {

            Render::view('developer/templates/template.header', array( 'pagetitle' => 'Developer / Connection',
                'styles' => array(
                    '<link href="/assets/css/highlight/atelier-cave-dark.css" rel="stylesheet">'
                )));
        }
        else
        {

            Render::view('developer/templates/template.header', array( 'pagetitle' => 'Developer / Connection',
                'styles' => array(
                    '<link href="/assets/css/highlight/magula.css" rel="stylesheet">'
                )));
        }
    ?>

    <body>
        <div class="container">

            <?php

                Render::view('developer/templates/template.navigation');
            ?>
            <div class="row">
                <div class="col-lg-12">
                    <?php

                        if( isset( $_GET['error'] ) )
                            Render::view('syscrack/templates/template.alert', array( 'message' => $_GET['error'] ) );
                        elseif( isset( $_GET['success'] ) )
                            Render::view('syscrack/templates/template.alert', array( 'message' => $settings['alert_success_message'), 'alert_type' => 'alert-success' ) );
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <h5 style="color: #ababab" class="text-uppercase">
                        Connection
                    </h5>
                    <p class="lead">
                        This tool displays the current status your database connection and if there is currently a successful
                        connection between the web-application and your database.
                    </p>

                    <p>
                        To use this, first if you have not already, head to the <a href="/developer/connection/creator/">Connecton Creator</a>
                        tool and enter your database settings. This page should update automatically everytime you refresh, but
                        you can also press the 'refresh' button underneath the status report.
                    </p>
                    <h5 style="color: #ababab" class="text-uppercase">
                        Actions
                    </h5>
                    <button class="btn btn-sm btn-primary btn-block" onclick="window.location.reload()">
                        Refresh
                    </button>
                </div>
                <div class="col-md-8">
                    <h5 style="color: #ababab" class="text-uppercase">
                        Connection Status
                    </h5>
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
                                <div class="panel panel-danger">
                                    <div class="panel-body">
                                        <p class="lead">
                                            Failed to connect...
                                        </p>
                                        <p>
                                            <?= $databaseerror ?>
                                        </p>
                                        <h5 style="color: #ababab" class="text-uppercase">
                                            Details
                                        </h5>
                                        <pre style="width 100%; margin: 0; padding: 0;">
                                                <code class="json hljs">
<?php
    if( $settings['database_show_decrypted') == false )
    {

        echo ( FileSystem::read( $settings['database_connection_file') ) );
    }
    else
    {

        echo ( json_encode( Database::$connection, JSON_PRETTY_PRINT ) );
    }
?>
                                            </code>
                                        </pre>
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
                                        <h5 style="color: #ababab" class="text-uppercase">
                                            Details
                                        </h5>
                                        <pre style="width 100%; margin: 0; padding: 0;">
                                                <code class="json hljs">
<?php
    if( $settings['database_show_decrypted') == false )
    {

        echo ( FileSystem::read( $settings['database_connection_file') ) );
    }
    else
    {

        echo ( json_encode( Database::$connection, JSON_PRETTY_PRINT ) );
    }
?>
                                            </code>
                                        </pre>
                                    </div>
                                </div>
                            <?php
                        }
                    ?>
                </div>
            </div>

            <?php

                Render::view('developer/templates/template.footer', array( 'breadcrumb' => true,
                    'scripts' => array(
                            '<script src="/assets/js/highlight.pack.js">'
                    )));
            ?>
        </div>
    </body>
    <footer>
        <script>hljs.initHighlightingOnLoad();</script>
    </footer>
</html>

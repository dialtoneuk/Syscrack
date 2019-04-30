<?php

    use Framework\Application\Settings;
    use Framework\Application\Render;
?>
<html lang="en">

    <?php

        Render::view('developer/templates/template.header', array( 'pagetitle' => 'Developer / Connection / Creator'));
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
                            Render::view('syscrack/templates/template.alert', array( 'message' => Settings::getSetting('alert_success_message'), 'alert_type' => 'alert-success' ) );
                    ?>
                </div>
            </div>
            <?php
                if( Settings::getSetting('database_encrypt_connection') == false )
                {

                    ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-warning">
                                <div class="panel-heading">
                                    Encryption is turned off
                                </div>
                                <div class="panel-body">
                                    Your database encryption setting is currently turned off, this means that the information you give
                                    below will not be encrypted and will be viewable in plain-text from anybody with root access. This
                                    setting should only be used is mcrypt is not functioning correctly on your system.
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            ?>
            <div class="row">
                <div class="col-md-6">
                    <h5 style="color: #ababab" class="text-uppercase">
                        Connection Creator
                    </h5>
                    <p class="lead">
                        This tool is used to create a new database connection file which is read by the framework
                        when accessing your associated database. This file is encrypted by default for added
                        security.
                    </p>
                    <p>
                        To use this, enter your associated database <b>username, password, host and database</b> into the
                        associated boxed, and then press submit. An error will appear if the tool has been unsuccessful.
                    </p>
                    <p>
                        If you would like to test your connection, <a href="/developer/connection/">head to the connection page.</a>
                    </p>
                </div>
                <div class="col-md-6">
                    <h5 style="color: #ababab" class="text-uppercase">
                        Settings
                    </h5>

                    <?php

                        Render::view('developer/templates/template.form', array('form_elements' => [
                            [
                                'type'          => 'text',
                                'name'          => 'username',
                                'placeholder'   => 'Username'
                            ],
                            [
                                'type'          => 'password',
                                'name'          => 'password',
                                'placeholder'   => 'Password'
                            ],
                            [
                                'type'          => 'text',
                                'name'          => 'host',
                                'placeholder'   => 'Host'
                            ],
                            [
                                'type'          => 'text',
                                'name'          => 'database',
                                'placeholder'   => 'Database'
                            ]
                        ]));
                    ?>
                </div>
            </div>

            <?php

                Render::view('developer/templates/template.footer', array( 'breadcrumb' => true ));
            ?>
        </div>
    </body>
</html>

<html lang="en">

    <?php

        Flight::render('developer/templates/template.header', array( 'pagetitle' => 'Connection Creator'));
    ?>
    <body>
        <div class="container">

            <?php

                Flight::render('developer/templates/template.navigation');
            ?>
            <div class="row">
                <div class="col-lg-12">
                    <?php

                        if( isset( $_GET['error'] ) )
                            Flight::render('syscrack/templates/template.alert', array( 'message' => $_GET['error'] ) );
                        elseif( isset( $_GET['success'] ) )
                            Flight::render('syscrack/templates/template.alert', array( 'message' => Settings::getSetting('alert_success_message'), 'alert_type' => 'alert-success' ) );
                    ?>
                </div>
            </div>
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
                        If you would like to test your connection, <a href="/developer/connectiontester/">head to the <b>Connection Tester</b> tool.</a>
                    </p>
                </div>
                <div class="col-md-6">
                    <h5 style="color: #ababab" class="text-uppercase">
                        Settings
                    </h5>

                    <?php

                        Flight::render('developer/templates/template.form', array('form_elements' => [
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

                Flight::render('developer/templates/template.footer', array( 'breadcrumb' => true ));
            ?>
        </div>
    </body>
</html>

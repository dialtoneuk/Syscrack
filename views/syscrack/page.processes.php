<?php
    use Framework\Application\Container;

    $session = Container::getObject('session');

    if( $session->isLoggedIn() )
    {

        $session->updateLastAction();
    }
?>
<html>
    <?php

        Flight::render('syscrack/templates/template.header', array('pagetitle' => 'Syscrack | Processes'));
    ?>
    <body>
        <div class="container">
            <?php

                Flight::render('syscrack/templates/template.navigation');
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

            <?php

                Flight::render('syscrack/templates/template.footer', array('breadcrumb' => true ) );
            ?>
        </div>
    </body>
</html>

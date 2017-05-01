<?php

    use Framework\Application\Container;
    use Framework\Application\Settings;

    $session = Container::getObject('session');

if( $session->isLoggedIn() )
{

    $session->updateLastAction();

    Flight::redirect('/game/');

    exit;
}
?>

<!DOCTYPE html>
<html>

    <?php

        Flight::render('syscrack/templates/template.header', array('pagetitle' => 'Syscrack | Register') );
    ?>
    <body>
        <div class="container">

            <div class="row" style="margin-top: 2.5%;">
                <div class="col-lg-12">
                    <?php

                        if( isset( $_GET['error'] ) )
                            Flight::render('syscrack/templates/template.alert', array( 'message' => $_GET['error'] ) );
                        elseif( isset( $_GET['success'] ) )
                            Flight::render('syscrack/templates/template.alert', array( 'message' => 'Success', 'alert_type' => 'alert-success' ) );
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="page-header">
                        <h1>Register</h1>
                    </div>
                    <form method="post">

                        <?php

                            if( Settings::getSetting('user_require_betakey') )
                            {

                                Flight::render('syscrack/templates/template.form', array('form_elements' => [
                                    [
                                        'type'          => 'text',
                                        'name'          => 'username',
                                        'placeholder'   => 'Username',
                                        'icon'          => 'glyphicon-user'
                                    ],
                                    [
                                        'type'          => 'password',
                                        'name'          => 'password',
                                        'placeholder'   => 'Password',
                                        'icon'          => 'glyphicon-lock'
                                    ],
                                    [
                                        'type'          => 'email',
                                        'name'          => 'email',
                                        'placeholder'   => 'Email',
                                        'icon'          => 'glyphicon-envelope'
                                    ],
                                    [
                                        'type'          => 'text',
                                        'name'          => 'betakey',
                                        'placeholder'   => '0001-0002-0003',
                                        'icon'          => 'glyphicon-certificate'
                                    ]
                                ], 'form_submit_label' => 'Login' ));
                            }
                            else
                            {

                                Flight::render('syscrack/templates/template.form', array('form_elements' => [
                                    [
                                        'type'          => 'text',
                                        'name'          => 'username',
                                        'placeholder'   => 'Username',
                                        'icon'          => 'glyphicon-user'
                                    ],
                                    [
                                        'type'          => 'password',
                                        'name'          => 'password',
                                        'placeholder'   => 'Password',
                                        'icon'          => 'glyphicon-lock'
                                    ],
                                    [
                                        'type'          => 'email',
                                        'name'          => 'email',
                                        'placeholder'   => 'Email',
                                        'icon'          => 'glyphicon-envelope'
                                    ],
                                ], 'form_submit_label' => 'Login' ));
                            }
                        ?>
                    </form>
                </div>
            </div>

            <?php

                Flight::render('syscrack/templates/template.footer', array('breadcrumb' => true ));
            ?>
        </div>
    </body>
</html>

<?php

$session = \Framework\Application\Container::getObject('session');

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

        Flight::render('syscrack/templates/template.header', array('pagetitle' => 'Syscrack | Login') );
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
                        <h1>SC:\\login</h1>
                    </div>
                    <form method="post">

                        <?php

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
                                ]
                            ],'form_submit_label' => 'Login'));
                        ?>
                    </form>
                </div>
                <div class="col-lg-6">
                    <div class="page-header">
                        <h1>SC:\\register</h1>
                    </div>
                    <ul class="list-group">
                        <li class="list-group-item"><span class="glyphicon glyphicon-certificate"></span> Hack and infect users with your deadly viruses.</li>
                        <li class="list-group-item"><span class="glyphicon glyphicon glyphicon-usd"></span> Make money to buy better softwares, hardwares and computer customizations.</li>
                        <li class="list-group-item"><span class="glyphicon glyphicon glyphicon-briefcase"></span> Be your own bank and bitcoin exchange.</li>
                        <li class="list-group-item"><span class="glyphicon glyphicon-wrench"></span> Sell hardwares and softwares on your own marketplace.</li>
                        <li class="list-group-item"><span class="glyphicon glyphicon-sunglasses"></span> Completely free and <a href="https://github.com/dialtoneuk/Syscrack2017/">open source</a>, no ads.</li>
                    </ul>
                    <div class="btn-group btn-group-justified" role="group" aria-label="...">
                        <div class="btn-group" role="group">
                            <button type="button" onclick='window.location.href="/register/"' class="btn btn-default">Register</button>
                        </div>
                    </div>
                </div>
            </div>

            <?php

                Flight::render('syscrack/templates/template.footer', array('breadcrumb' => true ));
            ?>
        </div>
    </body>
</html>

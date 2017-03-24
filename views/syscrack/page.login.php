<?php

$session = \Framework\Application\Container::getObject('session');

if( $session->isLoggedIn() )
{

    $session->updateLastAction();

    Flight::redirect('/game/');

    exit;
}
?>
<html>

    <?php

        Flight::render('syscrack/templates/template.header', array('pagetitle' => 'Syscrack | Login') );
    ?>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="page-header">
                        <h1>Login</h1>
                    </div>
                    <form method="post">

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
                                ]
                            ]));
                        ?>
                    </form>
                </div>
            </div>

            <?php

                Flight::render('syscrack/templates/template.footer');
            ?>
        </div>
    </body>
</html>

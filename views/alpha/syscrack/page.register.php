<?php

use Framework\Application\Container;
use Framework\Application\Render;
use Framework\Application\Settings;

$session = Container::getObject('session');

if ($session->isLoggedIn()) {

    $session->updateLastAction();

    Flight::redirect('/game/');

    exit;
}
?>

<!DOCTYPE html>
<html>

<?php

Render::view('syscrack/templates/template.header', array('pagetitle' => 'Syscrack | Register'));
?>
<body>
<div class="container">
    <?php

    Render::view('syscrack/templates/template.navigation');
    ?>
    <div class="row" style="margin-top: 2.5%;">
        <div class="col-sm-12">
            <?php

            if (isset($_GET['error']))
                Render::view('syscrack/templates/template.alert', array('message' => $_GET['error']));
            elseif (isset($_GET['success']))
                Render::view('syscrack/templates/template.alert', array('message' => Settings::getSetting('alert_success_message'), 'alert_type' => 'alert-success'));
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <h5 style="color: #ababab" class="text-uppercase">
                Register
            </h5>
            <form method="post">

                <?php

                if (Settings::getSetting('user_require_betakey')) {

                    Render::view('syscrack/templates/template.form', array('form_elements' => [
                        [
                            'type' => 'text',
                            'name' => 'username',
                            'placeholder' => 'Username',
                            'icon' => 'glyphicon-user'
                        ],
                        [
                            'type' => 'password',
                            'name' => 'password',
                            'placeholder' => 'Password',
                            'icon' => 'glyphicon-lock'
                        ],
                        [
                            'type' => 'email',
                            'name' => 'email',
                            'placeholder' => 'Email',
                            'icon' => 'glyphicon-envelope'
                        ],
                        [
                            'type' => 'text',
                            'name' => 'betakey',
                            'placeholder' => '0001-0002-0003',
                            'icon' => 'glyphicon-certificate'
                        ]
                    ], 'form_submit_label' => 'Register'));
                } else {

                    Render::view('syscrack/templates/template.form', array('form_elements' => [
                        [
                            'type' => 'text',
                            'name' => 'username',
                            'placeholder' => 'Username',
                            'icon' => 'glyphicon-user'
                        ],
                        [
                            'type' => 'password',
                            'name' => 'password',
                            'placeholder' => 'Password',
                            'icon' => 'glyphicon-lock'
                        ],
                        [
                            'type' => 'email',
                            'name' => 'email',
                            'placeholder' => 'Email',
                            'icon' => 'glyphicon-envelope'
                        ],
                    ], 'form_submit_label' => 'Register'));
                }
                ?>
            </form>
        </div>
        <div class="col-sm-6">
            <h5 style="color: #ababab" class="text-uppercase">
                Why Cant I Play?
            </h5>
            <div class="well">
                <iframe width="100%" height="100%" src="https://www.youtube.com/embed/DrxJCOVsV1E?ecver=1"
                        frameborder="0" allowfullscreen></iframe>
            </div>
            <p>
                We are currently setting up the final steps on making your experience awesome, <a
                        href="https://discordapp.com/invite/yezxfN3">please join our discord
                    for updates direct from the mouth of the developers.</a>
            </p>
        </div>
    </div>

    <?php

    Render::view('syscrack/templates/template.footer', array('breadcrumb' => true));
    ?>
</div>
</body>
</html>

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
    <div class="row">
        <div class="col-lg-12">
            <img style="width: 100%; height: 100%; border-radius: 5px;" src="<?=Render::getAssetsLocation()?>/img/art/art_login.png">
        </div>
    </div>
    <div class="row" style="margin-top: 12px">
        <div class="col-sm-4">
            <div class="row">
                <div class="col-sm-12">
                    <div class="panel panel-default">
                        <div class="panel-body text-center">
                            <h1>
                                <span class="glyphicon glyphicon-credit-card"></span>
                            </h1>
                            <p>
                                Get filthy rich distributing viruses and hacking into banks and then spend your hard
                                earnings on expensive upgrades for your arsenal.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="panel panel-default">
                        <div class="panel-body text-center">
                            <h1>
                                <span class="glyphicon glyphicon-save-file"></span>
                            </h1>
                            <p>
                                Amass a collection of rare and legendary tools to aide you in your quest to hack the
                                planet. Store them safe on multiple computers which are apart of your network.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="row">
                <div class="col-sm-12">
                    <?php

                    if (isset($_GET['error']))
                        Render::view('syscrack/templates/template.alert', array('message' => $_GET['error']));
                    elseif (isset($_GET['success']))
                        Render::view('syscrack/templates/template.alert', array('message' => Settings::getSetting('alert_success_message'), 'alert_type' => 'alert-success'));
                    ?>
                </div>
            </div>
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
            <div class="btn-group btn-group-justified" role="group" aria-label="..." style="margin-top: 12px;">
                <div class="btn-group" role="group">
                    <button type="button" onclick='window.location.href="/login/"' class="btn btn-primary">Already have an account?
                    </button>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="row">
                <div class="col-sm-12">
                    <div class="panel panel-default">
                        <div class="panel-body text-center">
                            <h1>
                                <span class="glyphicon glyphicon-search"></span>
                            </h1>
                            <p>
                                Explore an entire virtual internet and uncover mysteries and conspiracies deep in a
                                servers file structure. Uncover the truth of the global surveillance program.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="panel panel-default">
                        <div class="panel-body text-center">
                            <h1>
                                <span class="glyphicon glyphicon-fire"></span>
                            </h1>
                            <p>
                                Crush your enemies with the power of your bot net. Spread deadly worms which infect every machine
                                it touches and build your army.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php

    Render::view('syscrack/templates/template.footer', array('breadcrumb' => true));
    ?>
</div>
</body>
</html>

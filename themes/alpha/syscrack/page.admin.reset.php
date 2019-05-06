<?php

use Framework\Application\Container;
use Framework\Application\Render;
use Framework\Application\Settings;
use Framework\Syscrack\Game\Utilities\PageHelper;
use Framework\Syscrack\User;

$session = Container::getObject('session');

if ($session->isLoggedIn()) {

    $session->updateLastAction();
}

if (isset($user) == false) {

    $user = new User();
}

if (isset($pagehelper) == false) {

    $pagehelper = new PageHelper();
}
?>
<html>

<?php

Render::view('syscrack/templates/template.header', array('pagetitle' => 'Syscrack | Admin'));
?>
<body>
<div class="container">

    <?php

    Render::view('syscrack/templates/template.navigation');
    ?>
    <div class="row">
        <div class="col-sm-12">
            <?php

            if (isset($_GET['error']))
                Render::view('syscrack/templates/template.alert', array('message' => $_GET['error']));
            elseif (isset($_GET['success']))
                Render::view('syscrack/templates/template.alert', array('message' => $settings['alert_success_message'], 'alert_type' => 'alert-success'));
            ?>
        </div>
    </div>
    <form method="post">
        <div class="row">

            <?php

            Render::view('syscrack/templates/template.admin.options');
            ?>
            <div class="col-sm-8">
                <div class="row">
                    <div class="col-sm-4">
                        <h5 style="color: #ababab" class="text-uppercase">
                            Reset
                        </h5>

                        <?php
                        Render::view('syscrack/templates/template.form', array('form_elements' => [
                            [
                                'type' => 'checkbox',
                                'name' => 'clearfinance',
                                'icon' => 'glyphicon-globe',
                            ]
                        ], 'remove_submit' => true, 'remove_form' => true));
                        ?>
                    </div>
                    <div class="col-sm-8">
                        <h5 style="color: #ababab" class="text-uppercase">
                            How does this work?
                        </h5>
                        <p>
                            Simply press the reset button below and the game will be reset to square one. The game
                            will look for any computers with a schema file, and use that when resetting.
                            Clear banks will also clear all of the bank accounts currently on the system, resetting
                            everybody to nothing.
                        </p>
                        <p>
                            Please allow Syscrack a few minutes to do this process, depending on how many computers are
                            on your system, this
                            could take a long time. But it normally does not.
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="btn-group btn-group-justified" role="group" aria-label="...">
                            <div class="btn-group" role="group">
                                <button type="submit" class="btn btn-default">Reset Game</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <?php

    Render::view('syscrack/templates/template.footer', array('breadcrumb' => true));
    ?>
</div>
</body>
</html>

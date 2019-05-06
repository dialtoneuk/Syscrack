<?php

use Framework\Application\Container;
use Framework\Application\Render;
use Framework\Application\Settings;
use Framework\Syscrack\Game\Computer;
use Framework\Syscrack\Game\Utilities\PageHelper;

$computer_controller = new Computer();

$pagehelper = new PageHelper();

$session = Container::getObject('session');

if ($session->isLoggedIn()) {

    $session->updateLastAction();
}

$currentcomputer = $computer_controller->getComputer($computer_controller->getCurrentUserComputer());
?>

<!DOCTYPE html>
<html>

<?php

Render::view('syscrack/templates/template.header', array('pagetitle' => 'Syscrack | Game'));
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
                Render::view('syscrack/templates/template.alert', array('message' => $settings['alert_success_message'], 'alert_type' => 'alert-success') );
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <h5 style="color: #ababab" class="text-uppercase">
                <span class="badge"><?= $currentcomputer->type ?></span> <?= $currentcomputer->ipaddress ?>
            </h5>
        </div>
    </div>
    <div class="row" style="margin-top: 1.5%;">
        <?php

        Render::view('syscrack/templates/template.computer.actions', array('computer_controller' => $computer_controller));
        ?>

        <div class="col-md-8">

            <?php

            Render::view('syscrack/templates/template.softwares', array('ipaddress' => $currentcomputer->ipaddress, 'computer_controller' => $computer_controller, 'hideoptions' => false, "local" => true));
            ?>
        </div>
    </div>

    <?php

    Render::view('syscrack/templates/template.footer', array('breadcrumb' => true));
    ?>
</div>
</body>
</html>
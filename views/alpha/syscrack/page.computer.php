<?php

use Framework\Application\Container;
use Framework\Application\Render;
use Framework\Application\Settings;
use Framework\Syscrack\Game\Computers;
use Framework\Syscrack\Game\Utilities\PageHelper;

$computers = new Computers();

$pagehelper = new PageHelper();

$session = Container::getObject('session');

if ($session->isLoggedIn()) {

    $session->updateLastAction();
}

$currentcomputer = $computers->getComputer($computers->getCurrentUserComputer());
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
        <div class="col-lg-12">
            <?php

            if (isset($_GET['error']))
                Render::view('syscrack/templates/template.alert', array('message' => $_GET['error']));
            elseif (isset($_GET['success']))
                Render::view('syscrack/templates/template.alert', array('message' => Settings::getSetting('alert_success_message'), 'alert_type' => 'alert-success'));
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <h5 style="color: #ababab" class="text-uppercase">
                <span class="badge"><?= $currentcomputer->type ?></span> <?= $currentcomputer->ipaddress ?>
            </h5>
        </div>
    </div>
    <div class="row" style="margin-top: 1.5%;">
        <?php

        Render::view('syscrack/templates/template.computer.actions', array('computers' => $computers));
        ?>

        <div class="col-md-8">

            <?php

            Render::view('syscrack/templates/template.softwares', array('ipaddress' => $currentcomputer->ipaddress, 'computers' => $computers, 'hideoptions' => false, "local" => true));
            ?>
        </div>
    </div>

    <?php

    Render::view('syscrack/templates/template.footer', array('breadcrumb' => true));
    ?>
</div>
</body>
</html>
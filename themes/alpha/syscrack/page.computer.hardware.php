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

$currentcomputer = $computer_controller->getComputer($computer_controller->computerid());
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
        <div class="col-sm-12" onclick="window.location.href = '/computer/'">
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

            <div class="row">
                <div class="col-sm-12">
                    <?php

                    $hardwares = $computer_controller->getComputerHardware($currentcomputer->computerid);

                    foreach ($hardwares as $type => $hardware) {

                        $icons = $settings['syscrack_hardware_icons'];

                        ?>
                        <div class="panel panel-info">
                            <div class="panel-heading">
                                <?= $type ?>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-sm-2">
                                        <?php

                                        if (isset($icons[$type])) {

                                            ?>
                                            <h1>
                                                <span class="glyphicon <?= $icons[$type] ?>"></span>
                                            </h1>
                                            <?php
                                        } else {

                                            ?>
                                            <h1>
                                                <span class="glyphicon glyphicon-question-sign"></span>
                                            </h1>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                    <div class="col-sm-10">
                                        <h1>
                                            <?php

                                            if (isset($hardware['value'])) {

                                                echo (string)$hardware['value'];
                                            }

                                            $extensions = $settings['syscrack_hardware_extensions'];

                                            if (isset($extensions[$type])) {

                                                ?>
                                                <span class="small"><?= $extensions[$type] ?></span>
                                                <?php
                                            }
                                            ?>
                                        </h1>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-body">
                    This is really simple for now as we are in beta, if you are looking to buy new hardwares, check out
                    the <a href="/game/internet/">whois for a market.</a>
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
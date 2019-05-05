<?php

use Framework\Application\Container;
use Framework\Application\Render;
use Framework\Syscrack\Game\Computer;
use Framework\Syscrack\Game\Operations;
use Framework\Syscrack\Game\Utilities\PageHelper;

$computer_controller = new Computer();

$pagehelper = new PageHelper();

$operations = new Operations();

$session = Container::getObject('session');

if ($session->isLoggedIn()) {

    $session->updateLastAction();
}

$currentcomputer = $computer_controller->getComputer($computer_controller->getCurrentUserComputer());

$processes = $operations->getComputerProcesses($currentcomputer->computerid);
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
                Render::view('syscrack/templates/template.alert', array('message' => Settings::getSetting('alert_success_message'), 'alert_type' => 'alert-success'));
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

            <?php

            if (empty($processes) == false) {

                foreach ($processes as $process) {

                    ?>
                    <div class="row">
                        <?php Render::view('syscrack/templates/template.process', array('processid' => $process->processid, 'processcclass' => $operations->findProcessClass($process->process), 'refresh' => true)); ?>
                    </div>
                    <?php
                }
            } else {

                ?>
                <div class="panel panel-danger">
                    <div class="panel-heading">
                        Notice
                    </div>
                    <div class="panel-body">
                        Computer currently has no processes, maybe you should hack something?
                    </div>
                </div>
                <?php
            }
            ?>
            <div class="row">
                <div class="col-sm-12">
                    <div class="panel panel-info">
                        <div class="panel-body">
                            Head over to the <a href="/processes/computer/<?=$currentcomputer->computerid?>">process control panel</a> to edit your current
                            tasks!
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
<?php

use Framework\Application\Render;
use Framework\Application\Settings;

$session = \Framework\Application\Container::getObject('session');

if ($session->isLoggedIn()) {

    $session->updateLastAction();
}

$pagehelper = new \Framework\Syscrack\Game\Utilities\PageHelper();

if (isset($operations) == false) {

    $operations = new \Framework\Syscrack\Game\Operations();
}

if (isset($computer_controller) == false) {

    $computer_controller = new \Framework\Syscrack\Game\Computer();
}
?>




<!DOCTYPE html>
<html>

<?php

Render::view('syscrack/templates/template.header', array('pagetitle' => 'Syscrack | Processes'));
?>
<body>
<div class="container">
	<?php
		Render::view('syscrack/templates/template.navigation');
		Render::view('syscrack/templates/template.errors');
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
    <div class="row">
        <div class="col-sm-3">
            <div class="list-group">
                <a href="/processes/" class="list-group-item active">
                    <h4 class="list-group-item-heading">View All Processes</h4>
                    <p class="list-group-item-text">Processes which are faded out are of
                        other machines different to your current active machine you are
                        viewing this page from. You will need to switch to this VPC by clicking
                        the badge to the right of the IP Address in order to make changes to
                        these processes.</p>
                </a>
            </div>
            <div class="list-group">
                <a href="/processes/computer/<?= $computerid ?>" class="list-group-item">
                    <h4 class="list-group-item-heading">View Machine Processes</h4>
                    <p class="list-group-item-text">View and edit a specific machines processes. This will by
                        default show the processes of your current machine.</p>
                </a>
            </div>
        </div>
        <div class="col-sm-9">
            <?php

            if ( empty( $processes ) )
            {

                ?>
                    <div class="panel panel-danger">
                        <div class="panel-body">
                            You currently don't have any processes on any of your machines, go do something!
                        </div>
                    </div>
                <?php
            }
            else
            {

                foreach ($processes as $key => $value) {

                    $computer = $computer_controller->getComputer($key);

                    ?>
                    <div class="row">
                        <div class="col-sm-12">
                            <h5 style="color: #ababab" class="text-uppercase">
                                <?php

                                if ($computer_controller->computerid() == $computer->computerid) {

                                    ?>
                                    <p><span>This is your current computer</span> <a href="/processes/computer/<?= $computerid ?>" class="btn btn-info"
                                                                                     role="button" style="float: right; margin-top: -10px; padding-bottom: 5px;">Open Task Manager</a></p>
                                    <?php
                                } else {

                                    ?>
                                    <p><span><?=$computer->ipaddress?></span> <a href="/game/computer/#<?= $computer->ipaddress ?>" class="btn btn-warning"
                                                                                     role="button" style="float: right; margin-top: -10px; padding-bottom: 5px;">Switch to computer</a></p>
                                    <?php
                                }
                                ?>

                            </h5>

                            <?php
                            foreach ($value as $item => $process) {

                                if ($computer_controller->computerid() == $computer->computerid) {

                                    ?>
                                    <div class="row">
                                        <?php Render::view('syscrack/templates/template.process', array('panel' => 'panel-info', 'processid' => $process->processid, 'processcclass' => $operations->findProcessClass($process->process))); ?>
                                    </div>
                                    <?php
                                } else {

                                    ?>
                                    <div class="row" style="opacity: 0.25;">
                                        <?php Render::view('syscrack/templates/template.process', array('panel' => 'panel-warning','processid' => $process->processid, 'processcclass' => $operations->findProcessClass($process->process))); ?>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                            <p style="margin-top: -5%; font-size: 10px;"><?= count($value) ?> processes in total.</p>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>
        </div>
    </div>
    <?php

    Render::view('syscrack/templates/template.footer', array('breadcrumb' => true));
    ?>
</div>
</body>
</html>
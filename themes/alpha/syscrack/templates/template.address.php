<?php

use Framework\Application\Container;
use Framework\Syscrack\Game\Computer;
use Framework\Syscrack\Game\Internet;
use Framework\Syscrack\Game\Software;
use Framework\Syscrack\Game\Viruses;

if (isset($computer_controller) == false) {

    $computer_controller = new Computer();
}

if (isset($viruses) == false) {

    $viruses = new Viruses();
}

if (isset($softwares) == false) {

    $softwares = new Software();
}

if (isset($internet) == false) {

    $internet = new Internet();
}

$session = Container::getObject('session');

$computer = $internet->getComputer($value['ipaddress']);
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <span class="badge">address #<?= $key ?></span> <?= $value['ipaddress'] ?> <span style="float: right;"
                                                                                         class="badge"><?= $computer_controller->type ?></span>
    </div>
    <div class="panel-body">
        <?php
        if ($viruses->hasVirusesOnComputer($computer_controller->computerid, $session->getSessionUser())) {

            $virus = $viruses->getVirusesOnComputer( $computer_controller->computerid, $session->getSessionUser());

            ?>
                <?php

                if (empty( $virus ) ) {

                } else {

                    foreach ( $virus as $key=>$penis )
                    {
                        if ($softwares->softwareExists($penis->softwareid)) {

                            $software = $softwares->getSoftware($penis->softwareid);

                            if ( $software->installed == false )
                            {

                                continue;
                            }

                            ?>
                            <p class="text-center">
                                You currently have a <?= $software->softwarename ?> (<?= $software->level ?>) installed on this computer, it was
                                last collected <?= date('D M j G:i:s Y', $software->lastmodified) ?>
                            </p>
                            <?php
                        }
                    }
                }
                ?>
            </>
            <?php
        }
        ?>
        <button class="btn btn-default" style="width: 100%" type="button" data-toggle="collapse"
                data-target="#computer_<?= $computer_controller->computerid ?>" aria-expanded="false"
                aria-controls="computer_<?= $computer_controller->computerid ?>">
            View
        </button>
        <div class="collapse" id="computer_<?= $computer_controller->computerid ?>">
            <div class="panel panel-default" style="margin-top: 3.5%;">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="btn-group btn-group-justified" role="group" aria-label="...">
                                <div class="btn-group" role="group"
                                     onclick="window.location.href = '/game/internet/<?= $value['ipaddress'] ?>/'">
                                    <button type="button" class="btn btn-default">Goto</button>
                                </div>
                                <div class="btn-group" role="group"
                                     onclick="window.location.href = '/game/internet/<?= $value['ipaddress'] ?>/login'">
                                    <button type="button" class="btn btn-default">Login</button>
                                </div>
                                <div class="btn-group" role="group"
                                     onclick="window.location.href = '/game/internet/<?= $value['ipaddress'] ?>/login'">
                                    <button type="button" class="btn btn-danger">Delete</button>
                                </div>
                            </div>
                            <div style="margin-top: 2.5%" class="panel panel-default">
                                <div class="panel-heading">
                                    Hack Information
                                </div>
                                <div class="panel-body">
                                    Added <?= date('Y/m/d H:m:s', $value['timehacked']) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
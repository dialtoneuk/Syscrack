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
                                                                                         class="badge"><?= $computer->type ?></span>
    </div>
    <div class="panel-body">
        <?php
        if ($viruses->hasVirusesOnComputer($computer->computerid, $session->userid())) {

            $virus = $viruses->getVirusesOnComputer( $computer->computerid, $session->userid());

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
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="panel panel-info">
                                        <div class="panel-body">
                                            You currently have a <?= $software->softwarename ?> ( lvl <?= $software->level ?> ) installed on this computer. <small style="float: right;">It was
                                                last collected <?= date('D M j G:i:s Y', $software->lastmodified) ?></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    }
                }
                ?>
            </>
            <?php
        }
        ?>
        <button class="btn btn-info" style="width: 100%" type="button" data-toggle="collapse"
                data-target="#computer_<?= $computer->computerid ?>" aria-expanded="false"
                aria-controls="computer_<?= $computer->computerid ?>">
            View
        </button>
        <div class="collapse" id="computer_<?= $computer->computerid ?>">
            <div class="panel panel-default" style="margin-top: 12px">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="btn-group btn-group-justified" role="group" aria-label="...">
                                <div class="btn-group" role="group"
                                     onclick="window.location.href = '/game/internet/<?= $value['ipaddress'] ?>/'">
                                    <button type="button" class="btn btn-info">Goto</button>
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
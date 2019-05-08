<?php

use Framework\Application\Container;
use Framework\Syscrack\Game\Internet;
use Framework\Syscrack\Game\Log;

if (isset($internet) == false) {

    $internet = new Internet();
}

if (isset($log) == false) {

    $log = new Log();
}
?>

<div class="row">
    <div class="col-sm-12">
        <?php

        $connectedcomputer = $internet->getComputer($ipaddress);

        if ($log->hasLog($connectedcomputer->computerid) == false) {

            ?>

            <div class="panel panel-danger">
                <div class="panel panel-heading">
                    Missing Log
                </div>
                <div class="panel panel-body">
                    <p>
                        The log you are currently trying to view appears to be missing, please tell a developer as this
                        is usually a server side issue.
                    </p>
                    <ul class="list-group">
                        <li class="list-group-item">
                            Current Computer <span class="badge right"><?= $connectedcomputer->computerid ?></span>
                        </li>
                        <li class="list-group-item">
                            Userid <span
                                    class="badge right"><?= Container::getObject('session')->userid() ?></span>
                        </li>
                    </ul>
                </div>
            </div>
            <?php
        } else {

            ?>
            <div class="well">
                <textarea readonly id="log" name="log"
                          style="width: 100%; height: 400px; resize: none; font-size: 14px; padding: 2.5%;"><?php $log = array_reverse($log->getCurrentLog($connectedcomputer->computerid));
                    foreach ($log as $key => $value) {
                        echo '[', $value['ipaddress'] . '] ' . strftime("%d-%m-%Y %H:%M:%S", $value['time']) . ' : ' . $value['message'] . "\n";
                    } ?></textarea>
            </div>

            <div class="btn-group-vertical" style="width: 100%">
                <?php

                if (isset($hideoptions)) {

                    if ($hideoptions == false) {

                        ?>

                        <button class="btn btn-danger" type="submit"
                                onclick="window.location.href = '/game/internet/<?= $ipaddress ?>/clear'">
                            <span class="glyphicon glyphicon-alert" aria-hidden="true"></span> Clear Log
                        </button>
                        <button class="btn btn-success" type="button"
                                onclick="window.location.href = '/game/internet/<?= $ipaddress ?>'">
                            <span class="glyphicon glyphicon-circle-arrow-down" aria-hidden="true"></span> Refresh Log
                        </button>
                        <?php
                    }
                } else {

                    ?>

                    <button class="btn btn-danger" type="submit">
                        <span class="glyphicon glyphicon-alert" aria-hidden="true"></span> Clear Log
                    </button>
                    <button class="btn btn-success" type="button"
                            onclick="window.location.href = '/game/internet/<?= $ipaddress ?>'">
                        <span class="glyphicon glyphicon-circle-arrow-down" aria-hidden="true"></span> Refresh Log
                    </button>
                    <?php
                }
                ?>
            </div>
            <?php
        }
        ?>
    </div>
</div>
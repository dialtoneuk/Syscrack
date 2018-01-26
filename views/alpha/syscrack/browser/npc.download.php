<?php

use Framework\Syscrack\Game\Computers;
use Framework\Syscrack\Game\Internet;
use Framework\Syscrack\Game\Schema;
use Framework\Syscrack\Game\Softwares;

if (isset($computers) == false) {

    $computers = new Computers();
}

if (isset($software) == false) {

    $software = new Softwares();
}

if (isset($internet) == false) {

    $internet = new Internet();
}

if (isset($schema)) {

    $schema = new Schema();
}

$current_computer = $internet->getComputer($ipaddress);
?>

<div class="row">
    <div class="col-lg-12">
        <h5 style="color: #ababab" class="text-uppercase">
            <?php
            if ($schema->hasSchema($current_computer->computerid)) {

                $schema = $schema->getSchema($current_computer->computerid);

                if (isset($schema['name'])) {

                    echo $schema['name'];
                }
            } else {

                echo 'Download Server';
            }
            ?>
        </h5>
        <p>
            Free anonymous downloads! Download away!
        </p>

        <ul class="list-group">
            <?php

            $computersoftware = $computers->getComputerSoftware($internet->getComputer($ipaddress)->computerid);

            if (empty($computersoftware)) {

                ?>
                <div class="panel panel-warning">
                    <div class="panel-body">
                        No softwares are currently available to download.. sorry!
                    </div>
                </div>
                <?php
            } else {

                foreach ($computersoftware as $key => $value) {

                    if ($software->softwareExists($value['softwareid']) == false) {

                        continue;
                    }

                    $value = $software->getSoftware($value['softwareid']);

                    if (empty($value->data)) {

                        continue;
                    }

                    $data = json_decode($value->data, true);

                    if (isset($data['allowanondownloads']) == false) {

                        continue;
                    }

                    ?>
                    <li class="list-group-item">
                        <a href="/game/internet/<?= $ipaddress ?>/anondownload/<?= $value->softwareid ?>"><?= $value->softwarename . $software->getSoftwareExtension($software->getSoftwareNameFromSoftwareID($value->softwareid)) . ' ' . $value->size . 'mb (' . $value->level . ')' ?></a>
                    </li>
                    <?php
                }
            }
            ?>
        </ul>
    </div>
</div>
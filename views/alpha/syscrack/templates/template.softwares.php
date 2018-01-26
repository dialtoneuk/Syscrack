<?php

use Framework\Application\Settings;

if (isset($softwares) == null) {

    $softwares = new \Framework\Syscrack\Game\Softwares();
}

if (isset($computers) == null) {

    $computers = new \Framework\Syscrack\Game\Computers();
}

if (isset($internet) == null) {

    $internet = new \Framework\Syscrack\Game\Internet();
}

if (isset($viruses) == false) {

    $viruses = new \Framework\Syscrack\Game\Viruses();
}
?>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                C:\\
            </div>

            <table class="table">
                <thead>
                <tr>
                    <th><span class="glyphicon glyphicon-question-sign"></span></th>
                    <th>Name</th>
                    <th>Level</th>
                    <th>Size</th>

                    <?php

                    if (isset($hideoptions)) {

                        if ($hideoptions == false) {

                            ?>

                            <th>Options</th>
                            <?php
                        }
                    }
                    ?>
                </tr>
                </thead>

                <tbody>
                <?php

                $software = $computers->getComputerSoftware($internet->getComputer($ipaddress)->computerid);

                foreach ($software as $key => $value) {

                    if ($softwares->softwareExists($value['softwareid']) == false) {

                        continue;
                    }

                    if ($computers->hasSoftware($internet->getComputer($ipaddress)->computerid, $value['softwareid']) == false) {

                        continue;
                    }

                    $softwareclass = $softwares->getSoftwareClassFromID($value['softwareid']);

                    $software = $softwares->getSoftware($value['softwareid']);
                    ?>
                    <tr>
                        <td data-toggle="tooltip" data-placement="auto" title="<?= $value['type'] ?>"
                            style="padding-top: 2.5%;">
                            <?php

                            if ($softwares->hasIcon($software->softwareid)) {

                                ?>
                                <span class="glyphicon <?= $softwares->getIcon($software->softwareid) ?>"></span>
                                <?php
                            } else {

                                ?>
                                <span class="glyphicon glyphicon-question-sign"></span>
                                <?php
                            }
                            ?>
                        </td>
                        <td style="padding-top: 2.25%;">
                            <?php

                            if ($software->installed) {

                                if ($software->userid == \Framework\Application\Container::getObject('session')->getSessionUser()) {

                                    ?>
                                    <strong><u><?= $software->softwarename . $softwareclass->configuration()['extension'] ?></u></strong>
                                    <?php
                                } else {

                                    ?>
                                    <strong><?= $software->softwarename . $softwareclass->configuration()['extension'] ?></strong>
                                    <?php
                                }
                            } else {

                                if ($software->userid == \Framework\Application\Container::getObject('session')->getSessionUser()) {

                                    ?>
                                    <span style="color: grey"><u><?= $software->softwarename . $softwareclass->configuration()['extension'] ?></u></span>
                                    <?php
                                } else {

                                    ?>
                                    <span style="color: grey"><?= $software->softwarename . $softwareclass->configuration()['extension'] ?></span>
                                    <?php
                                }
                            }
                            ?>
                        </td>
                        <td style="padding-top: 2.25%;">
                            <?php

                            if ($software->level >= Settings::getSetting('syscrack_software_level_godlike')) {

                                ?>
                                <strong style="color: rebeccapurple;">
                                    <?= $software->level ?>
                                </strong>
                                <?php
                            } elseif ($software->level >= Settings::getSetting('syscrack_software_level_expert')) {

                                ?>
                                <strong style="color: limegreen;">
                                    <?= $software->level ?>
                                </strong>
                                <?php
                            } elseif ($software->level >= Settings::getSetting('syscrack_software_level_advanced')) {

                                ?>
                                <strong style="color: indianred;">
                                    <?= $software->level ?>
                                </strong>
                                <?php
                            } else {

                                ?>
                                <p>
                                    <?= $software->level ?>
                                </p>
                                <?php
                            }
                            ?>
                        </td>
                        <td style="padding-top: 2.25%;">
                            <?= $software->size ?>MB
                        </td>
                        <?php

                        if (isset($hideoptions) == false || $hideoptions == false) {

                            ?>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                        <span class="glyphicon glyphicon-cog"></span>
                                    </button>
                                    <ul class="dropdown-menu">

                                        <?php
                                        if (isset($local) == false || $local == false) {
                                            ?>
                                            <li>
                                                <a href="/game/internet/<?= $ipaddress ?>/download/<?= $value['softwareid'] ?>">Download</a>
                                            </li>
                                            <?php
                                        }
                                        ?>


                                        <?php
                                        if ($software->installed) {

                                            if (isset($local) == true && $local == true) {

                                                ?>

                                                <li><a href="/computer/actions/uninstall/<?= $value['softwareid'] ?>">Uninstall</a>
                                                </li>
                                                <li><a href="/computer/actions/execute/<?= $value['softwareid'] ?>">Execute</a>
                                                </li>
                                                <?php
                                            } else {

                                                ?>

                                                <li>
                                                    <a href="/game/internet/<?= $ipaddress ?>/uninstall/<?= $value['softwareid'] ?>">Uninstall</a>
                                                </li>
                                                <li>
                                                    <a href="/game/internet/<?= $ipaddress ?>/execute/<?= $value['softwareid'] ?>">Execute</a>
                                                </li>
                                                <?php
                                            }
                                        } else {

                                            if (isset($local) == true && $local == true) {

                                                ?>

                                                <li><a href="/computer/actions/install/<?= $value['softwareid'] ?>">Install</a>
                                                </li>
                                                <?php
                                            } else {

                                                ?>

                                                <li>
                                                    <a href="/game/internet/<?= $ipaddress ?>/install/<?= $value['softwareid'] ?>">Install</a>
                                                </li>
                                                <?php
                                            }
                                        }

                                        if ($softwares->hasData($value['softwareid'])) {

                                            if (isset($local) == true && $local == true) {

                                                if ($softwares->canView($value['softwareid'])) {


                                                    ?>
                                                    <li><a href="/computer/actions/view/<?= $value['softwareid'] ?>">View</a>
                                                    </li>
                                                    <?php
                                                }
                                            } else {

                                                if ($softwares->canView($value['softwareid'])) {


                                                    ?>
                                                    <li><a href="/computer/actions/view/<?= $value['softwareid'] ?>">View</a>
                                                    </li>
                                                    <?php
                                                }
                                            }
                                        }

                                        if (isset($local) == true && $local == true) {

                                            ?>

                                            <li>
                                                <a href="/computer/actions/delete/<?= $value['softwareid'] ?>">Delete</a>
                                            </li>
                                            <?php
                                        } else {

                                            ?>

                                            <li>
                                                <a href="/game/internet/<?= $ipaddress ?>/delete/<?= $value['softwareid'] ?>">Delete</a>
                                            </li>
                                            <?php
                                        }
                                        ?>
                                    </ul>
                                </div>
                            </td>
                            <?php
                        }
                        ?>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php

use Framework\Application\Settings;
use Framework\Syscrack\Game\Computer;
use Framework\Syscrack\Game\Finance;
use Framework\Syscrack\Game\Internet;
use Framework\Syscrack\Game\Software;
use Framework\Syscrack\Game\Utilities\PageHelper;

if (empty($internet)) {

    $internet = new Internet();
}

if (empty($pagehelper)) {

    $pagehelper = new PageHelper();
}

if (empty($computer)) {

    $computer_controller = new Computer();
}

if (empty($softwares)) {

    $softwares = new Software();
}

$current_computer = $internet->getComputer($ipaddress);
?>
<div class="col-md-4">

    <?php

    if ($computer_controller->getComputer($computer_controller->computerid())->ipaddress == $ipaddress) {

        ?>

        <div class="panel panel-success">
            <div class="panel-body text-center">
                You are currently viewing yourself.
            </div>
        </div>
        <?php
    } elseif ($pagehelper->alreadyHacked($ipaddress)) {

        if ($pagehelper->isCurrentlyConnected($ipaddress) == false) {

            ?>

            <div class="panel panel-success">
                <div class="panel-body text-center">
                    This computer is already in your hacked database.
                </div>
            </div>
            <form action="/game/internet/<?= $ipaddress ?>/login" method="get">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <button style="width: 100%;" class="btn btn-success" type="submit">
                            <span class="glyphicon glyphicon-ok" aria-hidden="true"></span> Login
                        </button>
                    </div>
                </div>
            </form>
            <?php
        } else {

            ?>
            <div class="panel panel-info">
                <div class="panel-heading">
                    C:// Drive
                </div>
                <div class="panel-body">
                    <div class="btn-group" role="group" aria-label="Options" style="width: 100%;">
                        <button type="button" class="btn btn-default" style="width: 100%;" data-toggle="collapse"
                                data-parent="#accordion" href="#collapsehardware" aria-expanded="true"
                                aria-controls="collapsehardware">Show
                        </button>
                    </div>
                    <div id="collapsehardware" class="panel-collapse collapse" role="tabpanel"
                         aria-labelledby="hardware">
                        <div class="well-sm" style="margin-top: 8.75%;">
                            <?php
                            $hardware = json_decode($internet->getComputer($ipaddress)->hardware, true);
                            $csoftwares = json_decode($internet->getComputer($ipaddress)->software, true);
                            ?>

                            <?php
                            $usedspace = 0.0;
                            foreach ($csoftwares as $key => $value) {
                                $usedspace += $softwares->getSoftware($value['softwareid'])->size;
                            }
                            ?>
                            <pre style="white-space:pre-wrap; max-height: 345px;">
                                        Drive size: <?=@$hardware['harddrive']['value'] ?>mb
                                        Used Space: <?= $usedspace ?>mb
                                        Total Free Space: <?=@$hardware['harddrive']['value'] - $usedspace ?>mb
                                    </pre>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            if ($current_computer->type == $settings['syscrack_computers_bank_type']) {

                if (empty($finance)) {

                    $finance = new Finance();
                }


                if ($finance->hasCurrentActiveAccount()) {

                    if ($finance->accountNumberExists($finance->getCurrentActiveAccount())) {

                        if ($finance->getByAccountNumber($finance->getCurrentActiveAccount())->computerid == $current_computer->computerid) {

                            ?>
                            <p>
                                Account #<?= $finance->getCurrentActiveAccount() ?> Options
                            </p>
                            <form action="/game/internet/<?= $ipaddress ?>/remoteadmin">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <button style="width: 100%;" class="btn btn-info" type="submit">
                                            <span class="glyphicon glyphicon-wrench" aria-hidden="true"></span>
                                            Remote Admin
                                        </button>
                                    </div>
                                </div>
                            </form>
                            <?php
                        }
                    }
                } else {

                    ?>
                    <p>
                        Account Hacking Options
                    </p>
                    <form action="/game/internet/<?= $ipaddress ?>/crackaccount" method="post">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon">#</span>
                                    <input type="text" class="form-control" placeholder="000000000"
                                           name="accountnumber" aria-describedby="basic-addon">
                                </div>
                                <button style="width: 100%; margin-top: 2.5%;" class="btn btn-warning"
                                        type="submit">
                                    <span class="glyphicon glyphicon-cog" aria-hidden="true"></span> Crack Account
                                </button>
                            </div>
                        </div>
                    </form>
                    <?php
                }
            }
            ?>
            <p>
                Computer Options
            </p>
            <form action="/game/internet/<?= $ipaddress ?>/upload" method="post">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <select name="softwareid" class="combobox input-sm form-control">
                            <option></option>

                            <?php

                            $computeroftwares = $computer_controller->getComputerSoftware($computer_controller->computerid());

                            if (empty($computeroftwares) == false) {

                                foreach ($computeroftwares as $key => $value) {

                                    if ($softwares->softwareExists($value['softwareid']) == false) {

                                        continue;
                                    }

                                    if ($softwares->isInstalled($value['softwareid'], $computer_controller->computerid())) {

                                        continue;
                                    }

                                    $software = $softwares->getSoftware($value['softwareid']);

                                    $extension = $softwares->getSoftwareExtension($softwares->getSoftwareNameFromSoftwareID($value['softwareid']));

                                    echo('<option value="' . $software->softwareid . '">' . $software->softwarename . $extension . ' ' . $software->size . 'mb (' . $software->level . ')' . '</option>');
                                }

                            }
                            ?>
                        </select>
                        <button style="width: 100%; margin-top: 2.5%;" class="btn btn-primary" type="submit">
                            <span class="glyphicon glyphicon-arrow-up" aria-hidden="true"></span> Upload
                        </button>
                    </div>
                </div>
            </form>
            <form action="/game/internet/<?= $ipaddress ?>/logout">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <button style="width: 100%;" class="btn btn-danger" type="submit">
                            <span class="glyphicon glyphicon-alert" aria-hidden="true"></span> Logout
                        </button>
                    </div>
                </div>
            </form>
            <?php
        }
    } else {

        ?>
        <p>
            Access Options
        </p>
        <?php

        if ($pagehelper->getInstalledCracker() !== null) {

            ?>
            <form action="/game/internet/<?= $ipaddress ?>/hack">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <button style="width: 100%;" class="btn btn-primary" type="submit"
                            <?php if ($pagehelper->getInstalledCracker() == null) {
                                echo 'disabled';
                            } ?> >
                            <span class="glyphicon glyphicon-lock" aria-hidden="true"></span> Hack <span
                                    class="badge"><?= $pagehelper->getInstalledCracker()['level'] ?></span>
                        </button>
                    </div>
                </div>
            </form>
            <?php
        } else {

            ?>

            <div class="panel panel-danger">
                <div class="panel-body">
                    You currently don't have a cracker, you should probably get one...
                </div>
            </div>
            <?php
        }
    }

    if ($current_computer->type == $settings['syscrack_computers_isp_type']) {

        ?>
        <p>
            ISP Options
        </p>
        <form action="/game/internet/<?= $ipaddress ?>/resetaddress" method="post">
            <div class="panel panel-default">
                <div class="panel-body">

                    <?php
                    if (empty($finance)) {

                        $finance = new Finance();
                    }

                    $accounts = $finance->getUserBankAccounts(\Framework\Application\Container::getObject('session')->userid());

                    if (empty($accounts)) {

                        ?>
                        You currently don't have any bank accounts
                        <?php
                    } else {

                        ?>
                        <select name="accountnumber" class="combobox input-sm form-control">
                            <option></option>

                            <?php

                            if (empty($accounts) == false) {

                                foreach ($accounts as $account) {

                                    ?>
                                    <option value="<?= $account->accountnumber ?>">#<?= $account->accountnumber ?>
                                        (<?= $settings['syscrack_currency'] . number_format($account->cash) ?>
                                        ) @<?= $computer_controller->getComputer($account->computerid)->ipaddress ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                        <button style="width: 100%; margin-top: 2.5%;" class="btn btn-info" type="submit">
                            <span class="glyphicon glyphicon-globe" aria-hidden="true"></span> Reset Address
                            (<?= $settings['syscrack_currency'] . number_format($settings['syscrack_operations_resetaddress_price']) ?>
                            )
                        </button>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </form>
        <?php
    }

    if ($current_computer->type == $settings['syscrack_computers_bank_type']) {

        ?>
        <p>
            Remote Bank Options
        </p>
        <form action="/game/internet/<?= $ipaddress ?>/bank">
            <div class="panel panel-default">
                <div class="panel-body">
                    <button style="width: 100%;" class="btn btn-info" type="submit">
                        <span class="glyphicon glyphicon-search" aria-hidden="true"></span> View Account
                    </button>
                </div>
            </div>
        </form>
        <?php
    }

    if ($current_computer->type == $settings['syscrack_computers_market_type']) {

        ?>
        <p>
            Market Options
        </p>
        <form action="/game/internet/<?= $ipaddress ?>/market">
            <div class="panel panel-default">
                <div class="panel-body">
                    <button style="width: 100%;" class="btn btn-info" type="submit">
                        <span class="glyphicon glyphicon-gbp" aria-hidden="true"></span> Shop
                    </button>
                </div>
            </div>
        </form>
        <?php
    }
    ?>
</div>
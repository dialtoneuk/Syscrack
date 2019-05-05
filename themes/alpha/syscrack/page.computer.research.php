<?php

use Framework\Application\Container;
use Framework\Application\Render;
use Framework\Application\Settings;
use Framework\Syscrack\Game\Computer;
use Framework\Syscrack\Game\Finance;
use Framework\Syscrack\Game\Software;
use Framework\Syscrack\User;

if (isset($computer_controller) == false) {

    $computer_controller = new Computer();
}

if (isset($softwares) == false) {

    $softwares = new Software();
}

if (isset($user) == false) {

    $user = new User();
}

if (isset($finance) == false) {

    $finance = new Finance();
}

$session = Container::getObject('session');

if ($session->isLoggedIn()) {

    $session->updateLastAction();
}

$currentcomputer = $computer_controller->getComputer($computer_controller->getCurrentUserComputer());
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

            $licensed = $softwares->getLicensedSoftware($computer_controller->getCurrentUserComputer());

            $accounts = $finance->getUserBankAccounts($session->getSessionUser());
            ?>
            <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" href="#licensedsoftware">Licensed Software</a></li>
                <li><a data-toggle="tab" href="#licensesoftware">License Software</a></li>
                <li><a data-toggle="tab" href="#researchsoftware">Research Software</a></li>
            </ul>
            <div class="tab-content">
                <div id="licensedsoftware" class="tab-pane fade in active" style="padding-top: 2.5%;">

                    <?php

                    if (empty($licensed)) {

                        ?>
                        <div class="panel panel-info">
                            <div class="panel-heading">
                                No Licensed Software
                            </div>
                            <div class="panel-body">
                                You currently have no licensed software on your system, maybe you should license some?
                            </div>
                        </div>
                        <?php
                    } else {

                        foreach ($licensed as $software) {

                            $data = $softwares->getSoftwareData($software->softwareid);

                            if (isset($data['license']) == false) {

                                continue;
                            }

                            if ($user->userExists($data['license']) == false) {

                                continue;
                            }

                            ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <span class="glyphicon <?= $softwares->getIcon($software->softwareid) ?>"></span> <?= $software->softwarename ?>
                                            <span class="badge" style="float: right;"><?= $software->level ?></span>
                                        </div>
                                        <div class="panel-body">
                                            <?php

                                            if ($data['license'] == $session->getSessionUser()) {

                                                ?>
                                                <p class="text-center">
                                                    You currently own this software license
                                                </p>
                                                <?php
                                            }
                                            ?>
                                            <ul class="list-group">
                                                <li class="list-group-item">
                                                    Owner <span
                                                            class="badge right"><?= $user->getUsername($data['license']) ?></span>
                                                </li>
                                                <li class="list-group-item">
                                                    Type <span class="badge right"><?= $software->type ?></span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    }
                    ?>
                </div>
                <div id="licensesoftware" class="tab-pane fade" style="padding-top: 2.5%;">
                    <div class="row">
                        <div class="col-md-12">
                            <h5>
                                License Software
                            </h5>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <p>
                                Here you can license software in order to be researched, the price is normally dependent
                                on how high the softwares level appears to be, as well as a few other variables.
                                Including
                                the level of your current research software.
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">

                            <?php

                            if (empty($accounts)) {

                                ?>
                                <div class="panel panel-warning">
                                    <div class="panel-heading">
                                        No Bank Accounts
                                    </div>
                                    <div class="panel-body">
                                        You currently don't have any bank accounts, you should probably go create one if
                                        you want
                                        to license software.
                                    </div>
                                </div>
                                <?php
                            } else {

                                ?>

                                <div class="panel panel-info">
                                    <div class="panel-body">
                                        <form method="post">
                                            <p>
                                                Software
                                            </p>
                                            <select name="softwareid" class="combobox input-sm form-control">
                                                <option></option>
                                                <?php

                                                $computeroftwares = $computer_controller->getComputerSoftware($computer_controller->getCurrentUserComputer());

                                                if (empty($computeroftwares) == false) {

                                                    foreach ($computeroftwares as $key => $value) {

                                                        if ($softwares->softwareExists($value['softwareid']) == false) {

                                                            continue;
                                                        }

                                                        if ($softwares->hasLicense($value['softwareid'])) {

                                                            continue;
                                                        }

                                                        $software = $softwares->getSoftware($value['softwareid']);

                                                        $extension = $softwares->getSoftwareExtension($softwares->getSoftwareNameFromSoftwareID($value['softwareid']));

                                                        $price = Settings::getSetting('syscrack_research_price_multiplier') * $software->level;

                                                        echo('<option value="' . $software->softwareid . '">' . $software->softwarename . $extension . ' ' . $software->size . 'mb (' . $software->level . ') ' . Settings::getSetting('syscrack_currency') . $price . '</option>');
                                                    }
                                                }
                                                ?>
                                            </select>
                                            <p style="margin-top: 1.5%;">
                                                Account Number
                                            </p>
                                            <select name="accountnumber" class="combobox input-sm form-control">
                                                <option></option>

                                                <?php

                                                if (empty($accounts) == false) {

                                                    foreach ($accounts as $account) {

                                                        ?>
                                                        <option value="<?= $account->accountnumber ?>">
                                                            #<?= $account->accountnumber ?>
                                                            (<?= Settings::getSetting('syscrack_currency') . number_format($account->cash) ?>
                                                            )
                                                            @<?= $computer_controller->getComputer($account->computerid)->ipaddress ?></option>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </select>
                                            <button style="width: 100%; margin-top: 2.5%;" class="btn btn-primary"
                                                    name="action" value="licensesoftware" type="submit">
                                                <span class="glyphicon glyphicon-cog" aria-hidden="true"></span> License
                                                Software
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <div id="researchsoftware" class="tab-pane fade" style="padding-top: 2.5%;">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-danger">
                                <div class="panel-body">
                                    This is currently a little buggy as the current feature used isn't really designed for this
                                    front end. Please wait for the front end rewrite for a more fluid experience and please
                                    check your <a href="/processes/">for a research process.</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                                <p>
                                    Software
                                </p>
                                <select name="softwareid" id="softwareid" class="combobox input-sm form-control">
                                    <option></option>
                                    <?php

                                    $computeroftwares = $computer_controller->getComputerSoftware($computer_controller->getCurrentUserComputer());

                                    if (empty($computeroftwares) == false) {

                                        foreach ($computeroftwares as $key => $value) {

                                            if ($softwares->softwareExists($value['softwareid']) == false) {

                                                continue;
                                            }

                                            if ($softwares->hasLicense($value['softwareid']) == false ) {

                                                continue;
                                            }

                                            $software = $softwares->getSoftware($value['softwareid']);

                                            $extension = $softwares->getSoftwareExtension($softwares->getSoftwareNameFromSoftwareID($value['softwareid']));

                                            $price = Settings::getSetting('syscrack_research_price_multiplier') * $software->level;

                                            echo('<option value="' . $software->softwareid . '">' . $software->softwarename . $extension . ' ' . $software->size . 'mb (' . $software->level . ') ' . Settings::getSetting('syscrack_currency') . $price . '</option>');
                                        }
                                    }
                                    ?>
                                </select>
                                <p style="margin-top: 1.5%;">
                                    Software Name
                                </p>
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon1">
                                        <span class="glyphicon glyphicon-user" aria-hidden="true">
                                        </span>
                                    </span>
                                    <input type="text" class="form-control" name="name" id="name" placeholder="my software" aria-describedby="basic-addon1">
                                </div
                                <p style="margin-top: 1.5%;">
                                    Account Number
                                </p>
                                <select name="accountnumber" id="accountnumber" class="combobox input-sm form-control">
                                    <option></option>

                                    <?php

                                    if (empty($accounts) == false) {

                                        foreach ($accounts as $account) {

                                            ?>
                                            <option value="<?= $account->accountnumber ?>">
                                                #<?= $account->accountnumber ?>
                                                (<?= Settings::getSetting('syscrack_currency') . number_format($account->cash) ?>
                                                )
                                                @<?= $computer_controller->getComputer($account->computerid)->ipaddress ?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <button style="width: 100%; margin-top: 2.5%;" class="btn btn-primary"
                                        name="action" value="licensesoftware" onclick="sendPost()">
                                    <span class="glyphicon glyphicon-cog" aria-hidden="true"></span> Research Software
                                </button>
                                <script>

                                    function sendPost()
                                    {

                                        var softwareid = $("#softwareid").val();
                                        var name = $("#name").val();
                                        var accountnumber =  $("#accountnumber").val();

                                        $.ajax({
                                            url: '/computer/actions/research/' + softwareid,
                                            type: "POST",
                                            data: {
                                                name: name,
                                                accountnumber: accountnumber
                                            },
                                            success: function(data){
                                                console.log( data );
                                                window.location.reload();
                                            }
                                        });
                                    }
                                </script>
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

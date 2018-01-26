<?php

use Framework\Application\Container;
use Framework\Application\Render;
use Framework\Application\Settings;
use Framework\Exceptions\ViewException;
use Framework\Syscrack\Game\Computers;
use Framework\Syscrack\Game\Finance;
use Framework\Syscrack\Game\Utilities\PageHelper;

if (isset($computers) == false) {

    $computers = new Computers();
}

if (isset($pagehelper) == false) {

    $pagehelper = new PageHelper();
}

if (isset($finance) == false) {

    $finance = new Finance();
}

$session = Container::getObject('session');

if ($session->isLoggedIn()) {

    $session->updateLastAction();
}

$currentcomputer = $computers->getComputer($computers->getCurrentUserComputer());

if ($computers->hasType($currentcomputer->computerid, Settings::getSetting('syscrack_software_collector_type'), true) == false) {

    throw new ViewException();
}

$collector = $pagehelper->getInstalledCollector()
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
        <div class="col-lg-12">
            <?php

            if (isset($_GET['error']))
                Render::view('syscrack/templates/template.alert', array('message' => $_GET['error']));
            elseif (isset($_GET['success']))
                Render::view('syscrack/templates/template.alert', array('message' => Settings::getSetting('alert_success_message'), 'alert_type' => 'alert-success'));
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12" onclick="window.location.href = '/computer/'">
            <h5 style="color: #ababab" class="text-uppercase">
                <span class="badge"><?= $currentcomputer->type ?></span> <?= $currentcomputer->ipaddress ?>
            </h5>
        </div>
    </div>
    <div class="row" style="margin-top: 1.5%;">

        <?php

        Render::view('syscrack/templates/template.computer.actions', array('computers' => $computers));
        ?>
        <div class="col-md-8">
            <div class="row">
                <div class="col-sm-12">
                    <div class="panel panel-info">
                        <div class="panel-body text-center">
                            <?php
                            $accounts = $finance->getUserBankAccounts($session->getSessionUser());

                            if (empty($accounts)) {

                                ?>
                                You currently don't have a bank account, you should probably get one before you try and collect
                                <?php
                            } else {

                                ?>
                                <form method="post">
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
                                                    @<?= $computers->getComputer($account->computerid)->ipaddress ?></option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                    <button style="width: 100%; margin-top: 2.5%;" class="btn btn-sm btn-info"
                                            type="submit">
                                        <span class="glyphicon glyphicon-gbp" aria-hidden="true"></span> Collect
                                    </button>
                                </form>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <?php

                if (isset($results)) {

                    if (empty($results)) {

                        ?>
                        <div class="col-sm-12">
                            <div class="panel panel-info">
                                <div class="panel-heading">
                                    Results not found
                                </div>
                                <div class="panel-body">
                                    No results were found, maybe you should try again a little later...
                                </div>
                            </div>
                        </div>
                        <?php
                    } else {

                        ?>
                        <div class="col-sm-12">
                            <div class="panel panel-info">
                                <div class="panel-body">
                                    <h1>
                                        <?php if (isset($total) == false) {
                                            echo Settings::getSetting('syscrack_currency') . '0';
                                        } else {
                                            echo Settings::getSetting('syscrack_currency') . number_format($total);
                                        } ?>
                                        <small>
                                            Profits Generated
                                        </small>
                                    </h1>
                                    <h5 style="color: #ababab" class="text-uppercase">
                                        Results
                                    </h5>
                                    <ul class="list-group">
                                        <?php

                                        foreach ($results as $result) {

                                            ?>
                                            <li class="list-group-item">
                                                <?= $result['message'] ?> <span class="badge" style="float: right;"><a
                                                            style="color: white;"
                                                            href="/game/internet/<?= $result['ipaddress'] ?>/"><?= $result['ipaddress'] ?></a></span>
                                            </li>
                                            <?php
                                        }
                                        ?>
                                    </ul>
                                </div>
                            </div>
                            <?php
                            if (isset($total)) {

                                if ($total !== 0) {

                                    ?>
                                    <div class="panel panel-danger">
                                        <div class="panel-body text-center">
                                            <a href="/computer/log">Clean your log and hide your bank account number</a>
                                        </div>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                        </div>
                        <?php
                    }
                } else {

                    ?>
                    <div class="col-sm-12">
                        <div class="panel panel-default">
                            <div class="panel-body text-center">
                                Your profits will appear here when the collection process has completed
                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <?php

            Render::view('syscrack/templates/template.footer', array('breadcrumb' => true));
            ?>
        </div>
    </div>
</div>
</body>
</html>
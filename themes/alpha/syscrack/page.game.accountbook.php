<?php

use Framework\Application\Container;
use Framework\Application\Render;
use Framework\Application\Settings;
use Framework\Syscrack\Game\AccountDatabase;
use Framework\Syscrack\Game\Computer;
use Framework\Syscrack\Game\Finance;
use Framework\Syscrack\Game\Utilities\PageHelper;

$session = Container::getObject('session');

if ($session->isLoggedIn()) {

    $session->updateLastAction();
}

if (isset($pagehelper) == false) {

    $pagehelper = new PageHelper();
}

if (isset($computer_controller) == false) {

    $computer_controller = new Computer();
}

if (isset($addressdatabase) == false) {

    $accountdatabase = new AccountDatabase($session->getSessionUser());
}

if (isset($finance) == false) {

    $finance = new Finance();
}
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
            <h5 style="color: #ababab" class="text-uppercase">
                Account Book
            </h5>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            <div class="list-group">
                <a href="/game/addressbook/" class="list-group-item">
                    <h4 class="list-group-item-heading">Address Book</h4>
                    <p class="list-group-item-text">View all of your hacked addresses.</p>
                </a>
                <a href="/game/accountbook/" class="list-group-item active">
                    <h4 class="list-group-item-heading">Account Book</h4>
                    <p class="list-group-item-text">View all of the accounts you have hacked.</p>
                </a>
            </div>
        </div>
        <div class="col-sm-8">
            <?php
            $accounts = $accountdatabase->getDatabase($session->getSessionUser());

            if (empty($accounts)) {

                ?>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="panel panel-info">
                            <div class="panel-body">
                                You haven't hacked any bank accounts yet, why don't you check out
                                the <a href="/game/internet/">internet</a> to see if you can find any banks.
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            } else {

                $removed = array();

                $count = 0;

                foreach ($accounts as $key => $value) {

                    $count++;

                    if (isset($value['accountnumber']) == false) {

                        continue;
                    }

                    if ($finance->accountNumberExists($value['accountnumber']) == false) {

                        $removed[] = $value;

                        continue;
                    }

                    $account = $finance->getByAccountNumber($value['accountnumber']);

                    ?>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    #<?= $value['accountnumber'] ?>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="well-lg">
                                                <h1>
                                                    <?= $settings['syscrack_currency'] . number_format($account->cash) ?>
                                                </h1>
                                            </div>
                                        </div>
                                        <div class="col-sm-8">
                                            <ul class="list-group">
                                                <li class="list-group-item">
                                                    Account Address <span class="badge right"><a style="color: white;"
                                                                                                 href="/game/internet/<?= $value['ipaddress'] ?>/"><?= $value['ipaddress'] ?></a></span>
                                                </li>
                                                <li class="list-group-item">
                                                    Account Created <span
                                                            class="badge right"><?= date("F j, Y, g:i a", $account->timecreated) ?></span>
                                                </li>
                                            </ul>
                                            <div class="btn-group btn-group-justified" role="group" aria-label="...">
                                                <div class="btn-group" role="group"
                                                     onclick="window.location.href = '/game/internet/<?= $value['ipaddress'] ?>/'">
                                                    <button type="button" class="btn btn-info">Visit bank</button>
                                                </div>
                                                <div class="btn-group" role="group"
                                                     onclick="window.location.href = '/game/internet/<?= $value['ipaddress'] ?>/login'">
                                                    <button type="button" class="btn btn-success">Login</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }

                if (empty($removed) == false) {

                    ?>
                    <div class="panel panel-danger">
                        <div class="panel-heading">
                            Accounts Removed
                        </div>
                        <div class="panel-body">
                            <h5>
                                Some accounts have been removed from your database as they have become invalid.
                            </h5>

                            <?php

                            foreach ($removed as $key => $value) {
                                ?>
                                <p>
                                    <?= $value['accountnumber'] ?> @ <?= $value['ipaddress'] ?>
                                </p>
                                <?php
                                $accountdatabase->removeAccountNumber($value['accountnumber']);
                            }
                            ?>

                        </div>
                    </div>
                    <?php


                    $accountdatabase->saveDatabase();
                }

                ?>
                <div class="row">
                    <div class="col-sm-12">
                        <p>
                            <?= $count ?> accounts in total...
                        </p>
                    </div>
                </div>
                <?php
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
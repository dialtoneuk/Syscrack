<?php

use Framework\Application\Container;
use Framework\Application\Settings;
use Framework\Syscrack\Game\Finance;
use Framework\Syscrack\Game\Internet;
use Framework\Syscrack\Game\Schema;
use Framework\Syscrack\Game\Softwares;
use Framework\Syscrack\Game\Utilities\PageHelper;
use Framework\Application\Render;

$session = Container::getObject('session');

if ($session->isLoggedIn()) {

    $session->updateLastAction();
}

if (isset($pagehelper) == false) {

    $pagehelper = new PageHelper();
}

if (isset($softwares) == false) {

    $softwares = new Softwares();
}

if (isset($npc) == false) {

    $npc = new Schema();
}

if (isset($internet) == false) {

    $internet = new Internet();
}

if (isset($finance) == false) {

    $finance = new Finance();
}

if (isset($userid) == false) {

    $userid = $session->getSessionUser();
}

$current_computer = $internet->getComputer($ipaddress);
?>
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
        <div class="col-md-12">
            <h5 style="color: #ababab" class="text-uppercase">
                <?php
                if ($npc->hasSchema($current_computer->computerid)) {

                    $schema = $npc->getSchema($current_computer->computerid);

                    if (isset($schema['name'])) {

                        echo $schema['name'];
                    }
                } else {

                    ?>
                    Bank
                    <?php
                }
                ?>
            </h5>
            <div class="row">
                <div class="col-md-4 col-md-offset-4">
                    <?php

                    if ($finance->hasAccountAtComputer($current_computer->computerid, $userid)) {

                        $account = $finance->getAccountAtBank($current_computer->computerid, $userid);

                        ?>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Bank Information
                            </div>
                            <div class="panel-body">
                                <ul class="list-group">
                                    <li class="list-group-item">
                                        <span class="badge"><?= $account->accountnumber ?></span>
                                        Account Number
                                    </li>
                                    <li class="list-group-item">
                                        <span class="badge"><?= Settings::getSetting('syscrack_currency') . number_format($account->cash) ?></span>
                                        Balance
                                    </li>
                                </ul>
                                <p class="text-center">
                                    <strong>Warning</strong> Deleting your account will mean that you lose all the cash
                                    currently in that account... so be
                                    careful!
                                </p>
                                <form method="post" style="width: 100%;">
                                    <div class="btn-group" role="group" aria-label="Create bank account"
                                         style="width: 100%;">
                                        <button type="submit" name="action" value="delete" class="btn btn-danger"
                                                style="width: 50%;">Delete Account
                                        </button>
                                        <button type="button" class="btn btn-success"
                                                onclick="window.location.href = '/finances/transfer/'"
                                                style="width: 50%;">Transfer
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <?php
                    } else {

                        ?>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Create Account
                            </div>
                            <div class="panel-body">
                                <p class="text-center">
                                    You currently don't have an account at this bank, but its free to create one! There
                                    is also a signup bonus
                                    of
                                    <strong><?= Settings::getSetting('syscrack_currency') . number_format(Settings::getSetting('syscrack_bank_default_balance')) ?></strong>
                                </p>

                                <form method="post" style="width: 100%;">
                                    <div class="btn-group" role="group" aria-label="Create bank account"
                                         style="width: 100%;">
                                        <button type="submit" name="action" value="create" class="btn btn-default"
                                                style="width: 100%;">Create
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <p class="text-center">
                <a href="/game/internet/<?= $ipaddress ?>/">Go Back</a>
            </p>
        </div>
    </div>

    <?php

    Render::view('syscrack/templates/template.footer', array('breadcrumb' => true));
    ?>
</div>
</body>
</html>
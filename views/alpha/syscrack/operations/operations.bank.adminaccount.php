<?php

use Framework\Application\Settings;
use Framework\Syscrack\Game\Finance;
use Framework\Syscrack\Game\Internet;
use Framework\Syscrack\Game\Schema;

if (isset($internet) == false) {

    $internet = new Internet();
}

if (isset($npc) == false) {

    $npc = new Schema();
}

if (isset($finance) == false) {

    $finance = new Finance();
}

$current_computer = $internet->getComputer($ipaddress);

if ($finance->accountNumberExists($accountnumber) == false) {

    Flight::redirect(Settings::getSetting('controller_index_root') . Settings::getSetting('syscrack_game_page') . '/' . Settings::getSetting('syscrack_internet_page') . '/');
}

$account = $finance->getByAccountNumber($accountnumber);
?>

<html>

<?php

Render::render('syscrack/templates/template.header', array('pagetitle' => 'Syscrack | Game'));
?>
<body>
<div class="container">

    <?php

    Render::render('syscrack/templates/template.navigation');
    ?>
    <div class="row">
        <div class="col-sm-12">
            <?php

            if (isset($_GET['error']))
                Render::render('syscrack/templates/template.alert', array('message' => $_GET['error']));
            elseif (isset($_GET['success']))
                Render::render('syscrack/templates/template.alert', array('message' => Settings::getSetting('alert_success_message'), 'alert_type' => 'alert-success'));
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

                        ?>
                        <?= $schema['name'] ?> Remote Admin System
                        <?php
                    }
                } else {

                    ?>
                    Bank Remote Admin System
                    <?php
                }
                ?>
            </h5>
            <div class="row">
                <div class="col-md-4">
                    <p>
                        Current Account Information
                    </p>
                    <p class="small text-uppercase" style="font-size: 65%;">
                        Pleaes note that all actions on this page are logged and illegal misuse is reported.
                    </p>
                    <ul class="list-group">
                        <li class="list-group-item">
                            Cash <span
                                    class="badge right"><?= Settings::getSetting('syscrack_currency') . number_format($account->cash) ?></span>
                        </li>
                        <li class="list-group-item">
                            Database Address <span class="badge right"><?= $ipaddress ?></span>
                        </li>
                        <li class="list-group-item">
                            Account Number <span class="badge right"><?= $accountnumber ?></span>
                        </li>
                        <li class="list-group-item">
                            Account ID <span class="badge right"><?= $account->accountid ?></span>
                        </li>
                        <li class="list-group-item">
                            Time Created <span
                                    class="badge right"><?= date("F j, Y, g:i a", $account->timecreated) ?></span>
                        </li>
                    </ul>
                </div>
                <div class="col-md-8">
                    <p>
                        Transfer Cash
                    </p>
                    <div class="panel panel-info">
                        <div class="panel-body">
                            <form method="post" style="padding: 0; margin: 0;">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon" id="basic-addon1">Account</span>
                                    <input type="text" class="form-control" name="accountnumber" placeholder="00000000"
                                           aria-describedby="basic-addon1">
                                    <span class="input-group-addon" id="basic-addon1">@</span>
                                    <input type="text" class="form-control" name="ipaddress"
                                           placeholder="<?= $ipaddress ?>" aria-describedby="basic-addon1">
                                    <span class="input-group-addon" id="basic-addon1"><span
                                                class="glyphicon glyphicon-gbp"></span></span>
                                    <input type="number" class="form-control" name="amount" placeholder="25.0"
                                           value="<?= $account->cash ?>" aria-describedby="basic-addon1">
                                </div>
                                <button style="width: 100%; margin-top: 2.5%;" class="btn btn-info" name="action"
                                        value="transfer" type="submit">
                                    <span class="glyphicon glyphicon-cog" aria-hidden="true"></span> Transfer
                                </button>
                            </form>
                        </div>
                    </div>
                    <p>
                        Disconnect Remote Access
                    </p>
                    <div class="panel panel-info">
                        <div class="panel-body">
                            <form style="padding: 0; margin: 0;" method="post">
                                <button style="width: 100%;" class="btn btn-info" name="action" value="disconnect"
                                        type="submit">
                                    <span class="glyphicon glyphicon-arrow-down" aria-hidden="true"></span> Disconnect
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <p class="text-center">
                <a href="/game/internet/<?= $ipaddress ?>/">Go Back</a>
            </p>
        </div>
    </div>
    <?php

    Render::render('syscrack/templates/template.footer', array('breadcrumb' => true));
    ?>
</div>
</body>
</html>
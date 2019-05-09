<?php
    use Framework\Application\Render;
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
                    Render::view('syscrack/templates/template.alert', array('message' => $settings['alert_success_message'], 'alert_type' => 'alert-success'));
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <h5 style="color: #ababab" class="text-uppercase">
                <?php
                if( isset( $metadata ) )
                    echo @$metadata->custom["name"]
                ?>
            </h5>
            <div class="row">
                <div class="col-md-4 col-md-offset-4">
                    <?php
                    if ( empty( $account ) == false )
                    {

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
                                        <span class="badge"><?= $settings['syscrack_currency'] . number_format($account->cash) ?></span>
                                        Balance
                                    </li>
                                </ul>
                                <p class="text-center">
                                    <strong>Warning</strong> Deleting your account will mean that you lose all the cash
                                    currently in that account... so be
                                    careful!
                                </p>
                                <form method="post" action="/game/internet/<?=$ipaddress?>/bank" style="width: 100%;">
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
                    }
                    else
                    {
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
                                    <strong><?= $settings['syscrack_currency'] . number_format($settings['syscrack_bank_default_balance']) ?></strong>
                                </p>

                                <form method="post" action="/game/internet/<?=$ipaddress?>/bank" style="width: 100%;">
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
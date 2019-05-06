<?php
    use Framework\Application\Render;
?>
<html>
    <?php Render::view('syscrack/templates/template.header', array('pagetitle' => 'Syscrack | Game')); ?>
<body>
    <div class="container">
        <?php Render::view('syscrack/templates/template.navigation'); ?>
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
                    <div class="col-md-4">
                        <p>
                            Current Account Information
                        </p>
                        <ul class="list-group">
                            <li class="list-group-item list-group-item-success">
                                <h2><?=@$settings['syscrack_currency'] . number_format($account->cash)?></h2>
                            </li>
                            <li class="list-group-item">
                                Database Address <span class="badge right"><?=@ $ipaddress ?></span>
                            </li>
                            <li class="list-group-item">
                                Account Number <span class="badge right"><?=@$account->accountnumber ?></span>
                            </li>
                            <li class="list-group-item">
                                Account ID <span class="badge right"><?=@$account->accountid ?></span>
                            </li>
                            <li class="list-group-item">
                                Time Created <span
                                        class="badge right"><?=@date("F j, Y, g:i a", $account->timecreated) ?></span>
                            </li>
                        </ul>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="panel panel-warning">
                                    <div class="panel-body">
                                        Unusual activity will be reported to the local authorities and investigated fully.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-lg-12">
                                <p>
                                    Your accounts
                                </p>
                                <div class="panel panel-default">
                                    <ul class="list-group">
                                        <?php

                                            if( empty( $accounts ) )
                                            {
                                                ?>
                                                    <li class="list-group-item">
                                                        You don't currently have any bank accounts
                                                    </li>
                                                <?php
                                            }
                                            else
                                            {

                                                foreach( $accounts as $key=>$user_account )
                                                {

                                                    ?>
                                                    <li class="list-group-item">
                                                        #<?=@$user_account->accountnumber?> at <?=@$accounts_location[$key]["ipaddress"]?> <span class="badge right"><?=@$settings["syscrack_currency"] . @$user_account->cash ?></span>
                                                    </li>
                                                    <?php
                                                }
                                            }
                                        ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
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
                                                       placeholder="<?=@$ipaddress ?>" aria-describedby="basic-addon1">
                                                <span class="input-group-addon" id="basic-addon1"><span
                                                            class="glyphicon glyphicon-gbp"></span></span>
                                                <input type="number" class="form-control" name="amount" placeholder="25.0"
                                                       value="<?=@ $account->cash ?>" aria-describedby="basic-addon1">
                                            </div>
                                            <button style="width: 100%; margin-top: 2.5%;" class="btn btn-info" name="action"
                                                    value="transfer" type="submit">
                                                <span class="glyphicon glyphicon-cog" aria-hidden="true"></span> Transfer
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <form style="padding: 0; margin: 0;" method="post">
                                    <button style="width: 100%;" class="btn btn-default" name="action" value="disconnect"
                                            type="submit">
                                        <span class="glyphicon glyphicon-off" aria-hidden="true"></span> Disconnect
                                    </button>
                                </form>
                                <button style="width: 100%; margin-top: 16px;" class="btn btn-info" onclick="window.location.href = '/game/internet/<?=@$ipaddress?>'">
                                    <span class="glyphicon glyphicon-home" aria-hidden="true"></span> Root
                                </button>
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
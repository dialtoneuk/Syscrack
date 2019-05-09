<?php
    use Framework\Application\Render;
?>
<!DOCTYPE html>
<html style="overflow-x: hidden;">
    <?php
        Render::view('syscrack/templates/template.header', ['pagetitle' => 'Syscrack'] )
    ?>
    <style>
        :target {
            animation: ease-in 5s;
            border-color: rgb(40, 40, 40);
        }
    </style>
    <body>
        <div class="container">
            <?php
                Render::view('syscrack/templates/template.navigation')
            ?>
            <div class="row">
                <div class="col-sm-12">

                    <?php

                    if (isset($_GET['error']))
                        Render::view('syscrack/templates/template.alert', array('message' => $_GET['error']));
                    elseif (isset($_GET['success']))
                        Render::view('syscrack/templates/template.alert', array('message' => $settings['alert_success_message'], 'alert_type' => 'alert-success') );
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    Buy VPC
                                </div>
                                <div class="panel-body">

                                    <?php
                                    if ( empty( $accounts ) == false )
                                    {

                                        ?>
                                        <form method="post" action="/game/computer/">
                                            <p>
                                                You may purchase a band new computer for <?= number_format( count( $computers) * ( $settings['syscrack_vpc_purchase_price'] * $settings['syscrack_vpc_purchase_increase'] ) ) ?>
                                            </p>
                                            <?php
                                                Render::view("syscrack/templates/template.account.search", array('values' => @$accounts ) );
                                            ?>
                                            <div class="btn-group btn-group-justified" role="group" aria-label="..." style="margin-top: 2.5%;">
                                                <div class="btn-group" role="group">
                                                    <button type="submit" class="btn btn-success">Purchase
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                        <?php
                                    }
                                    else
                                    {

                                        ?>
                                        <p>
                                            You currently do not have any bank accounts, please create a bank account
                                            in order to buy a new computer.
                                        </p>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <h4>
                                        Total Computers
                                    </h4>
                                    <p>
                                        <?=count($computers)?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="panel panel-info">
                                <div class="panel-heading">
                                    Want more information?
                                </div>
                                <div class="panel-body">
                                    If you have a remote control unit currently installed on
                                    your active VPC, you will be able to remotely view the logs
                                    and hardwares of your computers. You can find one on the
                                    <a href="/game/internet/">internet</a> or by finding
                                    a marketplace which sells software.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="row">
                        <div class="col-sm-12">
                            <h4>
                                Current Machine
                            </h4>
                            <div class="panel panel-success">
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <h5>
                                                <?=$computer->ipaddress?> <span class="badge" style="float: right;"><?=$computer->type?></span>
                                            </h5>
                                        </div>
                                        <div class="col-sm-8">
                                            <button style="width: 100%;" class="btn btn-success"
                                                    onclick="window.location.href = '/computer/'">
                                                <span class="glyphicon glyphicon-arrow-up" aria-hidden="true"></span> View Machine
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <h4>
                                Computers on your network
                            </h4>
                        </div>
                    </div>
                    <?php

                    foreach ( $computers as $key=>$value )
                    {

                        if ( $computer->computerid == $value->computerid)
                            continue;

                    ?>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="panel panel-primary"  id="<?= $value->ipaddress ?>">
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <h5>
                                                    <?=$value->ipaddress?> <span class="badge" style="float: right;"><?=$value->type?></span>
                                                </h5>
                                            </div>
                                            <div class="col-sm-8">
                                                <form method="post" action="/game/computer/switch/<?=$value->computerid?>">
                                                    <button style="width: 100%;" class="btn btn-default"
                                                            name="action" value="licensesoftware" type="submit">
                                                        <span class="glyphicon glyphicon-cog" aria-hidden="true"></span> Switch
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </body>

    <?php
        Render::view('syscrack/templates/template.footer')
    ?>
</html>

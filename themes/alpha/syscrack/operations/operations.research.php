<?php
    use Framework\Application\Render;
?>

<!DOCTYPE html>
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
                <div class="col-sm-12" onclick="window.location.href = '/computer/'">
                    <h5 style="color: #ababab" class="text-uppercase">
                        <span class="badge"><?=@ $computer->type ?></span> <?=@ $computer->ipaddress ?>
                    </h5>
                </div>
            </div>
            <div class="row" style="margin-top: 1.5%;">
                <?php Render::view('syscrack/templates/template.computer.actions' ); ?>
                <div class="col-md-8">
                    <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="#licensedsoftware">Licensed Software</a></li>
                        <li><a data-toggle="tab" href="#licensesoftware">License Software</a></li>
                        <li><a data-toggle="tab" href="#researchsoftware">Research Software</a></li>
                    </ul>
                    <div class="tab-content">
                        <div id="licenced" class="tab-pane fade in active" style="padding-top: 2.5%;">
                            <?php
                            if (empty($licences)) {

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
                                foreach ($licences as $license) {
                            ?>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <span class="glyphicon <?=@$icons[ $license->softwareid ]?>"></span> <?=@@$softwares[ $license->softwareid ]->softwarename?>
                                                    <span class="badge" style="float: right;"><?=@$softwares[ $license->softwareid ]->level ?></span>
                                                </div>
                                                <div class="panel-body">
                                                    <?php
                                                        if ( @$license->userid == @$user["userid"] ) {

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
                                                                class="badge right"><?=@$user["userid"]?></span>
                                                        </li>
                                                        <li class="list-group-item">
                                                            Type <span class="badge right"><?=@$softwares[ $license->softwareid ]->type ?></span>
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
                        <div id="licence" class="tab-pane fade" style="padding-top: 2.5%;">
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
                                                to                                                    license software.
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
                                                    <?php
                                                        Render::view("syscrack/templates/template.software.search", array('values' => $softwares ) );
                                                    ?>
                                                    <p style="margin-top: 1.5%;">
                                                        Account Number
                                                    </p>
                                                    <select name="accountnumber" class="combobox input-sm form-control">
                                                        <option></option>

                                                        <?php
                                                            if (empty($accounts) == false)
                                                            {

                                                                foreach ($accounts as $account) {

                                                                    ?>
                                                                    <option value="<?=@$account->accountnumber?>">
                                                                        #<?=@$account->accountnumber ?>
                                                                        (<?=@$settings['syscrack_currency'] . number_format($account->cash)?>
                                                                        )
                                                                        @<?=@$ipaddresses[ $account->computerid ]?></option>
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


                                            foreach ( @$softwares as $software )
                                                if( isset( $licenses[ $software->softwareid ] ) == false )
                                                    echo('<option value="'
                                                        . $software->softwareid
                                                        . '">'
                                                        . $software->softwarename
                                                        . $extensions[ $software->softwareid ]
                                                        . ' '
                                                        . $software->size
                                                        . 'mb ('
                                                        . $software->level . ') '
                                                        . $settings['syscrack_currency']
                                                        . $licenses[ $software->softwareid ]["cost"]. '</option>');

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
                                            if (empty($accounts) == false)
                                            {

                                                foreach($accounts as $account) {

                                                    ?>
                                                    <option value="<?=@$account->accountnumber?>">
                                                        #<?=@$account->accountnumber ?>
                                                        (<?=@$settings['syscrack_currency'] . number_format($account->cash)?>
                                                        )
                                                        @<?=@$ipaddresses[ $account->computerid ]?></option>
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

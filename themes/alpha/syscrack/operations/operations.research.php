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
                <div class="col-sm-12">
                    <h2 style="color: #ababab" class="text-uppercase">
                        Research Centre <small style="float: right;">Executing from [<?=@$computer->ipaddress ?>] <a href="/game/computer">Switch?</a></small>
                    </h2>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="row">
                        <?php
                        $usedspace = 0;

                        if( isset( $localsoftwares ) )
                            foreach( $localsoftwares as $software )
                                $usedspace += @$software->size;
                        ?>
                        <div class="col-lg-12">
                            <?php Render::view('syscrack/templates/template.storage', array( 'hardwares' => json_decode( $computer->hardware, true ), 'usedspace' => $usedspace ) ); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <ul class="nav nav-tabs">
                                <li class="active"><a data-toggle="tab" href="#licenced">Licenses</a></li>
                                <li><a data-toggle="tab" href="#licence">Purchae</a></li>
                                <li><a data-toggle="tab" href="#researchsoftware">Research</a></li>
                            </ul>
                            <div class="tab-content">
                                <div id="licenced" class="tab-pane fade in active" style="padding-top: 12px;">
                                    <?php
                                    if (empty( $licenses )) {

                                        ?>
                                        <div class="panel panel-warning">
                                            <div class="panel-body">
                                                You currently don't have any licensed software on your system
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    else
                                    {
                                        foreach ($licenses as $license) {
                                            ?>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="panel panel-default">
                                                        <div class="panel-body">
                                                            <h3>
                                                                <span class="glyphicon <?=@$icons[ $license->softwareid ]?>"></span>
                                                                <?=@$license->softwarename?>
                                                                <small><?=@$license->level?></small>
                                                            </h3>
                                                            <?php
                                                            if ( @$license->userid == @$user->userid ) {

                                                                ?>
                                                                <div class="panel panel-info">
                                                                    <div class="panel-body text-center">
                                                                        You currently own this software license
                                                                    </div>
                                                                </div>
                                                                <?php
                                                            }
                                                            ?>
                                                            <ul class="list-group">
                                                                <li class="list-group-item">
                                                                    Owner <span
                                                                            class="badge right"><?=@$license->userid?></span>
                                                                </li>
                                                                <li class="list-group-item">
                                                                    Type <span class="badge right"><?=@$license->type ?></span>
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
                                <div id="licence" class="tab-pane fade" style="padding-top: 12px;">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="panel panel-info">
                                                <div class="panel-body">
                                                    In order to research a software further, you will need to purchase its license.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">

                                            <?php

                                            if ( empty($accounts) == false )
                                            {
                                                ?>
                                                <div class="panel panel-info">
                                                    <div class="panel-body">
                                                        <form method="post" action="/game/internet/<?=$ipaddress?>/research">
                                                            <?php
                                                            Render::view("syscrack/templates/template.software.search", array('values' => @$localsoftwares ) );
                                                            ?>
                                                            <?php
                                                            Render::view("syscrack/templates/template.account.search", array('values' => @$accounts ) );
                                                            ?>
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
                                            else
                                            {
                                                ?>
                                                <div class="panel panel-danger">
                                                    <div class="panel-body">
                                                        Research licenses are currently unavailable.
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <div id="researchsoftware" class="tab-pane fade" style="padding-top: 12px;">
                                    <div class="row">
                                        <form method="post" action="/game/internet/<?=$ipaddress?>/research">
                                            <div class="col-md-12">
                                                <?php
                                                    Render::view("syscrack/templates/template.software.search", array('values' => @$licenses ) );
                                                ?>
                                                <div class="input-group" style="margin-top: 18px;">
                                                <span class="input-group-addon" id="basic-addon1">
                                                    <span class="glyphicon glyphicon-user" aria-hidden="true"></span>
                                                </span>
                                                    <input type="text" class="form-control" name="name" id="name" placeholder="my software" aria-describedby="basic-addon1">
                                                </div>
                                                <?php
                                                    Render::view("syscrack/templates/template.account.search", array('values' => @$accounts ) );
                                                ?>
                                                <button style="width: 100%; margin-top: 18px;" class="btn btn-primary"
                                                        name="action" value="licensesoftware" onclick="sendPost()">
                                                    <span class="glyphicon glyphicon-cog" aria-hidden="true"></span> Research Software
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <?php
                        Render::view('syscrack/templates/template.softwares', array( "softwares" => $localsoftwares, "local" => true, "hideoptions" => true ));
                    ?>
                </div>
            </div>
            <?php

            Render::view('syscrack/templates/template.footer', array('breadcrumb' => true));
            ?>
        </div>
    </body>
</html>

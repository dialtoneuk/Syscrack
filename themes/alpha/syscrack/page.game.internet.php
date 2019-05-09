<?php use Framework\Application\Render;?>
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

                <?php
                    if ( isset( $connection ) && $connection !== $ipaddress ) {

                        ?>
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    Notice
                                </div>

                                <div class="panel-body">
                                    You are currently connected to <a href="/game/internet/<?= $connection ?>/"><?= $connection ?>, </a> <a href="/game/internet/<?=$connection?>/logout">Logout?</a>
                                </div>
                            </div>
                        <?php
                    }
                ?>
                <div class="row">

                    <?php
                    if ($connection == $ipaddress)
                    {
                        ?>
                            <div class="col-lg-9">
                                <?php Render::view('syscrack/templates/template.computer'); ?>
                            </div>
                        <?php
                    }
                    else
                    {
                        ?>
                            <div class="col-lg-9">
                                <?php Render::view('syscrack/templates/template.browser'); ?>
                            </div>
                        <?php
                    }

                    ?>
                    <div class="col-lg-3">
                        <?php
                            Render::view('syscrack/templates/template.tools', array('tools' => $tools ));
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php Render::view('syscrack/templates/template.footer', array('breadcrumb' => true)); ?>
    </div>
    </body>
</html>
<?php
use Framework\Application\Render;

if (isset($parsedown) == false)
    $parsedown = new Parsedown();

?>
<html>
    <?php Render::view('syscrack/templates/template.header', array('pagetitle' => 'Syscrack | Game')); ?>
    <body>
        <div class="container">
            <?php Render::view('syscrack/templates/template.navigation'); ?>
            <div class="row">
                <div class="col-md-12">
                    <h5>
                        C:\\<?=@$software->softwarename?>
                    </h5>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="well-lg">
                                <?=@$parsedown->parse( $data->text ) ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="panel panel-info">
                                <div class="panel-heading">
                                    Information
                                </div>
                                <div class="panel-body">
                                    <ul class="list-group">
                                        <li class="list-group-item">
                                            Owner <span class="badge right"><?=@$username?></span>
                                        </li>
                                        <li class="list-group-item">
                                            Last Modified <span class="badge right"><?= date("F j, Y, g:i a", @$software->lastmodified) ?></span>
                                        </li>
                                        <li class="list-group-item">
                                            Type <span class="badge right"><?= @$software->type ?></span>
                                        </li>
                                    </ul>
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
                            <button style="width: 100%;" class="btn btn-default" onclick="window.location.href = '/game/internet/<?=@$ipaddress?>'">
                                <span class="glyphicon glyphicon-home" aria-hidden="true"></span> Root
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php Render::view('syscrack/templates/template.footer', array('breadcrumb' => true)); ?>
        </div>
    </body>
</html>
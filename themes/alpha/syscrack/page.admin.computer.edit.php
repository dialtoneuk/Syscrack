<?php
    use Framework\Application\Render;
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
		        Render::view('syscrack/templates/template.errors');
	        ?>
            <div class="row">
                <div class="col-lg-12">
                    <h5 style="color: #ababab" class="text-uppercase">
                        <span class="badge"><?= $computer->type ?></span> <?= $computer->ipaddress ?>
                    </h5>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active">
                                <a href="#softwares" aria-controls="softwares" role="tab" data-toggle="tab">Software</a>
                            </li>
                            <li role="presentation">
                                <a href="#hardwares" aria-controls="hardwares" role="tab" data-toggle="tab">Hardwares</a>
                            </li>

                            <?php

                                if ( $computer->type == $settings['syscrack_computers_market_type'] )
                                {

                                    ?>
                                    <li role="presentation">
                                        <a href="#market" aria-controls="market" role="tab" data-toggle="tab">Hardwares</a>
                                    </li>
                                    <?php
                                }
                            ?>
                            <li style="float: right;"><a href="/admin/computer/">Home <span class="glyphicon glyphicon-arrow-right"></span> </a></li>
                            <li style="float: right;"><a href="/game/internet/<?= $computer->ipaddress ?>">View <span class="glyphicon glyphicon-search"></span> </a></li>
                        </ul>
                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="softwares" style="padding-top: 16px;">
                                <div class="row">
                                    <div class="col-md-9">
                                        <?php
                                            Render::view('syscrack/templates/template.softwares', array("hideoptions" => true  ));
                                        ?>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <form action="/admin/computer/edit/<?= $computer->computerid ?>/" method="post">
                                                    <div class="panel panel-default">
                                                        <div class="panel-body">
                                                            <button class="btn btn-success" style="width: 100%;" id="addsoftwaresbutton" type="button" data-toggle="collapse" data-target="#addsoftwares" aria-expanded="false" aria-controls="addsoftwares" onclick="$">
                                                                <span class="glyphicon glyphicon-plus-sign"></span> Add Software
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="collapse" id="addsoftwares" style="margin-top: 1.5%;">
                                                        <div class="panel panel-info">
                                                            <div class="panel-body">
                                                                <div class="row">
                                                                    <div class="col-lg-4">
                                                                        <div class="row">
                                                                            <div class="col-lg-12">
                                                                                <div class="input-group">
                                                                                <span class="input-group-addon"
                                                                                      id="basic-addon1"><span
                                                                                            class="glyphicon glyphicon-tags"></span></span>
                                                                                    <input type="text" class="form-control"
                                                                                           placeholder="LOIC" name="name"
                                                                                           aria-describedby="basic-addon1">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row" style="margin-top: 2.5%;">
                                                                            <div class="col-lg-12">
                                                                                <div class="input-group">
                                                                                <span class="input-group-addon"
                                                                                      id="basic-addon1"><span
                                                                                            class="glyphicon glyphicon glyphicon-asterisk"></span></span>
                                                                                    <input type="number" step="0.1"
                                                                                           class="form-control"
                                                                                           placeholder="1.0" name="level"
                                                                                           aria-describedby="basic-addon1">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row" style="margin-top: 2.5%;">
                                                                            <div class="col-lg-12">
                                                                                <div class="checkbox">
                                                                                    <label><input type="checkbox" name="schema" checked></label>
                                                                                    <span style="font-size: 10px;">Add to Schema if a schema file is present.</span>
                                                                                </div>
                                                                                <div class="checkbox">
                                                                                    <label><input type="checkbox" name="editable" checked></label>
                                                                                    <span style="font-size: 10px;">Editable</span>
                                                                                </div>
                                                                                <div class="checkbox">
                                                                                    <label><input type="checkbox" name="anondownloads"></label>
                                                                                    <span style="font-size: 10px;">Public Downloads</span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-4">
                                                                        <div class="row">
                                                                            <div class="col-lg-12">
                                                                                <div class="input-group">
                                                                                <span class="input-group-addon"
                                                                                      id="basic-addon1"><span
                                                                                            class="glyphicon glyphicon-hdd"></span></span>
                                                                                    <input type="text" class="form-control"
                                                                                           placeholder="vspam" name="uniquename"
                                                                                           aria-describedby="basic-addon1">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row" style="margin-top: 12px;">
                                                                            <div class="col-lg-12">
                                                                                <div class="input-group">
                                                                                <span class="input-group-addon"
                                                                                      id="basic-addon1"><span
                                                                                            class="glyphicon glyphicon-folder-close"></span></span>
                                                                                    <input type="number" class="form-control"
                                                                                           placeholder="20" name="size"
                                                                                           aria-describedby="basic-addon1">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row" style="margin-top: 12px;">
                                                                            <div class="col-lg-12">
                                                                                <p>
                                                                                    vspam, vminer, antivirus, research, cracker, hasher, text, firewall, nmap, vddos, breaker, collector
                                                                                </p>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-4">
                                                                        <?php
                                                                            Render::view('syscrack/templates/template.form', array('form_elements' => [
                                                                                [
                                                                                    'type' => 'textarea',
                                                                                    'name' => 'text',
                                                                                    'value' => '',
                                                                                    'resizeable' => 'vertical'
                                                                                ]
                                                                            ], 'remove_submit' => true, 'remove_form' => true));
                                                                        ?>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-lg-12">
                                                                        <button style="width: 100%;"
                                                                                class="btn btn-default" type="submit">
                                                                        <span class="glyphicon glyphicon-check"
                                                                              aria-hidden="true"></span> Add
                                                                        </button>
                                                                        <input type="hidden" name="action" value="add">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <?php
                                        if( empty( $tools_admin ) )
                                        {

                                            ?>
                                            <div class="row">
                                                <div class="col-lg-3">
                                                    <div class="panel panel-warning">
                                                        <div class="panel-body">
                                                            No tools found
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <?php
                                        }
                                        else
                                        {

                                            foreach( $tools_admin as $key=>$tool )
                                            {

                                                if( @$tool["requirements"]["software_action"] )
                                                    unset( $tools_admin[ $key ] );

                                                if( @$tool["requirements"]["hide"] )
                                                    unset( $tools_admin[ $key ] );

                                                if( isset( $tool["requirements"]["type"] ) && $tool["requirements"]["type"] != $computer->type )
                                                    unset( $tools_admin[ $key ] );
                                            }

                                            ?>
                                            <div class="col-lg-3">
                                                <?php Render::view("syscrack/templates/template.tools", array('tools' => $tools_admin) ) ?>
                                            </div>
                                            <?php

                                        }
                                    ?>

                                </div>

                            </div>
                            <div role="tabpanel" class="tab-pane" id="hardwares">
                                <div class="row" style="margin-top: 2.5%;">
                                    <div class="col-lg-8">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <?php

                                                $hardwares = json_decode( $computer->hardware, true );

                                                foreach ($hardwares as $type => $hardware) {

                                                    $icons = $settings['syscrack_hardware_icons'];

                                                    ?>
                                                    <div class="panel panel-info">
                                                        <div class="panel-heading">
                                                            <?= $type ?>
                                                        </div>
                                                        <div class="panel-body">
                                                            <div class="row">
                                                                <div class="col-lg-2">
                                                                    <?php

                                                                    if (isset($icons[$type])) {

                                                                        ?>
                                                                        <h1>
                                                                            <span class="glyphicon <?= $icons[$type] ?>"></span>
                                                                        </h1>
                                                                        <?php
                                                                    } else {

                                                                        ?>
                                                                        <h1>
                                                                            <span class="glyphicon glyphicon-question-sign"></span>
                                                                        </h1>
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                </div>
                                                                <div class="col-lg-10">
                                                                    <h1>
                                                                        <?php

                                                                        if (isset($hardware['value'])) {

                                                                            echo (string)$hardware['value'];
                                                                        }

                                                                        $extensions = $settings['syscrack_hardware_extensions'];

                                                                        if (isset($extensions[$type])) {

                                                                            ?>
                                                                            <span class="lgall"><?= $extensions[$type] ?></span>
                                                                            <?php
                                                                        }
                                                                        ?>
                                                                    </h1>
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
                                    <div class="col-lg-4">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
                Render::view('syscrack/templates/template.footer', array('breadcrumb' => true));
            ?>
    </body>
</html>
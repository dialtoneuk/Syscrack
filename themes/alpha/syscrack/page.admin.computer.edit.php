<?php

use Framework\Application\Container;
use Framework\Application\Render;
use Framework\Application\Settings;
use Framework\Syscrack\Game\Computers;
use Framework\Syscrack\Game\Utilities\PageHelper;

$computers = new Computers();

$pagehelper = new PageHelper();

$session = Container::getObject('session');

if ($session->isLoggedIn()) {

    $session->updateLastAction();
}

if (isset($softwares) == false) {

    $softwares = new \Framework\Syscrack\Game\Softwares();
}
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
        <div class="col-sm-12">
            <h5 style="color: #ababab" class="text-uppercase">
                <span class="badge"><?= $computer->type ?></span> <?= $computer->ipaddress ?>
            </h5>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div>
                <!-- Nav tabs -->
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#softwares" aria-controls="softwares" role="tab"
                                                              data-toggle="tab">Softwares</a></li>
                    <li role="presentation"><a href="#hardwares" aria-controls="hardwares" role="tab"
                                                              data-toggle="tab">Hardwares</a></li>

                    <?php

                        if ( $computer->type == Settings::getSetting('syscrack_computers_market_type') )
                        {

                            ?>
                                <li role="presentation"><a href="#market" aria-controls="market" role="tab"
                                                       data-toggle="tab">Market</a></li>
                            <?php
                        }
                    ?>
                    <li style="float: right;"><a href="/admin/computer/">Home <span class="glyphicon glyphicon-arrow-right"></span> </a></li>
                    <li style="float: right;"><a href="/game/internet/<?= $computer->ipaddress ?>">View <span class="glyphicon glyphicon-search"></span> </a></li>
                </ul>
                <!-- Tab panes -->
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="softwares">
                        <div class="row">
                            <div class="col-sm-9">
                                <h5 style="color: #ababab" class="text-uppercase">
                                    Main Hard Drive
                                </h5>
                                <?php
                                Render::view('syscrack/templates/template.softwares', array('ipaddress' => $computer->ipaddress, 'computers' => $computers, 'hideoptions' => true, "local" => true));
                                ?>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <form action="/admin/computer/edit/<?= $computer->computerid ?>/" method="post">
                                            <button class="btn btn-success" style="width: 100%;" id="addsoftwaresbutton" type="button" data-toggle="collapse" data-target="#addsoftwares" aria-expanded="false" aria-controls="addsoftwares" onclick="$">
                                                <span class="glyphicon glyphicon-plus-sign"></span> Add Software
                                            </button>
                                            <div class="collapse" id="addsoftwares" style="margin-top: 1.5%;">
                                                <div class="panel panel-info">
                                                    <div class="panel-body">
                                                        <div class="row">
                                                            <div class="col-sm-4">
                                                                <div class="row">
                                                                    <div class="col-sm-12">
                                                                        <div class="input-group">
                                                                        <span class="input-group-addon"
                                                                              id="basic-addon1"><span
                                                                                    class="glyphicon glyphicon-font"></span></span>
                                                                            <input type="text" class="form-control"
                                                                                   placeholder="Software Name" name="name"
                                                                                   aria-describedby="basic-addon1">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row" style="margin-top: 2.5%;">
                                                                    <div class="col-sm-12">
                                                                        <div class="input-group">
                                                                        <span class="input-group-addon"
                                                                              id="basic-addon1"><span
                                                                                    class="glyphicon glyphicon glyphicon-signal"></span></span>
                                                                            <input type="number" step="0.1"
                                                                                   class="form-control"
                                                                                   placeholder="Software Level" name="level"
                                                                                   aria-describedby="basic-addon1">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row" style="margin-top: 2.5%;">
                                                                    <div class="col-sm-12">
                                                                        <div class="checkbox">
                                                                            <label><input type="checkbox" name="schema"
                                                                                          checked></label>
                                                                            <span style="font-size: 10px;">Add to Schema if a schema file is present.</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-sm-12">
                                                                        <div class="checkbox">
                                                                            <label><input type="checkbox" name="editable"
                                                                                          checked></label>
                                                                            <span style="font-size: 10px;">Editable</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-sm-12">
                                                                        <div class="checkbox">
                                                                            <label><input type="checkbox"
                                                                                          name="anondownloads"></label>
                                                                            <span style="font-size: 10px;">Allow anonymous downloads (Used with download servers for serving files with out logging in)</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-4">
                                                                <div class="row">
                                                                    <div class="col-sm-12">
                                                                        <div class="input-group">
                                                                        <span class="input-group-addon"
                                                                              id="basic-addon1"><span
                                                                                    class="glyphicon glyphicon-list"></span></span>
                                                                            <input type="text" class="form-control"
                                                                                   placeholder="Uniquename" name="uniquename"
                                                                                   aria-describedby="basic-addon1">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row" style="margin-top: 5%;">
                                                                    <div class="col-sm-12">
                                                                        <div class="well">
                                                                            <p>
                                                                                vspam, vminer, antivirus, research, cracker, hasher, text, firewall, nmap, vddos, breaker, collector
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-sm-12">
                                                                        <div class="input-group">
                                                                        <span class="input-group-addon"
                                                                              id="basic-addon1"><span
                                                                                    class="glyphicon glyphicon-hdd"></span></span>
                                                                            <input type="number" class="form-control"
                                                                                   placeholder="Size" name="size"
                                                                                   aria-describedby="basic-addon1">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-4" style="max-height: 310px;">
                                                                <?php
                                                                Render::view('syscrack/templates/template.form', array('form_elements' => [
                                                                    [
                                                                        'type' => 'textarea',
                                                                        'name' => 'customdata',
                                                                        'value' => '',
                                                                        'resizeable' => 'vertical'
                                                                    ]
                                                                ], 'remove_submit' => true, 'remove_form' => true));
                                                                ?>
                                                                <p style="font-size: 10px;">
                                                                    Data entered in the box above should be in a valid json
                                                                    format.
                                                                </p>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-12">
                                                                <button style="width: 100%; margin-top: 2.5%;"
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
                            <div class="col-sm-3">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <h5 style="color: #ababab" class="text-uppercase">
                                            Storage Details
                                        </h5>
                                        <div class="well-lg">
                                            <?php
                                            $hardwares = json_decode($computer->hardwares, true);
                                            $csoftwares = json_decode($computer->softwares, true);
                                            ?>
                                            <?php
                                            $usedspace = 0.0;
                                            foreach ($csoftwares as $key => $value) {
                                                $usedspace += $softwares->getSoftware($value['softwareid'])->size;
                                            }
                                            ?>
                                            <pre style="white-space:pre-wrap; max-height: 345px;">
Drive size: <?= $hardwares['harddrive']['value'] ?>mb
Used Space: <?= $usedspace ?>mb
Total Free Space: <?= $hardwares['harddrive']['value'] - $usedspace ?>mb
                                            </pre>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <h5 style="color: #ababab" class="text-uppercase">
                                            Software Actions
                                        </h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <form action="/admin/computer/edit/<?= $computer->computerid ?>/" method="post">
                                            <div class="panel panel-default">
                                                <div class="panel-body">
                                                    <select name="softwareid" class="combobox input-sm form-control">
                                                        <option></option>
                                                        <?php

                                                            foreach ( json_decode( $computer->softwares, true ) as $key=>$value )
                                                            {

                                                                if ( $softwares->softwareExists( $value['softwareid'] ) )
                                                                {

                                                                    $software = $softwares->getSoftware( $value['softwareid'] );

                                                                    ?>
                                                                        <option value="<?=$value['softwareid']?>"><?=$software->softwarename . " (" . $software->level . ")"?></option>
                                                                    <?php
                                                                }
                                                            }
                                                        ?>
                                                        <option></option>
                                                    </select>
                                                    <button style="width: 100%; margin-top: 2.5%;"
                                                            class="btn btn-danger" type="submit">
                                                        <span class="glyphicon glyphicon-fire"
                                                              aria-hidden="true"></span> Delete
                                                    </button>
                                                    <input type="hidden" name="action" value="delete">
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <form action="/admin/computer/edit/<?= $computer->computerid ?>/" method="post">
                                            <div class="panel panel-default">
                                                <div class="panel-body">
                                                    <div class="row">
                                                        <div class="col-sm-12">
                                                            <select name="softwareid"
                                                                    class="combobox input-sm form-control">
                                                                <option></option>
                                                                <?php

                                                                foreach ( json_decode( $computer->softwares, true ) as $key=>$value )
                                                                {

                                                                    if ( $softwares->softwareExists( $value['softwareid'] ) )
                                                                    {

                                                                        $software = $softwares->getSoftware( $value['softwareid'] );

                                                                        ?>
                                                                        <option value="<?=$value['softwareid']?>"><?=$software->softwarename . " (" . $software->level . ")"?></option>
                                                                        <?php
                                                                    }
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row" style="margin-top: 2.5%;">
                                                        <div class="col-sm-12">
                                                            <select name="task" class="combobox input-sm form-control">
                                                                <option></option>
                                                                <option value="install">Install</option>
                                                                <option value="uninstall">Uninstall</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-12">
                                                            <button style="width: 100%; margin-top: 2.5%;"
                                                                    class="btn btn-info" type="submit">
                                                                <span class="glyphicon glyphicon-arrow-up"
                                                                      aria-hidden="true"></span> Install/Uninstall
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <input type="hidden" name="action" value="stall">
                                                </div>
                                            </div>
                                        </form>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                    <div role="tabpanel" class="tab-pane" id="hardwares">
                        <div class="row" style="margin-top: 2.5%;">
                            <div class="col-sm-8">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <?php

                                        $hardwares = json_decode( $computer->hardwares, true );

                                        foreach ($hardwares as $type => $hardware) {

                                            $icons = Settings::getSetting('syscrack_hardware_icons');

                                            ?>
                                            <div class="panel panel-info">
                                                <div class="panel-heading">
                                                    <?= $type ?>
                                                </div>
                                                <div class="panel-body">
                                                    <div class="row">
                                                        <div class="col-sm-2">
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
                                                        <div class="col-sm-10">
                                                            <h1>
                                                                <?php

                                                                if (isset($hardware['value'])) {

                                                                    echo (string)$hardware['value'];
                                                                }

                                                                $extensions = Settings::getSetting('syscrack_hardware_extensions');

                                                                if (isset($extensions[$type])) {

                                                                    ?>
                                                                    <span class="small"><?= $extensions[$type] ?></span>
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
                        </div>
                    </div>
                    <?php

                    if ( $computer->type == Settings::getSetting('syscrack_computers_market_type') )
                    {

                        $market = new \Framework\Syscrack\Game\Market();

                        $stock = $market->getStock( $computer->computerid );
                        $purchases = $market->getPurchases( $computer->computerid );

                        ?>
                            <div role="tabpanel" class="tab-pane" id="market">
                                <div class="row" style="margin-top: 2.5%;">
                                    <div class="col-sm-8">
                                        <?php
                                            if ( empty( $stock ) )
                                            {

                                                ?>
                                                    <div class="panel panel-danger">
                                                        <div class="panel-body">
                                                            No stock items found
                                                        </div>
                                                    </div>
                                                <?php
                                            }
                                            else
                                            {
                                                foreach ( $stock as $key=>$value )
                                                {

                                                    ?>
                                                        <div class="row">
                                                            <div class="col-sm-12">
                                                                <div class="panel panel-info">
                                                                    <div class="panel-heading">
                                                                        <?=$key?>
                                                                    </div>
                                                                    <div class="panel-body">
                                                                        <div class="well">
                                                                            <?php
                                                                                print_r( $value );
                                                                            ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php
                                                }
                                            }
                                        ?>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="panel panel-default">
                                            <div class="panel-body">
                                                <div class="well">
                                                    <?php print_r( $purchases )?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <form action="/admin/computer/edit/<?= $computer->computerid ?>/" method="post">
                                            <button class="btn btn-success" style="width: 100%;" id="addstockbutton" type="button" data-toggle="collapse" data-target="#addstock" aria-expanded="false" aria-controls="addstock">
                                                <span class="glyphicon glyphicon-plus-sign"></span> Add Stock Item
                                            </button>
                                            <div class="collapse" id="addstock" style="margin-top: 1.5%;">
                                                <div class="panel panel-default">
                                                    <div class="panel-body">
                                                        <div class="row">
                                                            <div class="col-sm-6">
                                                                <div class="input-group input-group-lg">
                                                                    <span class="input-group-addon" id="sizing-addon1">Name</span>
                                                                    <input type="text" name="name" class="form-control" placeholder="My Hardware" aria-describedby="sizing-addon1">
                                                                </div>
                                                                <div class="input-group input-group-sm"  style="margin-top: 1.5%;">
                                                                    <span class="input-group-addon" id="sizing-addon1">Cost</span>
                                                                    <input type="number" name="cost" class="form-control" placeholder="100" aria-describedby="sizing-addon1">
                                                                </div>
                                                                <div class="input-group input-group-sm"  style="margin-top: 1.5%;">
                                                                    <span class="input-group-addon" id="sizing-addon1">Quantity</span>
                                                                    <input type="number" name="quantity" class="form-control" placeholder="10000" aria-describedby="sizing-addon1">
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <div class="input-group input-group-sm" style="width: 100%;">
                                                                    <select name="type" class="combobox input-sm form-control">
                                                                        <option value="hardware">hardware</option>
                                                                        <option value="software">software</option>
                                                                    </select>
                                                                </div>
                                                                <p style="margin-top: 1.5%;">
                                                                    <small>If this is a hardware, please choose which hardware component the user will buy, and then its power</small>
                                                                </p>
                                                                <div class="input-group input-group-sm" style="margin-top: 1.5%;">
                                                                    <span class="input-group-addon" id="sizing-addon1">Hardware</span>
                                                                    <input type="text" name="hardware" class="form-control" placeholder="cpu" aria-describedby="sizing-addon1">
                                                                </div>
                                                                <div class="input-group input-group-sm" style="margin-top: 1.5%;">
                                                                    <span class="input-group-addon" id="sizing-addon1">Power</span>
                                                                    <input type="text" name="value" class="form-control" placeholder="cpu" aria-describedby="sizing-addon1">
                                                                </div>
                                                                <p style="margin-top: 1.5%;">
                                                                    <small>If instead you are selling a software, please provide a software id to copy</small>
                                                                </p>
                                                                <div class="input-group input-group-sm" style="margin-top: 1.5%;">
                                                                    <span class="input-group-addon" id="sizing-addon1">Software</span>
                                                                    <input type="text" name="softwareid" class="form-control" placeholder="1" aria-describedby="sizing-addon1">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <button style="width: 100%; margin-top: 2.5%;"
                                                                class="btn btn-info" type="submit">
                                                                <span class="glyphicon glyphicon-arrow-up"
                                                                      aria-hidden="true"></span> Add Stock Item
                                                        </button>
                                                        <input type="hidden" name="action" value="stock">
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php
                    }
                    ?>

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
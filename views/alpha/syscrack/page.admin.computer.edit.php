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
        <div class="col-lg-12">
            <?php

            if (isset($_GET['error']))
                Render::view('syscrack/templates/template.alert', array('message' => $_GET['error']));
            elseif (isset($_GET['success']))
                Render::view('syscrack/templates/template.alert', array('message' => Settings::getSetting('alert_success_message'), 'alert_type' => 'alert-success'));
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <h5 style="color: #ababab" class="text-uppercase">
                <span class="badge"><?= $computer->type ?></span> <?= $computer->ipaddress ?>
            </h5>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div>

                <!-- Nav tabs -->
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#softwares" aria-controls="softwares" role="tab"
                                                              data-toggle="tab">Softwares</a></li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="softwares">
                        <div class="row">
                            <div class="col-lg-9">
                                <h5 style="color: #ababab" class="text-uppercase">
                                    Main Hard Drive
                                </h5>
                                <?php
                                Render::view('syscrack/templates/template.softwares', array('ipaddress' => $computer->ipaddress, 'computers' => $computers, 'hideoptions' => true, "local" => true));
                                ?>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <form action="/admin/computer/edit/<?= $computer->computerid ?>/" method="post">
                                            <div class="panel panel-info">
                                                <div class="panel-heading">
                                                    Add Software
                                                </div>
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
                                                                    <div class="well">
                                                                        <p>
                                                                            vpc, npc, whois, download, isp, bank, market
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-sm-12">
                                                                    <div class="input-group">
                                                                        <span class="input-group-addon"
                                                                              id="basic-addon1"><span
                                                                                    class="glyphicon glyphicon-list"></span></span>
                                                                        <input type="text" class="form-control"
                                                                               placeholder="Uniquename" name="name"
                                                                               aria-describedby="basic-addon1">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row" style="margin-top: 2.5%;">
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
                                                                    class="btn btn-info" type="submit">
                                                                <span class="glyphicon glyphicon-plus"
                                                                      aria-hidden="true"></span> Add
                                                            </button>
                                                            <input type="hidden" name="action" value="add">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="row">
                                    <div class="col-lg-12">
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
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row" style="margin-top: 2.5%;">
                                                        <div class="col-sm-12">
                                                            <select name="task" class="combobox input-sm form-control">
                                                                <option>Install</option>
                                                                <option>Uninstall</option>
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
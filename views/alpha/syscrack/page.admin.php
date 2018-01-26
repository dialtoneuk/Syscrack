<?php

use Framework\Application\Container;
use Framework\Application\Render;
use Framework\Application\Settings;
use Framework\Syscrack\Game\Utilities\PageHelper;
use Framework\Syscrack\User;

$session = Container::getObject('session');

if ($session->isLoggedIn()) {

    $session->updateLastAction();
}

if (isset($user) == false) {

    $user = new User();
}

if (isset($pagehelper) == false) {

    $pagehelper = new PageHelper();
}
?>
<html>

<?php

Render::view('syscrack/templates/template.header', array('pagetitle' => 'Syscrack | Admin'));
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

        <?php

        Render::view('syscrack/templates/template.admin.options');
        ?>
        <div class="col-lg-8">
            <h5 style="color: #ababab" class="text-uppercase">
                Admin
            </h5>
            <div class="row">
                <div class="col-lg-6">
                    <div class="panel panel-success">
                        <div class="panel-heading">
                            Users
                        </div>
                        <div class="panel-body text-center">
                            <h3><?= $user->getUsersCount() ?></h3><span class="small"> Users</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="panel panel-success">
                        <div class="panel-heading">
                            Online
                        </div>
                        <div class="panel-body text-center">
                            <h3><?= $session->getActiveSessions()->count() ?></h3><span class="small"> Users</span>
                        </div>
                    </div>
                </div>
            </div>
            <h5 style="color: #ababab" class="text-uppercase">
                Notices
            </h5>
            <?php
            if (Settings::getSetting('developer_disabled') == false) {

                ?>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="panel panel-danger">
                            <div class="panel-heading">
                                Warning
                            </div>
                            <div class="panel-body">
                                Your developer area is still enabled, it is highly suggested that if you are currently
                                running a live version of Syscrack that you
                                <strong>disable the developer area.</strong> Please <a href="/developer/disable/">click
                                    here to do so!</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
            <div class="row">
                <?php
                if (Settings::getSetting('database_encrypt_connection') == false) {

                    ?>
                    <div class="col-sm-12">
                        <div class="panel panel-warning">
                            <div class="panel-heading">
                                Encryption is turned off
                            </div>
                            <div class="panel-body">
                                Your database encryption setting is currently turned off, this means that the
                                information you give
                                below will not be encrypted and will be viewable in plain-text from anybody with root
                                access. This
                                setting should only be used if mcrypt is not functioning correctly on your system.
                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>

    <?php

    Render::view('syscrack/templates/template.footer', array('breadcrumb' => true));
    ?>
</div>
</body>
</html>

<?php

use Framework\Application\Container;
use Framework\Application\Render;
use Framework\Application\Settings;
use Framework\Syscrack\Game\Internet;
use Framework\Syscrack\Game\Utilities\PageHelper;

$internet = new Internet();

$pagehelper = new PageHelper();

$session = Container::getObject('session');

if ($session->isLoggedIn()) {

    $session->updateLastAction();
}

if (isset($ipaddress) == false)
    $ipaddress = $internet->getComputerAddress(Settings::getSetting('syscrack_whois_computer'));
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

            <?php
            if ($internet->hasCurrentConnection()) {

                if ($ipaddress !== $internet->getCurrentConnectedAddress()) {

                    ?>
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            Notice
                        </div>

                        <div class="panel-body">
                            You are currently connected to <a
                                    href="/game/internet/<?= $internet->getCurrentConnectedAddress() ?>/"><?= $internet->getCurrentConnectedAddress() ?>
                                ,</a> <a href="/game/internet/<?= $internet->getCurrentConnectedAddress() ?>/logout">Logout?</a>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>
            <div class="row">

                <?php

                if ($pagehelper->isCurrentlyConnected($ipaddress) == false) {
                    Render::view('syscrack/templates/template.browser', array('ipaddress' => $ipaddress, 'internet' => $internet, 'pagehelper' => $pagehelper));
                } else {

                    Render::view('syscrack/templates/template.computer', array('ipaddress' => $ipaddress, 'internet' => $internet, 'pagehelper' => $pagehelper, 'hideoptions' => false));
                }

                Render::view('syscrack/templates/template.tools', array('ipaddress' => $ipaddress, 'internet' => $internet, 'pagehelper' => $pagehelper));
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
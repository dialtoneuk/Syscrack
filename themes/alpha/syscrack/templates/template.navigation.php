<?php

use Framework\Application\Container;
use Framework\Application\Settings;
use Framework\Syscrack\Game\Computer;
use Framework\Syscrack\Game\Utilities\PageHelper;

$session = Container::getObject('session');

if (isset($computer_controller) == false) {

    $computer_controller = new Computer();
}

if (isset($pagehelper) == false) {

    $pagehelper = new PageHelper();
}

//TODO: Look for a fix with this session shit

if ( empty( $_SESSION['current_computer'] ) )
{

    if ( $session->isLoggedIn() )
    {

        \Framework\Application\Render::redirect('/framework/error/session/');
    }
}
?>
<nav class="navbar navbar-default" style="margin-top: 15px;">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>

            <?php

            if ($session->isLoggedIn()) {

                ?>

                <a class="navbar-brand" href="/game/">SC://</a>
                <?php
            } else {

                ?>
                <a class="navbar-brand" href="/">SC://</a>
                <?php
            }

            if ($session->isLoggedIn()) {


            }
            ?>
        </div>
        <div class="collapse navbar-collapse" id="navbar">
            <div class="nav navbar-left navbar-fix">
                <?php
                if ($session->isLoggedIn()) {

                    if ( empty( $_SESSION['current_computer'] )  == false )
                    {

                        if ($computer_controller->hasCurrentComputer()) {

                            ?>

                            <a class="navbar-brand" style="font-size: 12px" href="/game/computer/" ata-toggle="tooltip"
                               data-placement="auto" title="Current IP Address">
                            <span class="glyphicon glyphicon-arrow-down" data-toggle="tooltip" data-placement="auto"
                                  title="Address"></span> <?= $computer_controller->getComputer($computer_controller->getCurrentUserComputer())->ipaddress ?>
                            </a>
                            <?php
                        }
                    }

                    ?>
                        <a class="navbar-brand" style="font-size: 12px" href="/finances/" data-toggle="tooltip"
                           data-placement="auto" title="Current balance of all accounts">
                            <span class="glyphicon glyphicon-gbp"></span> <?= $pagehelper->getCash() ?>
                        </a>
                    <?php

                    if ( empty( $_SESSION['connected_ipaddress'] ) == false )
                    {

                        ?>
                        <a class="navbar-brand" style="font-size: 12px; color: #5cb85c;" href="/game/internet/<?= $_SESSION['connected_ipaddress'] ?>/" data-toggle="tooltip"
                           data-placement="auto" title="Connected">
                            <span class="glyphicon glyphicon-arrow-up"></span> <?= $_SESSION['connected_ipaddress'] ?>
                        </a>
                        <?php
                    }

                }
                ?>
            </div>
            <ul class="nav navbar-nav navbar-right">

                <?php
                if ($session->isLoggedIn()) {

                    $user = new \Framework\Syscrack\User();

                    if ( empty( $_SESSION['current_computer'] )  == false )
                    {
                        ?>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle text-uppercase" data-toggle="dropdown" role="button"
                               aria-haspopup="true" data-toggle="tooltip" data-placement="auto" title="Processes"
                               aria-expanded="false">Procs</a>
                            <ul class="dropdown-menu">
                                <li><a href="/processes/">All Processes</a></li>
                                <li><a href="/processes/computer/<?= $computer_controller->getCurrentUserComputer() ?>">Current
                                        Machine Processes</a></li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle text-uppercase" data-toggle="dropdown" role="button"
                               aria-haspopup="true" data-toggle="tooltip" data-placement="auto" title="Finances"
                               aria-expanded="false">Cash</a>
                            <ul class="dropdown-menu">
                                <li><a href="/finances/">Finance</a></li>
                                <li><a href="/finances/transfer/">Transfer</a></li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle text-uppercase" data-toggle="dropdown" role="button"
                               aria-haspopup="true" data-toggle="tooltip" data-placement="auto" title="World Wide Web"
                               aria-expanded="false">WWW
                                <?php
                                    if (isset($_SESSION['connected_ipaddress']))
                                    {
                                    ?>
                                        <span class="badge" style="background-color: #5cb85c;" id="activesession">ACTIVE</span>
                                    <?php
                                    }
                                ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a href="/game/internet/">Browser</a></li>
                                <?php

                                if (isset($_SESSION['connected_ipaddress'])) {

                                    ?>
                                    <li role="separator" class="divider"></li>
                                    <li><a style="color: #5cb85c;" href="/game/internet/<?= $_SESSION['connected_ipaddress'] ?>"><?= $_SESSION['connected_ipaddress'] ?></a></li>
                                    <li role="separator" class="divider"></li>
                                    <?php
                                }
                                ?>
                                <li><a href="/game/addressbook/">Address Database</a></li>
                                <li><a href="/game/accountbook/">Account Database</a></li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle text-uppercase" data-toggle="dropdown" role="button"
                               aria-haspopup="true" data-toggle="tooltip" data-placement="auto" title="Current PC"
                               aria-expanded="false">Root</a>
                            <ul class="dropdown-menu">
                                <li><a href="/computer/">Files</a>
                                <li><a href="/computer/log/">Access Logs</a></li>
                                <li><a href="/computer/processes/">Processes</a></li>
                                <li><a href="/computer/hardware/">Hardware</a></li>

                                <?php
                                if ($pagehelper->getInstalledCollector() !== null) {

                                    ?>
                                    <li role="separator" class="divider"></li>
                                    <li><a href="/computer/collect/">Collect</a></li>
                                    <?php
                                }

                                if ($computer_controller->hasType($computer_controller->getCurrentUserComputer(), Settings::getSetting('syscrack_software_research_type'), true)) {

                                    ?>
                                    <li role="separator" class="divider"></li>
                                    <li><a href="/computer/research/">Research</a></li>
                                    <?php
                                }
                                ?>
                            </ul>
                        </li>
                        <?php
                        if ($user->isAdmin($session->getSessionUser())) {

                            ?>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle text-uppercase" data-toggle="dropdown" role="button"
                                   aria-haspopup="true" data-toggle="tooltip" data-placement="auto"
                                   title="Admin Control Panel" aria-expanded="false">Admin</a>
                                <ul class="dropdown-menu">
                                    <li><a href="/admin">Control Panel</a></li>
                                    <li role="separator" class="divider"></li>
                                    <li><a href="/admin/computer">Computer Index</a></li>
                                    <li><a href="/admin/computer/creator">Computer Creator</a></li>
                                    <li role="separator" class="divider"></li>
                                    <li><a href="/admin/users/">User Index</a></li>
                                    <li><a href="/admin/users/creator">New User</a></li>
                                    <li role="separator" class="divider"></li>
                                    <li><a href="/admin/riddles/">Riddles</a></li>
                                    <li><a href="/admin/riddles/creator">Riddle Creator</a></li>
                                    <li role="separator" class="divider"></li>
                                    <li><a href="/admin/themes/">Themes</a></li>
                                    <li><a href="/admin/settings/">Settings</a></li>
                                    <li role="separator" class="divider"></li>
                                    <li><a href="/admin/reset/">Reset</a></li>
                                </ul>
                            </li>
                            <?php
                        }
                        ?>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle text-uppercase" data-toggle="dropdown" role="button"
                               aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-cog"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="/account/settings/">Account Settings</a></li>
                                <li><a href="/account/logout/">Logout</a></li>
                            </ul>
                        </li>
                        <?php
                    }
                } else {
                    ?>
                    <li><a href="/login/"><span class="glyphicon glyphicon-off"></span> Login</a></li>
                    <li><a href="/register/"><span class="glyphicon glyphicon-star"></span> Register</a></li>
                    <?php
                }
                ?>
            </ul>
        </div>
    </div>
</nav>
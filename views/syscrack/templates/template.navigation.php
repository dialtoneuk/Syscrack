<?php

    $session = \Framework\Application\Container::getObject('session');

    if( isset( $computer ) == false )
    {

        $computer = new \Framework\Syscrack\Game\Computer();
    }
?>
<nav class="navbar navbar-default" style="margin-top: 2.5%">
    <div class="container-fluid">
        <div class="navbar-header">
            <a class="navbar-brand" href="/">Syscrack</a>
            <?php
                if( $computer->hasCurrentComputer() )
                {

                    ?>

                        <a class="navbar-brand" style="font-size: 12px" href="/game/computer">
                            [<?=$computer->getComputer( $computer->getCurrentUserComputer() )->ipaddress?>]
                        </a>
                    <?php
                }
            ?>
        </div>

        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav navbar-right">

                    <?php
                        if( $session->isLoggedIn() )
                        {

                            $user = new \Framework\Syscrack\User();
                            ?>
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Game<span class="caret"></span></a>
                                    <ul class="dropdown-menu">
                                        <li><a href="/game/">Home</a></li>
                                        <li role="separator" class="divider"></li>
                                        <li><a href="/game/internet/">Internet</a></li>
                                        <li><a href="/game/ddos">DDoS</a></li>
                                        <li><a href="/game/collect">Collect</a>
                                        <li><a href="/game/addressbook">Address book</a>
                                        <li><a href="/game/accountbook">Account book</a></li>
                                        <li role="separator" class="divider"></li>
                                        <li><a href="/computer/">Computer</a>
                                        <li><a href="/computer/log">Log</a></li>
                                        <li><a href="/computer/processes">Processes</a></li>
                                        <li><a href="/computer/upgrade">Upgrade</a></li>
                                        <li role="separator" class="divider"></li>
                                        <li><a href="/game/clans/">Clans</a>
                                        <li><a href="/game/ranking/">Ranking</a>
                                    </ul>
                                </li>
                                <?php
                                    if( $user->isAdmin( $session->getSessionUser() ) )
                                    {

                                        ?>
                                        <li class="dropdown">
                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Admin<span class="caret"></span></a>
                                            <ul class="dropdown-menu">
                                                <li><a href="/admin/">Home</a></li>
                                                <li role="separator" class="divider"></li>
                                                <li><a href="/admin/npcreator/">NPC Creator</a></li>
                                                <li><a href="/admin/npcviewer/">NPC Viewer</a></li>
                                                <li role="separator" class="divider"></li>
                                                <li><a href="/admin/softwarecreator/">Software Creator</a></li>
                                            </ul>
                                        </li>
                                        <?php
                                    }
                                ?>
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?=$user->getUsername($session->getSessionUser())?><span class="caret"></span></a>
                                    <ul class="dropdown-menu">
                                        <li><a href="/account/settings/">Account Settings</a></li>
                                        <li><a href="/account/logout/">Logout</a></li>
                                    </ul>
                                </li>
                            <?php
                        }
                        else
                        {
                            ?>
                                <li><a href="/login/">Login</a></li>
                            <?php
                        }
                    ?>
            </ul>
        </div>
    </div>
</nav>
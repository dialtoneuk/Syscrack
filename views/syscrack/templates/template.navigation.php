<?php

    use Framework\Application\Container;
    use Framework\Syscrack\Game\Computers;
    use Framework\Syscrack\Game\Utilities\PageHelper;
    use Framework\Application\Settings;

    $session = Container::getObject('session');

    if( isset( $computers) == false )
    {

        $computers= new Computers();
    }

    if( isset( $pagehelper ) == false )
    {

        $pagehelper = new PageHelper();
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

                if( $session->isLoggedIn() )
                {

                    ?>

                        <a class="navbar-brand" href="/game/">Syscrack</a>
                    <?php
                }
                else
                {

                    ?>
                        <a class="navbar-brand" href="/">Syscrack</a>
                    <?php
                }

                if( $session->isLoggedIn() )
                {


                }
            ?>
        </div>
        <div class="collapse navbar-collapse" id="navbar">
            <div class="nav navbar-left navbar-fix">
                <?php
                    if( $session->isLoggedIn() )
                    {
                        if( $computers->hasCurrentComputer() )
                        {

                            ?>

                            <a class="navbar-brand" style="font-size: 12px" href="/computer/" ata-toggle="tooltip" data-placement="auto" title="Current IP Address">
                                <span class="glyphicon glyphicon-cloud" data-toggle="tooltip" data-placement="auto" title="Address"></span> <?=$computers->getComputer( $computers->getCurrentUserComputer() )->ipaddress?>
                            </a>
                            <?php
                        }

                        ?>
                            <a class="navbar-brand" style="font-size: 12px" href="/finances/" data-toggle="tooltip" data-placement="auto" title="Current balance of all accounts">
                                <span class="glyphicon glyphicon-gbp"></span> <?=$pagehelper->getCash()?>
                            </a>
                        <?php
                    }
                ?>
            </div>
            <ul class="nav navbar-nav navbar-right">

                    <?php
                        if( $session->isLoggedIn() )
                        {

                            $user = new \Framework\Syscrack\User();
                            ?>
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle text-uppercase" data-toggle="dropdown" role="button" aria-haspopup="true" data-toggle="tooltip" data-placement="auto" title="Processes" aria-expanded="false"><span class="glyphicon glyphicon-tasks"></span></a>
                                    <ul class="dropdown-menu">
                                        <li><a href="/processes/">All Processes</a></li>
                                        <li><a href="/processes/computer/<?=$computers->getCurrentUserComputer()?>">Current Machine Processes</a></li>
                                    </ul>
                                </li>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle text-uppercase" data-toggle="dropdown" role="button" aria-haspopup="true" data-toggle="tooltip" data-placement="auto" title="Finances" aria-expanded="false"><span class="glyphicon glyphicon-gbp"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="/finances/">Finance</a></li>
                                    <li><a href="/finances/transfer/">Transfer</a></li>
                                </ul>
                            </li>
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle text-uppercase" data-toggle="dropdown" role="button" aria-haspopup="true" data-toggle="tooltip" data-placement="auto" title="World Wide Web" aria-expanded="false"><span class="glyphicon glyphicon-globe"></span></a>
                                    <ul class="dropdown-menu">
                                        <?php

                                            if( isset( $_SESSION['connected_ipaddress'] ) )
                                            {

                                                ?>

                                                <li><a href="/game/internet/<?=$_SESSION['connected_ipaddress']?>">Session</a></li>
                                                <?php
                                            }
                                        ?>
                                        <li><a href="/game/internet/">Internet Browser</a></li>
                                        <li><a href="/game/addressbook/">List of Hacked IP Addresses</a></li>
                                        <li><a href="/game/accountbook/">List of Hacked Bank Accounts</a></li>
                                    </ul>
                                </li>
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle text-uppercase" data-toggle="dropdown" role="button" aria-haspopup="true" data-toggle="tooltip" data-placement="auto" title="Current PC" aria-expanded="false"><span class="glyphicon glyphicon-folder-open"></span></a>
                                    <ul class="dropdown-menu">
                                        <li><a href="/computer/">Root</a>
                                        <li><a href="/computer/log/">Access Logs</a></li>
                                        <li><a href="/computer/processes/">Active Processes</a></li>
                                        <li><a href="/computer/hardware/">System Hardware</a></li>

                                        <?php
                                            if( $pagehelper->getInstalledCollector() !== null )
                                            {

                                                ?>
                                                    <li role="separator" class="divider"></li>
                                                    <li><a href="/computer/collect/">Collect</a></li>
                                                <?php
                                            }

                                            if( $computers->hasType( $computers->getCurrentUserComputer(), Settings::getSetting('syscrack_software_research_type'), true )  !== null )
                                            {

                                                ?>
                                                    <li role="separator" class="divider"></li>
                                                    <li><a href="/computer/research/">Research</a></li>
                                                <?php
                                            }
                                        ?>
                                    </ul>
                                </li>
                                <?php
                                    if( $user->isAdmin( $session->getSessionUser() ) )
                                    {

                                        ?>
                                        <li class="dropdown">
                                            <a href="#" class="dropdown-toggle text-uppercase" data-toggle="dropdown" role="button" aria-haspopup="true" data-toggle="tooltip" data-placement="auto" title="Admin Control Panel"aria-expanded="false"><span class="glyphicon glyphicon-list-alt"></span></a>
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
                                                <li><a href="/admin/reset/">Reset</a></li>
                                            </ul>
                                        </li>
                                        <?php
                                    }
                                ?>
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle text-uppercase" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-cog"></span></a>
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
                                <li><a href="/login/"><span class="glyphicon glyphicon-off"></span> Login</a></li>
                                <li><a href="/register/"><span class="glyphicon glyphicon-star"></span> Register</a></li>
                            <?php
                        }
                    ?>
            </ul>
        </div>
    </div>
</nav>
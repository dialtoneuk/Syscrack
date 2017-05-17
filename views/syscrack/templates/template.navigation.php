<?php

    use Framework\Application\Container;
    use Framework\Syscrack\Game\Computer;
    use Framework\Syscrack\Game\Utilities\PageHelper;

    $session = Container::getObject('session');

    if( isset( $computer ) == false )
    {

        $computer = new Computer();
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
                        if( $computer->hasCurrentComputer() )
                        {

                            ?>

                            <a class="navbar-brand" style="font-size: 12px" href="/computer/">
                                <span class="glyphicon glyphicon-cloud" data-toggle="tooltip" data-placement="auto" title="Address"></span> <?=$computer->getComputer( $computer->getCurrentUserComputer() )->ipaddress?>
                            </a>
                            <?php
                        }

                        ?>
                            <a class="navbar-brand" style="font-size: 12px" href="/finances/">
                                <span class="glyphicon glyphicon-gbp" data-toggle="tooltip" data-placement="auto" title="Cash"></span> <?=$pagehelper->getCash()?>
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
                                    <a href="#" class="dropdown-toggle text-uppercase" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-globe"></span> Internet</a>
                                    <ul class="dropdown-menu">
                                        <li><a href="/game/internet">Browser</a></li>

                                        <?php

                                            if( isset( $_SESSION['connected_ipaddress'] ) )
                                            {

                                                ?>

                                                    <li><a href="/game/internet/<?=$_SESSION['connected_ipaddress']?>">Session</a></li>
                                                <?php
                                            }
                                        ?>
                                        <li><a href="/game/addressbook">Address book</a>
                                        <li><a href="/game/accountbook">Account book</a></li>
                                    </ul>
                                </li>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle text-uppercase" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-hdd"></span> Computer</a>
                                <ul class="dropdown-menu">
                                    <li><a href="/computer/">Desktop</a>
                                    <li><a href="/computer/log">Log</a></li>
                                    <li><a href="/computer/processes">Processes</a></li>
                                    <li><a href="/computer/hardware">Hardware</a></li>

                                    <?php
                                        if( $pagehelper->getInstalledCollector() !== null )
                                        {

                                            ?>
                                            <li><a href="/computer/collect">Collect</a></li>
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
                                            <a href="#" class="dropdown-toggle text-uppercase" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-briefcase"></span> Admin</a>
                                            <ul class="dropdown-menu">
                                                <li><a href="/admin">Home</a></li>
                                                <li role="separator" class="divider"></li>
                                                <li><a href="/admin/computer">Computer Viewer</a></li>
                                                <li><a href="/admin/computer/creator">Computer Creator</a></li>
                                            </ul>
                                        </li>
                                        <?php
                                    }
                                ?>
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle text-uppercase" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-cog"></span> Settings</a>
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
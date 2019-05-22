<nav class="navbar navbar-default" style="margin-top: 12px;">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>

            <?php

            if ( @$model->session['loggedin'] ) {

                ?>

                <a class="navbar-brand" href="/game/">SC://</a>
                <?php
            } else {

                ?>
                <a class="navbar-brand" href="/">SC://</a>
                <?php
            }
            ?>
        </div>
        <div class="collapse navbar-collapse" id="navbar">
            <div class="nav navbar-left navbar-fix">
                <?php
                if (@$model->session["loggedin"]) {
                        if ( @$computer ) {

                            ?>

                            <a class="navbar-brand" style="font-size: 12px" href="/game/computer/" ata-toggle="tooltip"
                               data-placement="auto" title="Current IP Address">
                            <span class="glyphicon glyphicon-arrow-down" data-toggle="tooltip" data-placement="auto"
                                  title="Address"></span> <?= @$computer->ipaddress ?>
                            </a>
                            <?php
                        }
                    ?>
                        <a class="navbar-brand" style="font-size: 12px" href="/finances/" data-toggle="tooltip"
                           data-placement="auto" title="Current balance of all accounts">
                            <span class="glyphicon glyphicon-gbp"></span> <?=@$cash ?>
                        </a>
                    <?php

                    if( @$connection )
                    {
	                    ?>
                            <a class="navbar-brand" style="font-size: 12px; color: #5cb85c;" href="/game/internet/<?=@$connection?>/" data-toggle="tooltip"
                               data-placement="auto" title="Connected">
                                <span class="glyphicon glyphicon-arrow-up"></span> <?=@$connection ?>
                            </a>
	                    <?php
                    }
                }
                ?>
            </div>
            <ul class="nav navbar-nav navbar-right">

                <?php
                if (@$model->session["loggedin"]) {
                    if ( @$computer )
                    {
                        ?>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle text-uppercase" data-toggle="dropdown" role="button"
                               aria-haspopup="true" data-toggle="tooltip" data-placement="auto" title="Processes"
                               aria-expanded="false">Procs</a>
                            <ul class="dropdown-menu">
                                <li><a href="/processes/">All Processes</a></li>
                                <li><a href="/processes/computer/<?= @$computer->computerid ?>">Current
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
                                    if ( @$connection )
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

                                if ( @$connection ) {

                                    ?>
                                    <li role="separator" class="divider"></li>
                                    <li><a style="color: #5cb85c;" href="/game/internet/<?=@$connection?>"><?=@$connection?></a></li>
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
                            </ul>
                        </li>
                        <?php
                        if ( @$model->admin )
                        {

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
                               aria-haspopup="true" aria-expanded="false"><?=$model->user["username"]?></a>
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
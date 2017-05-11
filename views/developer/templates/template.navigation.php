<?php

    use Framework\Application\Settings;

?>
<nav class="navbar navbar-default" style="margin-top: 2.5%">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/developer/">Framework <?=Settings::getSetting('framework_version')?></a>
        </div>

        <div class="collapse navbar-collapse" id="navbar">
            <ul class="nav navbar-nav">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-tasks"></span> Connection</a>
                    <ul class="dropdown-menu">
                        <li><a href="/developer/connection/">Connection Status</a></li>
                        <li><a href="/developer/connection/creator/">Connection Creator</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-tasks"></span> Database</a>
                    <ul class="dropdown-menu">
                        <li><a href="/developer/migrator/">Migrator</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-tasks"></span> Framework</a>
                    <ul class="dropdown-menu">
                        <li><a href="/developer/errors/">Errors</a></li>
                        <li><a href="/developer/routes/">Routes</a></li>
                        <li><a href="/developer/settings/">Settings</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-tasks"></span> Developer</a>
                    <ul class="dropdown-menu">
                        <li><a href="/developer/disable/">Disable</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
<?php
    use Framework\Application\Settings;
    use Framework\Application\Utilities\IPAddress;
use Framework\Application\Render;
?>

<html lang="en">

    <?php

        Render::view('developer/templates/template.header', array( 'pagetitle' => 'Developer'));
    ?>
    <body>
        <div class="container">

            <?php

                Render::view('developer/templates/template.navigation');
            ?>
            <div class="row">
                <div class="col-lg-12">
                    <?php

                        if( isset( $_GET['error'] ) )
                            Render::view('syscrack/templates/template.alert', array( 'message' => $_GET['error'] ) );
                        elseif( isset( $_GET['success'] ) )
                            Render::view('syscrack/templates/template.alert', array( 'message' => $settings['alert_success_message'), 'alert_type' => 'alert-success' ) );
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <h5 style="color: #ababab" class="text-uppercase">
                        Hello, <?=IPAddress::getAddress()?>
                    </h5>
                    <p class="lead">
                        Welcome to Framework <?=$settings['framework_version')?>, you have reached the developer section...
                    </p>
                    <p>
                        The developer area can be used to access useful tools which modify and edit the framework to your
                        desired parameters. It is <strong>very important that you run the
                            <a href="/developer/disable/">Disable Developer Section</a>
                            tool when you officially launch your web-application</strong> to the public. If not, anybody can access this
                        developer section and anybody will be able to edit your settings, view your <strong>database password and username</strong>
                        and many other horrible things!
                    </p>

                    <p>
                        This section was created to make it easier for you to work with the framework, it includes a detailed
                        error logger, page viewer and various database tools which make it easier for you to work with
                        your database.
                    </p>
                </div>
                <div class="col-md-6">
                    <h5 style="color: #ababab" class="text-uppercase">
                        Tools
                    </h5>
                    <div class="list-group">
                        <div class="list-group">
                            <a href="/<?=$settings['developer_page')?>/connection/" class="list-group-item">
                                <h4 class="list-group-item-heading">Connection Status</h4>
                                <p class="list-group-item-text">Check your connection status and make sure your database is working.</p>
                            </a>
                            <a href="/<?=$settings['developer_page')?>/connection/creator/" class="list-group-item">
                                <h4 class="list-group-item-heading">Connection Creator</h4>
                                <p class="list-group-item-text">Create your connection file and link your database to the framework.</p>
                            </a>
                        </div>
                    </div>
                    <div class="list-group">
                        <a href="/<?=$settings['developer_page')?>/migrator/" class="list-group-item">
                            <h4 class="list-group-item-heading">Migrator</h4>
                            <p class="list-group-item-text">Migrate using json and populate your database with data.</p>
                        </a>
                        <a href="/<?=$settings['developer_page')?>/errors/" class="list-group-item">
                            <h4 class="list-group-item-heading">Errors</h4>
                            <p class="list-group-item-text">View the errors which have occurred.</p>
                        </a>
                        <a href="/<?=$settings['developer_page')?>/settings/" class="list-group-item">
                            <h4 class="list-group-item-heading">Settings</h4>
                            <p class="list-group-item-text">Make changes to the frameworks settings as well as create new ones.</p>
                        </a>
                        <a href="/<?=$settings['developer_page')?>/routes/" class="list-group-item">
                            <h4 class="list-group-item-heading">Routes</h4>
                            <p class="list-group-item-text">View the current routes of your page classes.</p>
                        </a>
                    </div>
                    <div class="list-group">
                        <a href="/<?=$settings['developer_page')?>/disable/" class="list-group-item list-group-item-danger">
                            <h4 class="list-group-item-heading">Disable</h4>
                            <p class="list-group-item-text">Disable the developer section, this is recommended if you are currently live!</p>
                        </a>
                    </div>
                </div>
            </div>

            <?php

                Render::view('developer/templates/template.footer', array( 'breadcrumb' => true ));
            ?>
        </div>
    </body>
</html>

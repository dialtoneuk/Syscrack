<?php
    use Framework\Application\Settings;
    use Framework\Application\Utilities\IPAddress;

?>

<html lang="en">

    <?php

        Flight::render('developer/templates/template.header', array( 'pagetitle' => 'Developer Area'));

        $page = Settings::getSetting('developer_page')
    ?>
    <body>
        <div class="container">

            <?php

                Flight::render('developer/templates/template.navigation');
            ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="page-header">
                        <h1>Hello <?= IPAddress::getAddress() ?></h1>
                    </div>

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

                    <p>
                        On the right you'll see a selection of various tools and visual aids. Simply click on the links to be
                        taken to the page of the tool. Remember, there are more tools hidden away in the navigation bar!
                    </p>

                    <p class="small">
                        Written by Lewis Lancaster in 2017 with the help of Gabriel Branco's beta-testing.
                    </p>
                </div>
                <div class="col-md-6">
                    <div class="page-header">
                        <h1>A Selection Of Tools</h1>
                    </div>

                    <div class="list-group">
                        <div class="list-group">
                            <a href="/<?=$page?>/connectioncreator/" class="list-group-item">Connection Creator</a>
                            <a href="/<?=$page?>/connectiontester/" class="list-group-item">Connection Tester</a>
                            <a href="/<?=$page?>/databasemigrator/" class="list-group-item">Database Migrator</a>
                            <a href="/<?=$page?>/settingsmanager/" class="list-group-item">Settings Manager</a>
                            <a href="/<?=$page?>/logger/" class="list-group-item">Logger</a>
                            <a href="/<?=$page?>/pageviewer/" class="list-group-item">Page Viewer</a>
                            <a href="/<?=$page?>/disable/" class="list-group-item">Disable Developer Section</a>
                        </div>
                    </div>
                </div>
            </div>

            <?php

                Flight::render('developer/templates/template.footer', array( 'breadcrumb' => true ));
            ?>
        </div>
    </body>
</html>

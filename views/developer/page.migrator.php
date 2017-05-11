<?php

    use Framework\Application\Settings;
    use Framework\Application\Utilities\FileSystem;

?>
<html lang="en">

    <?php

        Flight::render('developer/templates/template.header', array( 'pagetitle' => 'Database Migrator'));
    ?>
    <body>
        <div class="container">

            <?php

                Flight::render('developer/templates/template.navigation');

                if( isset( $_GET['error'] ) )
                    Flight::render('developer/templates/template.alert', array( 'message' => $_GET['error'] ) );
                elseif( isset( $_GET['success'] ) )
                    Flight::render('developer/templates/template.alert', array( 'message' => Settings::getSetting('alert_success_message'), 'alert_type' => 'alert-success' ) );
            ?>
            <div class="row">
                <div class="col-md-6">
                    <h5 style="color: #ababab" class="text-uppercase">
                        Migrator
                    </h5>
                    <p class="lead">
                        This tool is used to migrate your database. This is used to create tables and data automatically in your
                        sql or equivalent database.
                    </p>

                    <p>
                        If found, by default the framework will read the file <strong><?=Settings::getSetting('database_schema_file')?></strong> and
                        output it into field to your right. So all you need to do is press 'migrate database' to populate your database ready
                        for your current configuration.
                    </p>

                    <p>
                        To use this, insert a valid parasable json to the right and press the blue button. You will need to make sure
                        that your JSON follows the schema of the migrator, you can view the schema <a href="/">at our github.</a>
                    </p>

                    <h5 style="color: #ababab" class="text-uppercase">
                        Example
                    </h5>

                    <div class="well">
                        <?= json_encode( array(
                            'example' => [
                                    'exampleid' => 'increments',
                                    'message' => 'text'
                            ]
                        ), JSON_PRETTY_PRINT ) ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <h5 style="color: #ababab" class="text-uppercase">
                        Schema
                    </h5>
                    <form method="post">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <?php

                                    if( FileSystem::fileExists( Settings::getSetting('database_schema_file') ) )
                                    {

                                        ?>
<textarea style='resize: none; width: 100%; height: 42.5%;' name="json"><?=json_encode( FileSystem::readJson( Settings::getSetting('database_schema_file') ), JSON_PRETTY_PRINT )?></textarea>
                                        <?php
                                    }
                                    else
                                    {

                                        ?>
                                            <textarea style='resize: none; width: 100%; height: 42.5%;' name="json">{}</textarea>
                                        <?php
                                    }
                                ?>
                            </div>
                        </div>
                        <button class="btn btn-primary btn-block btn-sm" data-toggle="modal" data-target="#disablemodal">
                            Migrate Database
                        </button>
                    </form>
                </div>
            </div>

            <?php

                Flight::render('developer/templates/template.footer', array( 'breadcrumb' => true ));
            ?>
        </div>
    </body>
</html>
<?php

    use Framework\Application\Container;
    use Framework\Application\Settings;

    $error_handler = Container::getObject('application')->getErrorHandler();
?>
<html lang="en">

    <?php

        Flight::render('developer/templates/template.header', array( 'pagetitle' => 'Developer / Errors'));
    ?>
    <body>
        <div class="container">

            <?php

                Flight::render('developer/templates/template.navigation');
            ?>
            <div class="row">
                <div class="col-lg-12">
                    <?php

                        if( isset( $_GET['error'] ) )
                            Flight::render('syscrack/templates/template.alert', array( 'message' => $_GET['error'] ) );
                        elseif( isset( $_GET['success'] ) )
                            Flight::render('syscrack/templates/template.alert', array( 'message' => Settings::getSetting('alert_success_message'), 'alert_type' => 'alert-success' ) );
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <h5 style="color: #ababab" class="text-uppercase">
                        Errors
                    </h5>
                    <p class="lead">
                        View all of the errors which have been occurring on your framework, and fix them!
                    </p>

                    <p>
                        To delete a log, press the delete button under this paragraph. To view more detail about
                        an error, simply click onto the error and you will be taken to a more detailed screen.
                    </p>
                    <h5 style="color: #ababab" class="text-uppercase">
                        Actions
                    </h5>
                    <form method="post">
                        <button class="btn btn-sm btn-danger btn-block" name="action" value="delete" type="submit">
                            Clear Errors
                        </button>
                    </form>
                </div>

                <?php
                    if( \Framework\Application\Settings::getSetting('error_logging') == false )
                    {
                        ?>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="panel panel-danger">
                                        <div class="panel-heading">
                                            <h3 class="panel-title">Hey there</h3>
                                        </div>
                                        <div class="panel-body">
                                            <p>
                                                Error Logging is currently disabled by the framework, so there's no point in showing you anything. Maybe
                                                you need to <a href="/developer/settingsmanager/">activate logging?</a>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php

                    }
                    else
                    {

                        ?>

                            <div class="col-md-6">
                                <h5 style="color: #ababab" class="text-uppercase">
                                    A Selection Of Most Recent Errors
                                </h5>

                                <?php
                                    $error_log = $error_handler->getErrorLog();

                                    if( empty( $error_log ) )
                                    {

                                        ?>
                                            <div class="panel panel-info">
                                                <div class="panel-heading">
                                                    Information
                                                </div>
                                                <div class="panel-body">
                                                    No errors were found.
                                                </div>
                                            </div>
                                        <?php
                                    }
                                    else
                                    {

                                        for( $key = count( $error_log ), $count=1; $key > 0; $key--, $count++)
                                        {

                                            $value = $error_log[ $key - 1 ];

                                            if( $count > Settings::getSetting('logger_max_errors') )
                                                continue;
                                            ?>

                                            <div style='cursor: hand' class="panel panel-default" onclick="window.location.href='/<?=Settings::getSetting('developer_page') . '/errors/' . ( $key - 1 )?>'">
                                                <div class="panel-heading">
                                                    <h3 class="panel-title"><span class="badge"><?=array_search($value,$error_log, true)?></span> <?=$value['message']?></h3>
                                                </div>
                                                <div class="panel-body">
                                                    <?=$value['details']['file']?> at line <?=$value['details']['line']?>
                                                </div>
                                                <div class="panel-footer">
                                                    <?=$value['ip']?>
                                                </div>
                                            </div>
                                            <?php
                                        }

                                        if( count( $error_log ) > Settings::getSetting('logger_max_errors') )
                                        {

                                            ?>
                                            <p class="text-center">
                                                Some errors were removed from this list due to your max errors setting, currently
                                                it will only display <?=Settings::getSetting('logger_max_errors')?> entrys.
                                            </p>
                                            <?php
                                        }
                                    }
                                ?>
                            </div>
                        <?php
                    }
                ?>
            </div>
            <?php

                Flight::render('developer/templates/template.footer', array( 'breadcrumb' => true ));
            ?>
        </div>
    </body>
</html>
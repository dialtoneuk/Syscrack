<?php

    use Framework\Application\Utilities\Log;
    use Framework\Application\Utilities\PostHelper;

    $error_handler = \Framework\Application\Container::getObject('application')->getErrorHandler();

    Log::log('Page beginning to Render');
?>
<html lang="en">

    <?php

        Flight::render('developer/templates/template.header', array( 'pagetitle' => 'Framework Log'));
    ?>
    <body>
        <div class="container">

            <?php

                Flight::render('developer/templates/template.navigation');

                if( isset( $_GET['error'] ) )
                    Flight::render('developer/templates/template.alert', array( 'message' => $_GET['error'] ) );
                elseif( isset( $_GET['success'] ) )
                    Flight::render('developer/templates/template.alert', array( 'message' => 'Success', 'alert_type' => 'alert-success' ) );
            ?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header">
                        <h1>Framework Log</h1>
                    </div>

                    <p class="lead">
                        The Framework log shows you a log of the errors of which occured on your framework as well as
                        the active log which can be used for debugging.
                    </p>

                    <p>
                        To delete a log, press the delete button under the specific log section. To view more detail about
                        an error, simply click onto the error and you will be taken to a more detailed screen regarding the
                        error.
                    </p>
                </div>
            </div>

            <?php if( \Framework\Application\Settings::getSetting('error_logging') == false ){ ?>

                <div class="row">
                    <div class="col-lg-12">
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
            <?php }else{ ?>

                <div class="row" style="padding-bottom: 2.5%;">
                    <div class="col-lg-6">
                        <div class="page-header">
                            <h1>
                                Error Log
                                <small>
                                    Displaying
                                    <a href="/developer/settingsmanager#panel_logger_max_errors">
                                        <?=\Framework\Application\Settings::getSetting('logger_max_errors')?>
                                    </a>
                                    at once
                                </small>
                            </h1>
                        </div>

                        <?php
                            $error_log = $error_handler->getErrorLog();

                            if( empty( $error_log ) )
                                echo 'No Errors Detected';

                            for( $key = count( $error_log ), $count=1; $key > 0; $key--, $count++)
                            {

                                $value = $error_log[ $key - 1 ];

                                if( $count > \Framework\Application\Settings::getSetting('logger_max_errors') )
                                    continue;
                                ?>

                                    <div style='cursor: hand' class="panel panel-default" onclick="window.location.href='<?=$key - 1?>'">
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

                            if( empty( $error_log ) == false )
                            {
                                ?>
                                    <form method="post">
                                        <button class="btn btn-lg btn-danger btn-block" name="action" value="delete" type="submit">
                                            Delete Log
                                        </button>
                                    </form>
                                <?php
                            }
                        ?>
                    </div>
                    <div class="col-lg-6">
                        <div class="page-header">
                            <h1>Active Log</h1>
                        </div>

                        <?php

                        Log::log('Outputting Log');

                        if( Log::$disabled )
                        {

                            $active_log = Log::readActiveLog();
                        }
                        else
                        {

                            if( \Framework\Application\Settings::getSetting('active_log_showdeveloper') )
                            {

                                $active_log = Log::getActiveLog();
                            }
                        }

                        if( empty( $active_log ) )
                        {
                            ?>
                            <div class="well">
                                Active log is empty
                            </div>
                            <?php
                        }
                        else
                        {
                            ?>
                            <div class="well" style="max-height: 50%;">
                                <pre style="max-height: 45%;">
<?= json_encode( $active_log, JSON_PRETTY_PRINT )?>
                                </pre>
                            </div>
                            <button class="btn btn-lg btn-primary btn-block" onclick="window.location.reload()">
                                Refresh
                            </button>
                            <p class="small" style="padding-top: 2.5%;">
                                Took <?=microtime( true ) - reset( $active_log )['microtime']; ?> seconds to complete from
                                first log.
                            </p>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            <?php } ?>

            <?php



                Flight::render('developer/templates/template.footer', array( 'breadcrumb' => true ));
            ?>
        </div>
    </body>
</html>

<?php
if( $_POST )
    if( PostHelper::checkForRequirements(['action'] ) )
    {

        $action = PostHelper::returnRequirements(['action'])['action'];

        if( $action == 'delete' )
        {

            $error_handler->deleteErrorLog();

            Flight::redirect('/developer/logger?success');

            exit;
        }

        Flight::redirect('/developer/logger?error=Unable to process action');
    }
?>
